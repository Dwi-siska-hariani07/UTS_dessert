@extends('layouts.app')

@section('title', '403 - Akses Dibatasi')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-7 col-lg-6 text-center">
        <div class="card card-sweet p-5 shadow-sm border-0">
            <div class="mb-3">
                <span style="font-size: 4rem;">🔒</span>
            </div>
            
            <h3 class="fw-bold text-dark mb-2">Akses Terbatas</h3>
            
            <div class="alert alert-warning rounded-4 border-0 small text-start my-3">
                <i class="bi bi-info-circle-fill text-warning me-1"></i>
                {{ $exception->getMessage() ?: 'Peran akun Anda saat ini tidak memiliki izin untuk membuka halaman ini.' }}
            </div>

            <p class="text-muted small mb-4">
                Halaman Dashboard, Produk, Supplier, dan Kasir dikhususkan untuk akun <strong>Admin & Staff</strong>. Jika Anda saat ini masuk sebagai <strong>Customer (Pembeli)</strong>, silakan menuju toko belanja atau ganti akun.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                <a href="{{ route('shop') }}" class="btn btn-sweet-primary px-4 py-2">
                    <i class="bi bi-cake2-fill me-1"></i> Ke Toko & Promo Dessert
                </a>

                @auth
                <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('Keluar dari akun saat ini dan beralih ke login?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 py-2 w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar (Logout)
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
