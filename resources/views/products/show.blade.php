@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-sweet p-4 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">{{ $product->category->name }}</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $product->name }}</h4>
                </div>
                <div class="d-flex gap-2">
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-sweet-outline">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    @endif
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row g-4 align-items-center">
                <div class="col-md-5 text-center">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded-4 shadow-sm" style="max-height: 280px; width: 100%; object-fit: cover;">
                </div>

                <div class="col-md-7">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="text-muted small" style="width: 35%;">Kode SKU</th>
                                    <td><span class="badge bg-secondary-subtle text-dark fs-6">{{ $product->sku }}</span></td>
                                </tr>
                                @if(Auth::user()->isAdmin())
                                <tr>
                                    <th class="text-muted small">Harga Modal / Beli</th>
                                    <td class="fw-semibold">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th class="text-muted small">Harga Jual Konsumen</th>
                                    <td class="fs-4 fw-bold text-danger">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                </tr>
                                @if(Auth::user()->isAdmin())
                                <tr>
                                    <th class="text-muted small">Estimasi Margin Profit</th>
                                    <td class="text-success fw-semibold">
                                        Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}
                                        <small class="text-muted">({{ $product->purchase_price > 0 ? round((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100, 1) : 0 }}%)</small>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th class="text-muted small">Stok Tersedia</th>
                                    <td>
                                        <span class="badge {{ $product->stock > $product->minimum_stock ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2 rounded-pill">
                                            {{ $product->stock }} Porsi
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted small">Batas Minimum Stok</th>
                                    <td>{{ $product->minimum_stock }} Porsi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi Produk Ini -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card card-sweet p-3 shadow-sm h-100">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cart-check text-primary me-1"></i> Riwayat Penjualan Terakhir</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-sweet small align-middle">
                            <thead>
                                <tr>
                                    <th>No. Nota</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->saleItems->take(5) as $si)
                                <tr>
                                    <td><a href="{{ route('sales.show', $si->sale_id) }}">{{ $si->sale->sale_number }}</a></td>
                                    <td>{{ $si->sale->sold_at->format('d/m/Y') }}</td>
                                    <td class="text-center fw-bold">{{ $si->quantity }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada transaksi penjualan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-sweet p-3 shadow-sm h-100">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-arrow-in-down text-success me-1"></i> Riwayat Pembelian (Restok)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-sweet small align-middle">
                            <thead>
                                <tr>
                                    <th>No. PO</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->purchaseItems->take(5) as $pi)
                                <tr>
                                    <td><a href="{{ route('purchases.show', $pi->purchase_id) }}">{{ $pi->purchase->purchase_number }}</a></td>
                                    <td>{{ $pi->purchase->purchased_at->format('d/m/Y') }}</td>
                                    <td class="text-center fw-bold">{{ $pi->quantity }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada transaksi pembelian.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
