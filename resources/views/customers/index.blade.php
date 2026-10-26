@extends('layouts.app')

@section('title', 'Data Pelanggan (Customers)')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">❤️ Data Pelanggan (Customers)</h3>
        <p class="text-muted small mb-0">Kelola data pembeli dessert untuk pencatatan transaksi penjualan yang rapi.</p>
    </div>
    <button type="button" class="btn btn-sweet-primary" data-bs-toggle="modal" data-bs-target="#modalCreateCustomer">
        <i class="bi bi-person-plus-fill me-1"></i> + Tambah Customer Baru
    </button>
</div>

<!-- Search Bar -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('customers.index') }}" class="row g-2">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Cari nama pelanggan, nomor WhatsApp/telepon, atau email..." value="{{ $search }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Cari</button>
            @if($search)
                <a href="{{ route('customers.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table Customers -->
<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Pelanggan</th>
                    <th>No. WhatsApp / Telepon</th>
                    <th>Email</th>
                    <th>Alamat / Domisili</th>
                    <th class="text-center">Riwayat Pembelian</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $index => $cust)
                <tr>
                    <td>{{ $customers->firstItem() + $index }}</td>
                    <td class="fw-bold text-dark">
                        <i class="bi bi-person-circle text-danger me-1"></i> {{ $cust->name }}
                    </td>
                    <td><span class="badge bg-light text-dark border"><i class="bi bi-whatsapp text-success"></i> {{ $cust->phone ?: '-' }}</span></td>
                    <td class="small">{{ $cust->email ?: '-' }}</td>
                    <td class="small text-muted">{{ $cust->address ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                            {{ $cust->sales_count }} Kali Belanja
                        </span>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalEditCustomer{{ $cust->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        @if(Auth::user()->isAdmin())
                        <form action="{{ route('customers.destroy', $cust) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pelanggan \'{{ $cust->name }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal Edit Customer -->
                <div class="modal fade" id="modalEditCustomer{{ $cust->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content card-sweet border-0 p-3">
                            <form action="{{ route('customers.update', $cust) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold">✏️ Edit Data Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $cust->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">No. WhatsApp / Telepon</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $cust->phone }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $cust->email }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Alamat Pengiriman</label>
                                        <textarea name="address" class="form-control" rows="2">{{ $cust->address }}</textarea>
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
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data customer yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $customers->links() }}
    </div>
</div>

<!-- Modal Create Customer -->
<div class="modal fade" id="modalCreateCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-sweet border-0 p-3">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">🌸 Tambah Customer Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Clarissa Aurelia" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">No. WhatsApp / Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="clarissa@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Alamat rumah / kantor untuk pengiriman dessert..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sweet-primary px-4">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
