<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sweetie Dessert') - Sweet & Delicious</title>

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sweet-pink-bg: #fff0f3;
            --sweet-pink-header: #ffccd5;
            --sweet-pink-accent: #ff758f;
            --sweet-pink-dark: #c2185b;
            --sweet-pink-vibrant: #e91e63;
            --sweet-card-bg: #ffffff;
            --sweet-border: #f8bbd0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--sweet-pink-bg);
            color: #374151;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styling */
        .navbar-sweet {
            background-color: var(--sweet-pink-header);
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.08);
            padding: 12px 24px;
        }

        .navbar-logo {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--sweet-pink-dark);
            background: #fff;
        }

        .navbar-brand-text {
            font-family: 'Great Vibes', cursive;
            color: var(--sweet-pink-dark) !important;
            font-size: 2.2rem;
            letter-spacing: 1px;
            font-weight: bold;
            line-height: 1;
        }

        .nav-link-sweet {
            font-weight: 500;
            color: #4b5563 !important;
            padding: 8px 14px !important;
            border-radius: 20px;
            transition: all 0.2s ease;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-sweet:hover, .nav-link-sweet.active {
            background-color: #ffffff;
            color: var(--sweet-pink-dark) !important;
            box-shadow: 0 2px 8px rgba(194, 24, 91, 0.12);
        }

        /* Sweet Card & Buttons */
        .card-sweet {
            background: var(--sweet-card-bg);
            border-radius: 20px;
            border: 1px solid var(--sweet-border);
            box-shadow: 0 8px 24px rgba(255, 117, 143, 0.08);
        }

        .btn-sweet-primary {
            background: linear-gradient(135deg, #ff758f 0%, #c2185b 100%);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .btn-sweet-primary:hover {
            background: linear-gradient(135deg, #ff4d6d 0%, #a01048 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(194, 24, 91, 0.25);
        }

        .btn-sweet-outline {
            background: transparent;
            color: var(--sweet-pink-dark);
            border: 1.5px solid var(--sweet-pink-dark);
            border-radius: 25px;
            padding: 7px 18px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-sweet-outline:hover {
            background: var(--sweet-pink-dark);
            color: #fff;
        }

        .badge-role-admin {
            background-color: #e91e63;
            color: #fff;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .badge-role-staff {
            background-color: #0284c7;
            color: #fff;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .badge-role-customer {
            background-color: #10b981;
            color: #fff;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .table-sweet thead {
            background-color: #ffe4e9;
            color: #880e4f;
        }

        .table-sweet th {
            font-weight: 600;
            font-size: 0.88rem;
            border-bottom: 2px solid var(--sweet-border);
        }

        .footer-sweet {
            margin-top: auto;
            background-color: #ffe4e9;
            border-top: 1px solid var(--sweet-border);
            padding: 16px 0;
            font-size: 0.85rem;
            color: #880e4f;
        }

        /* Shopee-style Shopping Cart Icon Button & Badge */
        .cart-shopee-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #ffffff;
            color: var(--sweet-pink-dark) !important;
            border: 1.5px solid var(--sweet-border);
            box-shadow: 0 2px 8px rgba(194, 24, 91, 0.12);
            transition: all 0.25s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .cart-shopee-btn:hover, .cart-shopee-btn.active {
            background-color: #ffe4e9;
            color: var(--sweet-pink-vibrant) !important;
            border-color: var(--sweet-pink-vibrant);
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(194, 24, 91, 0.25);
        }

        .cart-shopee-badge {
            position: absolute;
            top: -5px;
            right: -6px;
            background: linear-gradient(135deg, #ff1744, #e91e63);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 10px;
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            line-height: 1;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sweet Navbar -->
    <nav class="navbar navbar-expand-lg navbar-sweet sticky-top">
        <div class="container-fluid px-lg-4">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ Auth::check() && Auth::user()->isCustomer() ? route('shop') : (Auth::check() ? route('dashboard') : route('shop')) }}" title="Kembali ke Dashboard / Beranda">
                <img src="{{ asset('image/logo.png') }}" alt="Logo" class="navbar-logo" onerror="this.src='https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=100&q=80'">
                <span class="navbar-brand-text">Sweetie Dessert</span>
            </a>

            <!-- Toggle Mobile -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navSweetMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Navigation -->
            <div class="collapse navbar-collapse" id="navSweetMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    @auth
                        @if(Auth::user()->isCustomer())
                            {{-- Customer tidak butuh menu teks panjang agar navbar bersih --}}
                        @else
                            {{-- Menu Backoffice Admin & Staff (Dikelompokkan Profesional) --}}
                            <!-- Dropdown Master Data -->
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-link-sweet dropdown-toggle {{ request()->routeIs('products.*', 'categories.*', 'suppliers.*', 'customers.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-box-seam-fill"></i> Master Data
                                </a>
                                <ul class="dropdown-menu shadow-sm rounded-4 border-0 p-2">
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 {{ request()->routeIs('products.*') ? 'active text-white bg-danger' : '' }}" href="{{ route('products.index') }}">
                                            <i class="bi bi-cake2-fill text-danger"></i> Stok & Katalog Produk (Gudang)
                                        </a>
                                    </li>
                                    @if(Auth::user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 {{ request()->routeIs('categories.*') ? 'active text-white bg-danger' : '' }}" href="{{ route('categories.index') }}">
                                            <i class="bi bi-tags-fill text-secondary"></i> Kategori Menu
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 {{ request()->routeIs('suppliers.*') ? 'active text-white bg-danger' : '' }}" href="{{ route('suppliers.index') }}">
                                            <i class="bi bi-truck text-primary"></i> Mitra Pemasok (Supplier)
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 {{ request()->routeIs('customers.*') ? 'active text-white bg-danger' : '' }}" href="{{ route('customers.index') }}">
                                            <i class="bi bi-heart-fill text-danger"></i> Data Pelanggan (Member)
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Dropdown Transaksi -->
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-link-sweet dropdown-toggle {{ request()->routeIs('sales.*', 'purchases.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-arrow-left-right"></i> Transaksi
                                </a>
                                <ul class="dropdown-menu shadow-sm rounded-4 border-0 p-2">
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 {{ request()->routeIs('sales.*') ? 'active text-white bg-danger' : '' }}" href="{{ route('sales.index') }}">
                                            <i class="bi bi-cart-check-fill text-success"></i> Kasir Penjualan (Sales)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 {{ request()->routeIs('purchases.*') ? 'active text-white bg-danger' : '' }}" href="{{ route('purchases.index') }}">
                                            <i class="bi bi-box-arrow-in-down text-warning-emphasis"></i> Pembelian Stok (Restock Gudang)
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link nav-link-sweet {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="bi bi-people-fill text-info"></i> User
                                </a>
                            </li>
                            @endif

                            {{-- Shortcut ke Toko Publik (Preview Etalase Standard Shopify/WooCommerce) --}}
                            <li class="nav-item ms-lg-2">
                                <a class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 mt-1" href="{{ route('shop') }}" target="_blank" title="Tinjau Tampilan Etalase Toko (Buka di Tab Baru)">
                                    <i class="bi bi-eye"></i> Lihat Toko ↗
                                </a>
                            </li>
                        @endif
                    @else
                        {{-- Menu Tamu (Guest) --}}
                    @endauth
                </ul>

                <!-- User Profile & Action & Cart -->
                <div class="d-flex align-items-center gap-3">
                    {{-- Icon Keranjang ala Shopee (Khusus Customer & Tamu) --}}
                    @if(!Auth::check() || Auth::user()->isCustomer())
                        @php $cartQty = array_sum(array_column(session('cart', []), 'quantity')); @endphp
                        <a class="cart-shopee-btn {{ request()->routeIs('shop.cart') ? 'active' : '' }}" href="{{ route('shop.cart') }}" title="Keranjang Belanja">
                            <i class="bi bi-cart3 fs-5"></i>
                            @if($cartQty > 0)
                                <span class="cart-shopee-badge">{{ $cartQty }}</span>
                            @endif
                        </a>
                    @endif
                    @auth
                        <!-- Dropdown Profil Customer / User -->
                        <div class="dropdown">
                            <button class="btn btn-sm bg-white border rounded-pill px-3 py-1 d-flex align-items-center gap-2 shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--sweet-border) !important;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 28px; height: 28px; background: linear-gradient(135deg, #ff758f, #c2185b); font-size: 0.8rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="text-start d-none d-sm-block">
                                    <div class="fw-semibold text-dark lh-1" style="font-size: 0.84rem;">{{ Auth::user()->name }}</div>
                                    <span class="{{ Auth::user()->isAdmin() ? 'badge-role-admin' : (Auth::user()->isStaff() ? 'badge-role-staff' : 'badge-role-customer') }}" style="font-size: 0.65rem; padding: 1px 6px;">
                                        {{ strtoupper(Auth::user()->role) }}
                                    </span>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow rounded-4 border-0 p-2 mt-2" style="min-width: 220px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                                </li>

                                @if(Auth::user()->isCustomer())
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('shop.my-orders') }}">
                                            <i class="bi bi-receipt text-danger fs-6"></i> Pesanan & Struk Saya
                                        </a>
                                    </li>
                                @elseif(Auth::user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                                            <i class="bi bi-grid-fill text-danger fs-6"></i> Dashboard Bisnis (Admin)
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                                            <i class="bi bi-grid-fill text-primary fs-6"></i> Dashboard Kasir (Staff)
                                        </a>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider my-1"></li>

                                <li>
                                    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Keluar dari akun Anda?');">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 small text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right fs-6"></i> Keluar (Logout)
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-sweet-outline px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-sweet-primary px-3">
                            <i class="bi bi-heart-fill me-1"></i> Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container-fluid px-lg-4 py-4 flex-grow-1">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {!! session('info') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <div class="fw-semibold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Mohon perbaiki beberapa kesalahan berikut:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Cafe Sweetie Dessert -->
    <footer class="footer-sweet py-4 mt-auto">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-5 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <img src="{{ asset('image/logo.png') }}" alt="Logo" style="height: 38px;" onerror="this.src='https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=100&q=80'">
                        <span class="navbar-brand-text fs-3">Sweetie Dessert</span>
                    </div>
                    <p class="text-muted small mb-0">
                        Homemade & Premium Dessert Cafe. Menyajikan aneka cake, pudding lembut, croissant, dan cookies istimewa segar setiap hari.
                    </p>
                </div>
                <div class="col-md-4 text-center text-md-start">
                    <h6 class="fw-bold text-dark mb-2">📍 Info Cafe & Jam Buka</h6>
                    <div class="small text-muted mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Jl. Manis No. 07 Surabaya</div>
                    <div class="small text-muted mb-1"><i class="bi bi-clock-fill text-danger me-1"></i> Setiap Hari: 08.00 - 21.00 WIB</div>
                    <div class="small text-muted"><i class="bi bi-whatsapp text-success me-1"></i> WA Order: 0812-3456-7890</div>
                </div>
                <div class="col-md-3 text-center text-md-end">
                    <h6 class="fw-bold text-dark mb-2">✨ Ikuti Media Sosial</h6>
                    <div class="small text-muted mb-2">
                        <div><i class="bi bi-instagram text-danger me-1"></i> @sweetie.dessert</div>
                        <div><i class="bi bi-tiktok text-dark me-1"></i> @sweetiedessertcafe</div>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 small fw-semibold">
                        Dine-in &bull; Takeaway &bull; Delivery
                    </span>
                </div>
            </div>
            <div class="text-center pt-3 mt-3 border-top small text-muted">
                &copy; {{ date('Y') }} <strong>Sweetie Dessert</strong>. All Rights Reserved. Dibuat dengan penuh cinta & kelezatan manis. ❤️
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
