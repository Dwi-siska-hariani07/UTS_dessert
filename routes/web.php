<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerShopController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sweetie Dessert Inventory & Sales Management
|--------------------------------------------------------------------------
*/

// Public & Guest Routes
Route::get('/', function () {
    return redirect()->route('shop');
});

// Etalase & Promo Belanja Customer (Bisa dilihat publik)
Route::get('/shop', [CustomerShopController::class, 'index'])->name('shop');
Route::post('/cart/add/{product}', [CustomerShopController::class, 'addToCart'])->name('shop.cart.add');
Route::get('/cart', [CustomerShopController::class, 'cart'])->name('shop.cart');
Route::post('/cart/update', [CustomerShopController::class, 'updateCart'])->name('shop.cart.update');
Route::post('/cart/remove/{product}', [CustomerShopController::class, 'removeFromCart'])->name('shop.cart.remove');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Registrasi Akun Customer / Pembeli Baru
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Semua Akun Login: Admin, Staff, Customer)
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return auth()->user()->isCustomer() 
            ? redirect()->route('shop') 
            : redirect()->route('dashboard');
    })->name('home');

    // Customer Checkout & Pesanan Saya
    Route::post('/cart/checkout', [CustomerShopController::class, 'checkoutCart'])->name('shop.cart.checkout');
    Route::get('/shop/order/{product}', [CustomerShopController::class, 'orderForm'])->name('shop.order');
    Route::post('/shop/order/{product}', [CustomerShopController::class, 'placeOrder'])->name('shop.order.post');
    Route::get('/my-orders', [CustomerShopController::class, 'myOrders'])->name('shop.my-orders');

    // Backoffice: Dashboard & Master Data (Khusus Admin & Staff)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Products (Semua melihat katalog stok; create/edit hanya Admin)
        Route::resource('products', ProductController::class);

        // Customers Management (Kasir & Admin mendata member pelanggan)
        Route::resource('customers', CustomerController::class);

        // Purchases (Stock-In)
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

        // Sales (Kasir Offline)
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });

    // Rincian & Cetak Struk Penjualan
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');

    // Khusus Admin (Kelola Pengguna, Kategori, Supplier, & Pembatalan Transaksi)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    });
});
