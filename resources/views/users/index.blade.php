@extends('layouts.app')

@section('title', 'Kelola Pengguna (Users)')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">👥 Manajemen Pengguna & Hak Akses</h3>
        <p class="text-muted small mb-0">Kelola akun Admin dan Staff kasir untuk pembatasan hak akses sistem.</p>
    </div>
    <button type="button" class="btn btn-sweet-primary" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
        <i class="bi bi-person-plus-fill me-1"></i> + Tambah Pengguna Baru
    </button>
</div>

<!-- Search Bar -->
<div class="card card-sweet p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('users.index') }}" class="row g-2">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau email pengguna..." value="{{ $search }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sweet-primary w-100">Cari</button>
            @if($search)
                <a href="{{ route('users.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table Users -->
<div class="card card-sweet p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sweet align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th class="text-center">Peran (Role)</th>
                    <th>Terdaftar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                <tr>
                    <td>{{ $users->firstItem() + $index }}</td>
                    <td class="fw-bold text-dark">
                        {{ $u->name }}
                        @if($u->id === Auth::id())
                            <span class="badge bg-light text-dark border small ms-1">(Anda)</span>
                        @endif
                    </td>
                    <td><code>{{ $u->username }}</code></td>
                    <td class="small">{{ $u->email }}</td>
                    <td class="text-center">
                        <span class="{{ $u->isAdmin() ? 'badge-role-admin' : 'badge-role-staff' }}">
                            {{ strtoupper($u->role) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $u->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        @if($u->id !== Auth::id())
                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun \'{{ $u->name }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal Edit User -->
                <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content card-sweet border-0 p-3">
                            <form action="{{ route('users.update', $u) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold">✏️ Edit Pengguna</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                                        <input type="text" name="username" class="form-control" value="{{ $u->username }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Hak Akses (Role) <span class="text-danger">*</span></label>
                                        <select name="role" class="form-select" required>
                                            <option value="staff" {{ $u->role === 'staff' ? 'selected' : '' }}>Staff (Kasir & Restok)</option>
                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                                        </select>
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
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada pengguna yang terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>

<!-- Modal Create User -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-sweet border-0 p-3">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">👥 Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Siska Hariani" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Username Login <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: siska07" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="siska@sweetie.test" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="staff" selected>Staff (Kasir & Restok)</option>
                            <option value="admin">Admin (Akses Penuh)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sweet-primary px-4">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
