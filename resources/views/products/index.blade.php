@extends('layouts.app')

@section('title', 'Katalog Produk Dessert')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">🍰 Katalog Produk Dessert</h3>
        <p class="text-muted small mb-0">Kelola inventori, harga modal, harga jual, dan stok dessert lezat Anda.</p>
    </div>
    @if(Auth::user()->isAdmin())
    <div class="d-flex gap-2">
        <a href="{{ route('products.create') }}" class="btn btn-sweet-primary">
            <i class="bi bi-plus-circle-fill me-1"></i> + Tambah Produk Dessert
        </a>
    </div>
    @endif
</div>

<!-- Search & Filter Card (Slide 18) -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama dessert atau kode SKU..." value="{{ $search }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <div class="form-check pt-1">
                <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStockCheck" {{ $lowStock ? 'checked' : '' }}>
                <label class="form-check-label small fw-semibold text-danger" for="lowStockCheck">
                    Hanya Stok Menipis
                </label>
            </div>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Filter</button>
            @if($search || $categoryId || $lowStock)
                <a href="{{ route('products.index') }}" class="btn btn-light border" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Product Cards Grid (Aesthetic Pastel Dessert Cards) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
    @forelse($products as $product)
    <div class="col">
        <div class="card card-sweet h-100 overflow-hidden shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
            <div class="position-relative">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-100" style="height: 180px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&q=80'">
                
                <span class="badge bg-light text-dark position-absolute top-0 start-0 m-2 shadow-sm rounded-pill px-2 py-1 small">
                    {{ $product->category->name }}
                </span>

                @if($product->isLowStock())
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2 shadow-sm rounded-pill px-2 py-1 small">
                        <i class="bi bi-exclamation-triangle-fill"></i> Stok Kritis!
                    </span>
                @endif
            </div>

            <div class="card-body p-3 d-flex flex-direction-column flex-column justify-content-between">
                <div>
                    <div class="text-muted" style="font-size: 0.78rem;">SKU: {{ $product->sku }}</div>
                    <h6 class="fw-bold text-dark mt-1 mb-2">{{ $product->name }}</h6>
                    
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span class="fs-5 fw-bold text-danger">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        @if(Auth::user()->isAdmin())
                            <span class="text-muted small" style="font-size: 0.75rem;">Modal: Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge {{ $product->stock > $product->minimum_stock ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1">
                            Tersedia: <strong>{{ $product->stock }}</strong> porsi
                        </span>
                        <span class="text-muted" style="font-size: 0.75rem;">Min: {{ $product->minimum_stock }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top gap-1">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-light border rounded-pill {{ Auth::user()->isAdmin() ? 'px-2' : 'w-100 py-1' }}" style="font-size: 0.78rem;">
                        <i class="bi bi-eye"></i> Detail Menu
                    </a>
                    
                    @if(Auth::user()->isAdmin())
                    <div class="d-flex gap-1">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" style="font-size: 0.78rem;">
                            <i class="bi bi-pencil"></i> Edit
                        </a>

                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dessert \'{{ $product->name }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" style="font-size: 0.78rem;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-sweet p-5 text-center text-muted">
            <i class="bi bi-cake2 fs-1 mb-2"></i>
            <h5>Belum ada produk dessert yang sesuai kriteria filter.</h5>
            <p class="small">Silakan ubah kata kunci pencarian atau tambahkan produk baru.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination (Slide 18: Pertahankan query filter saat berpindah halaman) -->
<div class="d-flex justify-content-center">
    {{ $products->links() }}
</div>
@endsection
