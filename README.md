# 🍰 Sweetie Dessert - Integrated Laravel Project
> **Mata Kuliah:** Pemograman Web — Ujian Tengah Semester (UTS)  
> **Aplikasi:** Inventory, POS Cashier & Online Dessert Store Management System  
> **Framework:** Laravel 8 (PHP 8.2 &bull; MySQL &bull; Eloquent ORM &bull; Blade &bull; Bootstrap 5.3)

---

## 1. Project Overview & Skenario Bisnis
**Sweetie Dessert** adalah aplikasi bisnis distribusi dan cafe makanan penutup (*dessert*) terpadu yang memadukan etalase belanja online pelanggan (*customer shop*), kasir cepat offline (*POS Cashier*), serta manajemen inventori & pergudangan (*inventory management*).

### Fitur & Masalah yang Diselesaikan:
* **Etalase Publik & Belanja Customer (Shopee Style):** Pengunjung dapat melihat foto katalog dessert premium, promo banner, kategori, dan detail harga. Pembeli dapat registrasi akun mandiri, memasukkan item ke keranjang belanja, checkout pesanan, dan mencetak nota struk digital.
* **Kasir Kas POS Cepat (Shift Staff):** Kasir/Staff memiliki panel operasional khusus untuk mencatat kas masuk harian, transaksi penjualan instan, dan memantau stok tipis tanpa melihat data laba rahasia pemilik.
* **Konsistensi Stok Real-Time:** Mencegah ketidaksesuaian stok fisik dan pembukuan melalui mutasi stok otomatis saat pembelian dari supplier (*stock-in*) maupun penjualan (*stock-out*).
* **Mencegah Overselling (`lockForUpdate`):** Produk yang stoknya habis atau tidak mencukupi tidak dapat dijual. Sistem mengunci baris produk dalam database transaksi atomik (`DB::transaction`).
* **Pemisahan Hak Akses (Role-Based Access Control):** 
  - **Admin / Owner:** Akses penuh kelola user, master produk, supplier, customer, pembelian (*stock-in*), pembatalan transaksi, serta laporan laba rugi.
  - **Staff Kasir:** Transaksi kasir penjualan offline (*POS*), penerimaan stok, dan riwayat shift harian.
  - **Customer / Pembeli:** Registrasi akun mandiri, belanja dessert, keranjang belanja, dan riwayat pesanan pribadi.
* **Desain Responsif Multi-Device:** Tampilan otomatis menyesuaikan dan nyaman dibuka di Smartphone (HP), iPad/Tablet, maupun Layar Laptop.
* **Siap Online HTTPS (Cloudflare Tunnel):** Dilengkapi skrip 1-klik untuk presentasi live via internet tanpa sewa domain.

---

## 2. Diagram Relasi Data (ERD) & Struktur Database

```text
  ┌──────────────┐          ┌────────────────────┐
  │  categories  │ 1      * │      products      │
  ├──────────────┤──────────├────────────────────┤
  │ id (PK)      │          │ id (PK)            │
  │ name (UQ)    │          │ category_id (FK)   │
  │ description  │          │ sku (UQ)           │
  └──────────────┘          │ name               │
                            │ purchase_price     │
                            │ selling_price      │
                            │ stock              │
                            │ minimum_stock      │
                            │ image              │
                            └─────────┬──────────┘
                                      │ 1
        ┌─────────────────────────────┼─────────────────────────────┐
        │ *                           │                             │ *
 ┌──────┴───────────────┐             │                    ┌────────┴───────────┐
 │    purchase_items    │             │                    │     sale_items     │
 ├──────────────────────┤             │                    ├────────────────────┤
 │ id (PK)              │             │                    │ id (PK)            │
 │ purchase_id (FK)     │             │                    │ sale_id (FK)       │
 │ product_id (FK)      │             │                    │ product_id (FK)    │
 │ quantity             │             │                    │ quantity           │
 │ unit_cost            │             │                    │ unit_price         │
 │ subtotal             │             │                    │ subtotal           │
 └──────────┬───────────┘             │                    └─────────┬──────────┘
            │ *                       │                              │ *
 ┌──────────┴───────────┐             │                    ┌─────────┴──────────┐
 │      purchases       │             │                    │       sales        │
 ├──────────────────────┤             │                    ├────────────────────┤
 │ id (PK)              │             │                    │ id (PK)            │
 │ supplier_id (FK)     │             │                    │ customer_id (FK)   │
 │ user_id (FK)         │             │                    │ user_id (FK)       │
 │ purchase_number (UQ) │             │                    │ sale_number (UQ)   │
 │ total                │             │                    │ total              │
 │ status               │             │                    │ status             │
 │ purchased_at         │             │                    │ sold_at            │
 └──────────┬───────────┘             │                    └─────────┬──────────┘
            │                         │                              │
            │ *                       │                              │ *
   ┌────────┴────────┐                │                     ┌────────┴──────────┐
   │    suppliers    │                │                     │     customers     │
   ├─────────────────┤                │                     ├───────────────────┤
   │ id (PK)         │                │                     │ id (PK)           │
   │ name            │                │                     │ name              │
   │ phone, email    │                │                     │ phone, email      │
   │ address         │                │                     │ address           │
   └─────────────────┘                │                     └───────────────────┘
                                      │ *
                             ┌────────┴─────────┐
                             │      users       │
                             ├──────────────────┤
                             │ id (PK)          │
                             │ username (UQ)    │
                             │ name             │
                             │ email (UQ)       │
                             │ password         │
                             │ role             │
                             └──────────────────┘
```

### Penjelasan Relasi Eloquent:
1. **Category `hasMany` Product / Product `belongsTo` Category:** Satu kategori menaungi banyak produk dessert. Foreign key `category_id` berada pada tabel `products`.
2. **Supplier `hasMany` Purchase / Purchase `belongsTo` Supplier:** Satu vendor supplier dapat memasok banyak nota faktur pembelian bahan/produk.
3. **Purchase `hasMany` PurchaseItem / PurchaseItem `belongsTo` Purchase:** Satu transaksi pembelian memuat rincian item produk yang dibeli.
4. **Customer `hasMany` Sale / Sale `belongsTo` Customer:** Satu pelanggan menaungi banyak riwayat transaksi pembelian.
5. **Sale `hasMany` SaleItem / SaleItem `belongsTo` Sale:** Header transaksi penjualan menaungi rincian item dessert yang terjual.
6. **Product `hasMany` SaleItem & PurchaseItem:** Satu produk dessert tercatat pada banyak detail penjualan dan pembelian.
7. **Pencegahan N+1 Query:** Daftar produk dimuat menggunakan eager loading `Product::with('category')->paginate(12)`.

---

## 3. Business Logic & Konsistensi Stok

* **Perhitungan Server-Side:** Total harga transaksi, diskon, dan subtotal selalu dihitung oleh server backend Laravel, tidak pernah mempercayai input kiriman browser pengguna.
* **Atomisitas Transaksi (`DB::transaction`):** Header nota, item detail, dan mutasi saldo stok tersimpan secara utuh. Jika salah satu baris gagal disimpan, seluruh operasi otomatis di-*rollback*.
* **Pencegahan Concurrency / Overselling (`lockForUpdate`):** Saat kasir atau pelanggan memproses checkout, baris data produk dikunci sementara agar stok tidak terjual ganda pada saat bersamaan.
* **Aturan Pembatalan Transaksi Konsisten:**
  * **Batal Penjualan:** Stok dessert dikembalikan / ditambah kembali (`increment`).
  * **Batal Pembelian:** Stok dessert dikurangkan kembali (`decrement`), dilengkapi validasi aman agar tidak menjadi minus.
* **Integritas Master Data:** Produk yang sudah memiliki riwayat transaksi dilindungi dari *hard delete* (menggunakan *soft delete*). Kategori/Supplier/Customer yang memiliki relasi dilindungi dari penghapusan sembarangan.

---

## 4. Akun Demo Pengujian

| Role | Username / Email | Password | Halaman Masuk Utama | Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Admin / Owner** | `admin` / `admin@dessert.test` | `password` | `/dashboard` | Akses Penuh: Kelola User, CRUD Master Data, Batalkan Transaksi, Laporan Laba Rugi |
| **Staff Kasir** | `staff` / `staff@dessert.test` | `password` | `/dashboard` | Kasir POS Cepat, Pembelian Stok Masuk, Shift Kasir Harian |
| **Customer** | `bella` / `bella@gmail.com` | `password` | `/shop` | Belanja Katalog, Keranjang Belanja, Checkout, Riwayat Pesanan Saya |

> **Catatan Pengunjung Baru (Guest):** Pengunjung belum login dapat langsung melihat etalase `/shop`. Jika menekan tombol keranjang atau checkout, sistem ramah mengarahkan ke halaman registrasi/login member.

---

## 5. Panduan Instalasi & Menjalankan Aplikasi

### Prasyarat:
* PHP >= 8.0 (disertai ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`)
* Web Server & Database: XAMPP (Apache & MySQL)
* Composer

### Langkah-langkah:
1. **Clone repository ini:**
   ```bash
   git clone https://github.com/username-anda/UTS_dessert.git
   cd UTS_dessert
   ```
2. **Install dependensi PHP:**
   ```bash
   composer install
   ```
3. **Konfigurasi Environment (`.env`):**
   Salin `.env.example` menjadi `.env` dan pastikan konfigurasi database sesuai:
   ```env
   APP_NAME="Sweetie Dessert"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost/UTS_dessert/public

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=uts_dessert_store
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
5. **Siapkan Database (Pilih salah satu cara):**
   * **Cara A (Rekomendasi via SQL Dump Siap Pakai):**
     Buat database kosong bernama `uts_dessert_store` di phpMyAdmin, lalu import file `uts_dessert_store.sql` yang sudah tersedia di folder utama proyek.
   * **Cara B (Via Migration & Seeder Laravel):**
     ```bash
     php artisan migrate:fresh --seed
     ```
6. **Akses Aplikasi Melalui Browser:**
   * Melalui server Apache XAMPP:  
     👉 **`http://localhost/UTS_dessert/public/`**
   * Atau via Laravel Artisan Serve:  
     ```bash
     php artisan serve
     ```  
     👉 Akses di **`http://127.0.0.1:8000`**

---

## 6. Fitur Peluncur Online 1-Klik (Cloudflare HTTPS)

Proyek ini telah dilengkapi dengan skrip peluncur instan:
* Cukup jalankan file **`Jalankan_Web_Online.bat`**
* Secara otomatis menghubungkan aplikasi lokal ke internet melalui Cloudflare Tunnel dengan sertifikat SSL/HTTPS gratis dan aman.
* Tautan online dapat langsung dibagikan dan dibuka melalui Smartphone (HP) maupun tablet saat demonstrasi presentasi.

---

## 7. Persiapan Wawancara Pemilik Kode (Code Interview)

1. **Mengapa Sale memiliki relasi `hasMany` ke SaleItem?**  
   Karena satu transaksi penjualan di kasir dapat memuat lebih dari satu jenis kue/dessert dalam satu struk nota.
2. **Di mana sistem memastikan stok tidak menjadi negatif?**  
   Pada [SaleController.php](app/Http/Controllers/SaleController.php) di method `store()`, sistem mengecek `if ($qty < 1 || $product->stock < $qty)` sebelum pengurangan stok dilakukan.
3. **Mengapa total penjualan dihitung di server?**  
   Untuk integritas keamanan. Jika total dihitung di browser, pihak tak bertanggung jawab dapat memanipulasi nilai harga via inspect element sebelum request dikirim.
4. **Apa risiko jika pengurangan stok di luar `DB::transaction`?**  
   Jika penyimpanan tabel detail gagal di tengah jalan, stok sudah terlanjur berkurang atau sebaliknya (terjadi data korup/inkonsisten).
5. **Bagaimana mencegah Staff mengakses fungsi Admin melalui URL langsung?**  
   Menggunakan [RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) yang memverifikasi `$user->role === 'admin'`. Jika staf membuka URL khusus admin, sistem membalas dengan status `HTTP 403 Forbidden`.
6. **Apa peran `lockForUpdate()`?**  
   Menerapkan *pessimistic locking* pada baris database MySQL sehingga jika ada 2 transaksi bersamaan pada produk yang sama, transaksi kedua harus mengantre hingga transaksi pertama selesai, mencegah *overselling*.
