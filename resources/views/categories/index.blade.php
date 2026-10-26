@extends('layouts.app')

@section('title', 'Kategori Dessert')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">🏷️ Kategori Dessert</h3>
        <p class="text-muted small mb-0">Kelola kelompok menu makanan penutup untuk memudahkan klasifikasi produk.</p>
    </div>
    <button type="button" class="btn btn-sweet-primary" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">
        <i class="bi bi-plus-circle-fill me-1"></i> + Tambah Kategori
    </button>
</div>

<!-- Search Bar -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('categories.index') }}" class="row g-2">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau deskripsi kategori..." value="{{ $search }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Cari</button>
            @if($search)
                <a href="{{ route('categories.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table Categories -->
<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Jumlah Produk</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $cat)
                <tr>
                    <td>{{ $categories->firstItem() + $index }}</td>
                    <td class="fw-bold text-dark">{{ $cat->name }}</td>
                    <td class="text-muted small">{{ $cat->description ?: '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="badge bg-pink-subtle text-danger text-decoration-none px-3 py-1 rounded-pill">
                            {{ $cat->products_count }} Dessert
                        </a>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalEditCategory{{ $cat->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        @if(Auth::user()->isAdmin())
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori \'{{ $cat->name }}\'?');">
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
                <div class="modal fade" id="modalEditCategory{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content card-sweet border-0 p-3">
                            <form action="{{ route('categories.update', $cat) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold">✏️ Edit Kategori</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Deskripsi Singkat</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $cat->description }}</textarea>
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
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada kategori yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $categories->links() }}
    </div>
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="modalCreateCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-sweet border-0 p-3">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">🍰 Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Macarons & Choux" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Kue kering khas perancis beraneka rasa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sweet-primary px-4">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
