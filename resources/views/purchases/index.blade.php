@extends('layouts.app')

@section('title', 'Transaksi Pembelian (Stock-In)')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">📦 Transaksi Pembelian / Restok (Stock-In)</h3>
        <p class="text-muted small mb-0">Pencatatan barang/stok masuk dari supplier; stok dessert bertambah secara otomatis.</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-sweet-primary">
        <i class="bi bi-plus-circle-fill me-1"></i> + Buat Pembelian Baru
    </a>
</div>

<!-- Filter Bar -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('purchases.index') }}" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Cari No. PO..." value="{{ $search }}">
        </div>
        <div class="col-md-3">
            <select name="supplier_id" class="form-select">
                <option value="">-- Semua Supplier --</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
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
            @if($search || $supplierId || $status || $startDate || $endDate)
                <a href="{{ route('purchases.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table Purchases -->
<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th>No. PO</th>
                    <th>Supplier</th>
                    <th>Petugas</th>
                    <th>Tanggal Beli</th>
                    <th class="text-end">Total Biaya</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                <tr>
                    <td class="fw-bold text-dark">{{ $p->purchase_number }}</td>
                    <td>{{ $p->supplier->name }}</td>
                    <td class="small">{{ $p->user->name }}</td>
                    <td class="small">{{ $p->purchased_at->format('d M Y, H:i') }}</td>
                    <td class="text-end fw-bold text-dark">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $p->status === 'completed' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-1">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi pembelian yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
