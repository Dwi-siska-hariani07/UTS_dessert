@extends('layouts.app')

@section('title', 'Data Pemasok (Suppliers)')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">🚚 Mitra Produsen Dessert (Suppliers)</h3>
        <p class="text-muted small mb-0">Kelola data mitra dapur bakery dan produsen kue yang memasok aneka dessert lezat ke kafe Anda.</p>
    </div>
    <button type="button" class="btn btn-sweet-primary" data-bs-toggle="modal" data-bs-target="#modalCreateSupplier">
        <i class="bi bi-plus-circle-fill me-1"></i> + Tambah Supplier
    </button>
</div>

<!-- Search Bar -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Cari nama supplier, no. telepon, atau email..." value="{{ $search }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Cari</button>
            @if($search)
                <a href="{{ route('suppliers.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table Suppliers -->
<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Supplier</th>
                    <th>Kontak Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th class="text-center">Total PO</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $index => $sup)
                <tr>
                    <td>{{ $suppliers->firstItem() + $index }}</td>
                    <td class="fw-bold text-dark">{{ $sup->name }}</td>
                    <td><span class="badge bg-light text-dark border"><i class="bi bi-telephone"></i> {{ $sup->phone ?: '-' }}</span></td>
                    <td class="small">{{ $sup->email ?: '-' }}</td>
                    <td class="small text-muted">{{ $sup->address ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                            {{ $sup->purchases_count }} Pembelian
                        </span>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalEditSupplier{{ $sup->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        @if(Auth::user()->isAdmin())
                        <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus supplier \'{{ $sup->name }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEditSupplier{{ $sup->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content card-sweet border-0 p-3">
                            <form action="{{ route('suppliers.update', $sup) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold">✏️ Edit Data Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $sup->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nomor Telepon</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $sup->phone }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $sup->email }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Alamat</label>
                                        <textarea name="address" class="form-control" rows="2">{{ $sup->address }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-sweet-primary px-4">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data supplier yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $suppliers->links() }}
    </div>
</div>

<!-- Modal Create Supplier -->
<div class="modal fade" id="modalCreateSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-sweet border-0 p-3">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">🚚 Tambah Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Supplier / Vendor <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: CV Cokelat Premium" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="vendor@cokelat.test">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Alamat gudang / kantor pemasok..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sweet-primary px-4">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
