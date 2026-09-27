# 🍰 Sweetie Dessert - Aplikasi Manajemen Produk

Aplikasi web berbasis PHP (PDO) dan MySQL yang dirancang untuk mengelola inventori produk makanan penutup (*dessert*) dengan antarmuka yang modern, responsif, dan bernuansa pastel manis.

---

## 🖥️ Tampilan Antarmuka (Screenshots & Features)

Aplikasi ini memiliki beberapa halaman utama untuk mempermudah navigasi pengguna dan pengelolaan data:

1. **Tampilan Home / Beranda (`index.php`)**
   * Halaman sambutan yang dilengkapi dengan banner promosi interaktif dan navigasi menu utama (Home, Product, serta tombol pintasan Tambah Produk).
   
2. **Tampilan Daftar Produk (`index.php` bagian bawah)**
   * Menampilkan seluruh katalog produk dalam bentuk kartu (*card*) yang elegan. Setiap kartu memuat foto produk, kategori, nama produk, harga (dalam format Rupiah), informasi jumlah stok yang tersedia, serta tombol aksi **Edit** dan **Hapus**.

3. **Tampilan Tambah Produk (`create.php`)**
   * Formulir khusus untuk memasukkan produk baru ke dalam database. Dilengkapi dengan validasi ketat meliputi:
     * Nama produk wajib diisi dan minimal 3 karakter (serta pengecekan keunikan nama).
     * Harga wajib berupa angka positif di atas 0 (`> 0`).
     * Stok barang minimal bernilai 0 (`>= 0`).
     * Unggah foto produk berformat JPG, PNG, atau WEBP.

4. **Tampilan Edit Produk (`edit.php`)**
   * Formulir untuk memperbarui informasi produk yang sudah ada (Nama, Kategori, Harga, dan Stok) berdasarkan ID produk yang dipilih.

5. **Fitur Konfirmasi Hapus Data**
   * Menggunakan konfirmasi dialog JavaScript interaktif (`confirm`) sebelum data produk benar-benar dihapus dari database untuk mencegah kesalahan penghapusan data.

---

## 🛠️ Teknologi yang Digunakan
* **Backend:** PHP (Native dengan pendekatan PDO / PHP Data Objects)
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, Bootstrap 5, Google Fonts (Poppins)
* **Server Lokal:** XAMPP

---

## 📥 Cara Menjalankan Aplikasi

1. Pindahkan folder proyek ke direktori `htdocs` pada server lokal Anda (Contoh: `C:\xampp\htdocs\projek3`).
2. Nyalakan layanan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Buat database baru di `phpMyAdmin` dan import file database proyek Anda.
4. Sesuaikan konfigurasi koneksi database pada file `config.php` jika diperlukan.
5. Buka browser dan akses melalui alamat:
   ```text
   http://localhost/projek3/index.php