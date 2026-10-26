@extends('layouts.app')

@section('title', 'Toko & Promo Dessert')

@section('content')
<!-- Hero Carousel Banner & Promo (Persis Projek 3) -->
<div class="card card-sweet overflow-hidden border-0 mb-4 shadow-sm" style="border-radius: 20px;">
    <div id="shopBannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#shopBannerCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#shopBannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#shopBannerCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner" style="border-radius: 20px;">
            <div class="carousel-item active">
                <img src="{{ asset('image/1.png') }}" class="d-block w-100" style="height: auto; object-fit: contain;" alt="Promo 1" onerror="this.src='https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=1200&q=80'">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('image/2.png') }}" class="d-block w-100" style="height: auto; object-fit: contain;" alt="Promo 2" onerror="this.src='https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=1200&q=80'">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('image/3.png') }}" class="d-block w-100" style="height: auto; object-fit: contain;" alt="Promo 3" onerror="this.src='https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=1200&q=80'">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#shopBannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#shopBannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<!-- Promo Special Banner Strip -->
<div class="alert alert-danger rounded-4 border-0 p-3 mb-4 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
    <div class="d-flex align-items-center gap-3">
        <span class="fs-1">🎁</span>
        <div>
            <h5 class="fw-bold text-danger mb-0">PROMO MANIS MINGGU INI!</h5>
            <small class="text-dark">Pesan dessert favoritmu sekarang dan nikmati kelezatan premium homemade setiap hari.</small>
        </div>
    </div>
    @guest
        <a href="{{ route('register') }}" class="btn btn-sm btn-sweet-primary px-4">
            <i class="bi bi-gift-fill me-1"></i> Daftar Akun & Dapatkan Promo
        </a>
    @endguest
</div>

<!-- Search & Category Filters -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('shop') }}" class="row g-2 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Mau pesan dessert apa hari ini? Cari di sini..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">-- Semua Kategori Menu --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Cari</button>
            @if($search || $categoryId)
                <a href="{{ route('shop') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Kategori Cepat (Pill Tabs Horizontal ala GrabFood / ShopeeFood) -->
<div class="d-flex align-items-center gap-2 overflow-auto pb-2 mb-3" style="white-space: nowrap; -webkit-overflow-scrolling: touch;">
    <a href="{{ route('shop', array_filter(['search' => $search])) }}" 
       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ empty($categoryId) ? 'btn-sweet-primary shadow-sm' : 'bg-white text-dark border' }}">
        ✨ Semua Menu
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('shop', array_filter(['category_id' => $cat->id, 'search' => $search])) }}" 
       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ $categoryId == $cat->id ? 'btn-sweet-primary shadow-sm' : 'bg-white text-dark border' }}">
        @if(str_contains(strtolower($cat->name), 'cake')) 🍰 
        @elseif(str_contains(strtolower($cat->name), 'pudding')) 🍮 
        @elseif(str_contains(strtolower($cat->name), 'pastry')) 🥐 
        @elseif(str_contains(strtolower($cat->name), 'cookie')) 🍪 
        @else 🧁 @endif
        {{ $cat->name }}
    </a>
    @endforeach
</div>

<!-- Dessert Products Grid Header -->
<div class="text-center my-4 py-2">
    <h3 class="fw-bold text-dark mb-2" style="letter-spacing: -0.3px;">
        Pilihan Menu Dessert
    </h3>
    <div class="mx-auto" style="width: 55px; height: 3.5px; background: linear-gradient(135deg, #ff758f 0%, #c2185b 100%); border-radius: 4px;"></div>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
    @forelse($products as $product)
    <div class="col">
        <div class="card card-sweet h-100 overflow-hidden shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div class="position-relative">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-100" style="height: 190px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&q=80'">
                
                <span class="badge bg-light text-dark position-absolute top-0 start-0 m-2 shadow-sm rounded-pill px-2 py-1 small">
                    {{ $product->category->name }}
                </span>

                @if($loop->iteration <= 2)
                    <span class="badge position-absolute top-0 end-0 m-2 shadow-sm rounded-pill px-2 py-1 small text-white" style="background: linear-gradient(135deg, #ff9800, #f57c00);">
                        ⭐ Best Seller
                    </span>
                @elseif($loop->iteration == 3 || $loop->iteration == 4)
                    <span class="badge position-absolute top-0 end-0 m-2 shadow-sm rounded-pill px-2 py-1 small text-white" style="background: linear-gradient(135deg, #e91e63, #c2185b);">
                        🍓 Favorit
                    </span>
                @elseif($product->stock <= 5)
                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 shadow-sm rounded-pill px-2 py-1 small">
                        ⚡ Sisa Sedikit
                    </span>
                @endif
            </div>

            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-1">{{ $product->name }}</h6>
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span class="fs-5 fw-bold text-danger">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                            <i class="bi bi-check-circle-fill"></i> Stok: <strong>{{ $product->stock }}</strong> porsi
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-top d-flex gap-2">
                    <form action="{{ route('shop.cart.add', $product) }}" method="POST" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-sm btn-sweet-outline w-100" title="Masukkan ke Keranjang Belanja">
                            <i class="bi bi-cart-plus"></i> + Keranjang
                        </button>
                    </form>
                    <form action="{{ route('shop.cart.add', $product) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="action" value="buy_now">
                        <button type="submit" class="btn btn-sm btn-sweet-primary px-3" title="Beli Langsung">
                            Beli
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-sweet p-5 text-center text-muted">
            <i class="bi bi-cake2 fs-1 mb-2"></i>
            <h5>Belum ada dessert yang sesuai pencarian Anda.</h5>
            <p class="small">Silakan pilih kategori lain atau reset kata kunci pencarian.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
