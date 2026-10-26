<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Akun Pengguna Demo
        $admin = User::create([
            'username' => 'admin',
            'name' => 'Owner / Admin Sweetie',
            'email' => 'admin@dessert.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $staff = User::create([
            'username' => 'staff',
            'name' => 'Staff Kasir & Inventori',
            'email' => 'staff@dessert.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // 2. Kategori Dessert
        $catCakes = Category::create(['name' => 'Cake & Tart', 'description' => 'Aneka kue tart dan bolu slice lembut']);
        $catTiramisu = Category::create(['name' => 'Tiramisu & Pudding', 'description' => 'Dessert dingin manis dan menyegarkan']);
        $catCheesecake = Category::create(['name' => 'Cheesecake', 'description' => 'Olahan keju lembut bertekstur creamy']);
        $catPastry = Category::create(['name' => 'Pastry & Cookies', 'description' => 'Kue kering, croissant, dan cookies hangat']);
        $catDessertBox = Category::create(['name' => 'Dessert Box & Cupcake', 'description' => 'Dessert box kekinian dan aneka cupcake manis']);

        // 3. Pemasok (Suppliers Mitra Dessert)
        $sup1 = Supplier::create([
            'name' => 'Dapur Cake & Tart Artisan',
            'phone' => '081122334455',
            'email' => 'artisan.cake@supplier.test',
            'address' => 'Jl. Dharmahusada Indah No. 12, Surabaya',
        ]);

        $sup2 = Supplier::create([
            'name' => 'Pastry & Croissant Fresh Bakery',
            'phone' => '082233445566',
            'email' => 'pastry.fresh@supplier.test',
            'address' => 'Kawasan Kuliner Rungkut, Surabaya',
        ]);

        $sup3 = Supplier::create([
            'name' => 'Sweet Pudding & Dessert Box House',
            'phone' => '083344556677',
            'email' => 'dessertbox@supplier.test',
            'address' => 'Jl. Mawar No. 45, Sidoarjo',
        ]);

        // 4. Pelanggan (Customers) & Akun Customer
        $userBella = User::create([
            'username' => 'bella',
            'name' => 'Bella Anastasya',
            'email' => 'bella@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $cust1 = Customer::create([
            'user_id' => $userBella->id,
            'name' => 'Bella Anastasya',
            'phone' => '081234567891',
            'email' => 'bella@gmail.com',
            'address' => 'Jl. Dharmawangsa No. 20, Surabaya',
        ]);

        $userDimas = User::create([
            'username' => 'dimas',
            'name' => 'Dimas Pratama',
            'email' => 'dimas@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $cust2 = Customer::create([
            'user_id' => $userDimas->id,
            'name' => 'Dimas Pratama',
            'phone' => '085678912345',
            'email' => 'dimas@gmail.com',
            'address' => 'Jl. Ketintang Baru No. 15, Surabaya',
        ]);

        $cust3 = Customer::create([
            'name' => 'Rania Salsabila',
            'phone' => '087811223344',
            'email' => 'rania.s@gmail.com',
            'address' => 'Jl. Manyar Kertoarjo No. 8, Surabaya',
        ]);

        $custWalkIn = Customer::create([
            'name' => 'Pelanggan Umum (Walk-in)',
            'phone' => '080000000000',
            'email' => 'walkin@sweetie.test',
            'address' => 'Pembelian Langsung di Toko',
        ]);

        // 5. Produk Dessert (20 Produk Sesuai File Gambar)
        $productsData = [
            ['cat' => $catCakes, 'sku' => 'DS-CK-001', 'name' => 'Strawberry Shortcake', 'p_price' => 30000, 's_price' => 45000, 'stock' => 15, 'min' => 5, 'img' => 'produk1.jpg'],
            ['cat' => $catCakes, 'sku' => 'DS-CK-002', 'name' => 'Chocolate Lava Cake', 'p_price' => 26000, 's_price' => 40000, 'stock' => 12, 'min' => 4, 'img' => 'produk2.jpg'],
            ['cat' => $catCakes, 'sku' => 'DS-CK-003', 'name' => 'Red Velvet Slice', 'p_price' => 25000, 's_price' => 38000, 'stock' => 10, 'min' => 4, 'img' => 'produk3.jpg'],
            ['cat' => $catTiramisu, 'sku' => 'DS-TM-004', 'name' => 'Matcha Green Tea Tiramisu', 'p_price' => 28000, 's_price' => 42000, 'stock' => 8, 'min' => 3, 'img' => 'produk4.jpg'],
            ['cat' => $catCheesecake, 'sku' => 'DS-CS-005', 'name' => 'Blueberry Cheesecake', 'p_price' => 32000, 's_price' => 48000, 'stock' => 14, 'min' => 5, 'img' => 'produk5.jpg'],
            ['cat' => $catDessertBox, 'sku' => 'DS-CP-006', 'name' => 'Vanilla Cupcake with Sprinkles', 'p_price' => 14000, 's_price' => 22000, 'stock' => 25, 'min' => 8, 'img' => 'produk6.jpg'],
            ['cat' => $catPastry, 'sku' => 'DS-PS-007', 'name' => 'Belgian Chocolate Tart', 'p_price' => 22000, 's_price' => 35000, 'stock' => 18, 'min' => 5, 'img' => 'produk7.jpg'],
            ['cat' => $catTiramisu, 'sku' => 'DS-PD-008', 'name' => 'Mango Pannacotta', 'p_price' => 18000, 's_price' => 28000, 'stock' => 20, 'min' => 6, 'img' => 'produk8.jpg'],
            ['cat' => $catDessertBox, 'sku' => 'DS-DB-009', 'name' => 'Lotus Biscoff Dessert Box', 'p_price' => 33000, 's_price' => 50000, 'stock' => 12, 'min' => 4, 'img' => 'produk9.jpg'],
            ['cat' => $catPastry, 'sku' => 'DS-MC-010', 'name' => 'Rainbow Macarons (Box of 5)', 'p_price' => 36000, 's_price' => 55000, 'stock' => 16, 'min' => 5, 'img' => 'produk10.jpg'],
            ['cat' => $catTiramisu, 'sku' => 'DS-PD-011', 'name' => 'Caramel Cream Pudding', 'p_price' => 16000, 's_price' => 25000, 'stock' => 22, 'min' => 6, 'img' => 'produk11.jpg'],
            ['cat' => $catTiramisu, 'sku' => 'DS-TM-012', 'name' => 'Tiramisu Klasik Italia', 'p_price' => 30000, 's_price' => 45000, 'stock' => 9, 'min' => 3, 'img' => 'produk12.jpg'],
            ['cat' => $catPastry, 'sku' => 'DS-PS-013', 'name' => 'Choco Hazelnut Croissant', 'p_price' => 20000, 's_price' => 32000, 'stock' => 15, 'min' => 5, 'img' => 'produk13.jpg'],
            ['cat' => $catCheesecake, 'sku' => 'DS-CS-014', 'name' => 'Oreo Cheesecake Cup', 'p_price' => 19000, 's_price' => 30000, 'stock' => 17, 'min' => 5, 'img' => 'produk14.jpg'],
            ['cat' => $catPastry, 'sku' => 'DS-CK-015', 'name' => 'Matcha Soft Cookies', 'p_price' => 12000, 's_price' => 20000, 'stock' => 30, 'min' => 8, 'img' => 'produk15.jpg'],
            ['cat' => $catPastry, 'sku' => 'DS-CK-016', 'name' => 'Choco Chip Soft Cookies', 'p_price' => 11000, 's_price' => 18000, 'stock' => 35, 'min' => 10, 'img' => 'produk16.jpg'],
            ['cat' => $catCakes, 'sku' => 'DS-CR-017', 'name' => 'Berry Cream Mille Crepe', 'p_price' => 30000, 's_price' => 46000, 'stock' => 11, 'min' => 4, 'img' => 'produk17.jpg'],
            ['cat' => $catCakes, 'sku' => 'DS-PC-018', 'name' => 'Japanese Souffle Pancake', 'p_price' => 24000, 's_price' => 38000, 'stock' => 10, 'min' => 4, 'img' => 'produk18.jpg'],
            ['cat' => $catPastry, 'sku' => 'DS-BW-019', 'name' => 'Dark Chocolate Brownies', 'p_price' => 22000, 's_price' => 35000, 'stock' => 20, 'min' => 5, 'img' => 'produk19.jpg'],
            ['cat' => $catDessertBox, 'sku' => 'DS-DB-020', 'name' => 'Signature Sweetie Dessert Box', 'p_price' => 38000, 's_price' => 60000, 'stock' => 15, 'min' => 5, 'img' => 'produk20.jpg'],
        ];

        $createdProducts = [];
        foreach ($productsData as $p) {
            $createdProducts[] = Product::create([
                'category_id' => $p['cat']->id,
                'sku' => $p['sku'],
                'name' => $p['name'],
                'purchase_price' => $p['p_price'],
                'selling_price' => $p['s_price'],
                'stock' => $p['stock'],
                'minimum_stock' => $p['min'],
                'image' => $p['img'],
            ]);
        }

        // 6. Transaksi Pembelian Sampel (Stock-in)
        $purchase = Purchase::create([
            'supplier_id' => $sup1->id,
            'user_id' => $staff->id,
            'purchase_number' => 'PO-' . date('Ymd') . '-0001',
            'total' => 300000,
            'status' => 'completed',
            'purchased_at' => now()->subDays(2),
            'notes' => 'Restok Strawberry Shortcake untuk akhir pekan',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $createdProducts[0]->id,
            'quantity' => 10,
            'unit_cost' => 30000,
            'subtotal' => 300000,
        ]);

        // 7. Transaksi Penjualan Sampel (Stock-out)
        $sale1 = Sale::create([
            'customer_id' => $cust1->id,
            'user_id' => $staff->id,
            'sale_number' => 'INV-' . date('Ymd') . '-0001',
            'total' => 140000,
            'status' => 'completed',
            'sold_at' => now()->subDay(),
            'notes' => 'Pesanan kue ulang tahun Bella',
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $createdProducts[0]->id, // Strawberry Shortcake
            'quantity' => 2,
            'unit_price' => 45000,
            'subtotal' => 90000,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $createdProducts[8]->id, // Lotus Biscoff
            'quantity' => 1,
            'unit_price' => 50000,
            'subtotal' => 50000,
        ]);

        $sale2 = Sale::create([
            'customer_id' => $cust2->id,
            'user_id' => $staff->id,
            'sale_number' => 'INV-' . date('Ymd') . '-0002',
            'total' => 88000,
            'status' => 'completed',
            'sold_at' => now(),
            'notes' => 'Takeaway santai Dimas',
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $createdProducts[5]->id, // Vanilla Cupcake
            'quantity' => 4,
            'unit_price' => 22000,
            'subtotal' => 88000,
        ]);
    }
}
