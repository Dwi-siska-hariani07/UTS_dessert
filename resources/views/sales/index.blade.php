@extends('layouts.app')

@section('title', 'Transaksi Penjualan Dessert')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">🛍️ Transaksi Penjualan (Sales)</h3>
        <p class="text-muted small mb-0">Pencatatan kasir penjualan dessert ke pelanggan; stok produk berkurang secara otomatis.</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-sweet-primary">
        <i class="bi bi-cart-plus-fill me-1"></i> + Buat Penjualan Baru
    </a>
</div>

<!-- Filter Bar -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('sales.index') }}" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Cari No. Nota..." value="{{ $search }}">
        </div>
        <div class="col-md-3">
            <select name="customer_id" class="form-select">
                <option value="">-- Semua Pelanggan --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Status --</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" placeholder="Dari Tgl">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Filter</button>
            @if($search || $customerId || $status || $startDate || $endDate)
                <a href="{{ route('sales.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table Sales -->
<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Pelanggan</th>
                    <th>Kasir / Staff</th>
                    <th>Tanggal Jual</th>
                    <th class="text-end">Total Belanja</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $s)
                <tr>
                    <td class="fw-bold text-dark">{{ $s->sale_number }}</td>
                    <td>
                        <span class="fw-semibold">{{ $s->customer->name ?? 'Walk-in Customer' }}</span>
                    </td>
                    <td class="small">{{ $s->user->name }}</td>
                    <td class="small">{{ $s->sold_at->format('d M Y, H:i') }}</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $s->status === 'completed' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-1">
                            {{ ucfirst($s->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('sales.show', $s) }}" class="btn btn-sm btn-light border rounded-pill px-2">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <a href="{{ route('sales.print', $s) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Cetak Struk">
                            <i class="bi bi-printer"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi penjualan yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $sales->links() }}
    </div>
</div>
@endsection
