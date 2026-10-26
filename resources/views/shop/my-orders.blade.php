@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">🛍️ Riwayat Pesanan Dessert Saya</h3>
        <p class="text-muted small mb-0">Daftar transaksi pesanan manis yang pernah Anda beli di Sweetie Dessert.</p>
    </div>
    <a href="{{ route('shop') }}" class="btn btn-sweet-primary">
        <i class="bi bi-plus-circle-fill me-1"></i> + Pesan Dessert Lagi
    </a>
</div>

<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Tanggal Pesan</th>
                    <th>Menu Dessert</th>
                    <th class="text-end">Total Pembayaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Struk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="fw-bold text-dark">{{ $order->sale_number }}</td>
                    <td class="small">{{ $order->sold_at->format('d M Y, H:i') }} WIB</td>
                    <td>
                        @foreach($order->items as $item)
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <img src="{{ $item->product->image_url }}" alt="" class="rounded-2" style="width: 28px; height: 28px; object-fit: cover;">
                                <span class="small fw-semibold">{{ $item->product->name }}</span>
                                <span class="badge bg-light text-dark border small">x{{ $item->quantity }}</span>
                            </div>
                        @endforeach
                    </td>
                    <td class="text-end fw-bold text-success fs-6">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $order->status === 'completed' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-1">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('sales.print', $order) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm" title="Lihat Struk & Simpan ke File">
                            <i class="bi bi-receipt-cutoff me-1"></i> Cetak Struk
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-bag-x fs-1 d-block mb-2 text-danger"></i>
                        <h5>Belum Ada Riwayat Pesanan</h5>
                        <p class="small mb-3">Yuk pilih dessert lezat favoritmu sekarang!</p>
                        <a href="{{ route('shop') }}" class="btn btn-sm btn-sweet-primary px-4">Lihat Menu Dessert</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
</div>
@endsection
