@extends('layouts.app')

@section('title', 'Detail Pembelian #' . $purchase->purchase_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card card-sweet p-4 shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <span class="badge {{ $purchase->status === 'completed' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-1 mb-1">
                        {{ strtoupper($purchase->status) }}
                    </span>
                    <h4 class="fw-bold text-dark mb-0">No. PO: {{ $purchase->purchase_number }}</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-light border rounded-pill">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>

                    @if($purchase->status === 'completed' && Auth::user()->isAdmin())
                    <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('PERINGATAN: Membatalkan transaksi ini akan mengurangkan kembali stok produk. Lanjutkan pembatalan?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="bi bi-x-circle"></i> Batalkan Pembelian
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Header Info -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4">
                        <div class="text-muted small fw-semibold">INFORMASI SUPPLIER</div>
                        <h6 class="fw-bold text-dark mt-1 mb-1">{{ $purchase->supplier->name }}</h6>
                        <div class="small text-muted"><i class="bi bi-telephone"></i> {{ $purchase->supplier->phone ?: '-' }}</div>
                        <div class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $purchase->supplier->address ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4">
                        <div class="text-muted small fw-semibold">INFORMASI TRANSAKSI</div>
                        <div class="mt-1 small"><strong>Tanggal:</strong> {{ $purchase->purchased_at->format('d F Y, H:i') }} WIB</div>
                        <div class="small"><strong>Dicatat oleh:</strong> {{ $purchase->user->name }}</div>
                        <div class="small"><strong>Catatan:</strong> {{ $purchase->notes ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check me-1"></i> Rincian Produk yang Dibeli</h6>
            <div class="table-responsive">
                <table class="table table-sweet align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Produk Dessert</th>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah (Qty)</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item->product->image_url }}" alt="" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <span class="fw-semibold">{{ $item->product->name }}</span>
                                </div>
                            </td>
                            <td class="small">{{ $item->product->category->name }}</td>
                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="5" class="text-end fs-6 fw-bold">Total Nilai Pembelian:</th>
                            <th class="text-end fs-5 fw-bold text-danger">Rp {{ number_format($purchase->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
