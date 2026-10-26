@extends('layouts.app')

@section('title', 'Detail Penjualan #' . $sale->sale_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card card-sweet p-4 shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-1 mb-1">
                        {{ strtoupper($sale->status) }}
                    </span>
                    <h4 class="fw-bold text-dark mb-0">Nota: {{ $sale->sale_number }}</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('sales.print', $sale) }}" target="_blank" class="btn btn-sm btn-sweet-outline">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </a>

                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-light border rounded-pill">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>

                    @if($sale->status === 'completed' && Auth::user()->isAdmin())
                    <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('PERINGATAN: Membatalkan transaksi ini akan mengembalikan stok produk ke etalase. Lanjutkan pembatalan?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="bi bi-x-circle"></i> Batalkan Penjualan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Customer & Order Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4">
                        <div class="text-muted small fw-semibold">DATA PEMBELI (CUSTOMER)</div>
                        <h6 class="fw-bold text-dark mt-1 mb-1">
                            <i class="bi bi-person-heart text-danger me-1"></i> {{ $sale->customer->name ?? 'Pelanggan Umum (Walk-in)' }}
                        </h6>
                        <div class="small text-muted"><i class="bi bi-telephone"></i> {{ $sale->customer->phone ?? '-' }}</div>
                        <div class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $sale->customer->address ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4">
                        <div class="text-muted small fw-semibold">DETAIL TRANSAKSI</div>
                        <div class="mt-1 small"><strong>Tanggal Transaksi:</strong> {{ $sale->sold_at->format('d F Y, H:i') }} WIB</div>
                        <div class="small"><strong>Kasir / Staff:</strong> {{ $sale->user->name }}</div>
                        <div class="small"><strong>Catatan Khusus:</strong> {{ $sale->notes ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cake2 text-danger me-1"></i> Rincian Menu Dessert Dipesan</h6>
            <div class="table-responsive">
                <table class="table table-sweet align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Menu Dessert</th>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah (Qty)</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item->product->image_url }}" alt="" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <span class="fw-semibold">{{ $item->product->name }}</span>
                                </div>
                            </td>
                            <td class="small">{{ $item->product->category->name }}</td>
                            <td class="text-center fw-bold">{{ $item->quantity }} Porsi</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="5" class="text-end fs-6 fw-bold">Total Pembayaran:</th>
                            <th class="text-end fs-4 fw-bold text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
