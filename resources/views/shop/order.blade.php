@extends('layouts.app')

@section('title', 'Pesan Dessert - ' . $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-sweet p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark mb-1">🍰 Checkout Pesanan Dessert</h4>
                    <p class="text-muted small mb-0">Lengkapi jumlah pesanan dan alamat pengantaran dessert Anda.</p>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-sm btn-light border rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali ke Menu
                </a>
            </div>

            <!-- Product Summary Card -->
            <div class="card card-sweet p-3 mb-4 bg-light border-0">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-4" style="width: 100px; height: 100px; object-fit: cover;">
                    <div>
                        <span class="badge bg-danger rounded-pill px-2 py-1 small mb-1">{{ $product->category->name }}</span>
                        <h5 class="fw-bold text-dark mb-1">{{ $product->name }}</h5>
                        <div class="fs-5 fw-bold text-danger">Rp {{ number_format($product->selling_price, 0, ',', '.') }} / porsi</div>
                        <div class="small text-muted">Sisa Stok Tersedia: <strong>{{ $product->stock }}</strong> porsi</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('shop.order.post', $product) }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Jumlah Porsi yang Dipesan <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="orderQty" class="form-control form-control-lg @error('quantity') is-invalid @enderror" min="1" max="{{ $product->stock }}" value="1" required oninput="updateTotal(this.value)">
                        <div class="form-text small">Maksimal pemesanan saat ini: {{ $product->stock }} porsi.</div>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Total Pembayaran</label>
                        <div class="form-control form-control-lg bg-light fw-bold text-success" id="totalDisplay">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alamat Pengiriman Lengkap <span class="text-danger">*</span></label>
                        <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap pengantaran dessert..." required>{{ old('delivery_address', $customer->address ?? '') }}</textarea>
                        @error('delivery_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Catatan Pesanan Khusus (Opsional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Contoh: Tulisan ucapan 'Happy Birthday', lilin, pita pink...">
                    </div>
                </div>

                <div class="alert alert-info rounded-4 border-0 mt-4 mb-4 small">
                    <i class="bi bi-info-circle-fill me-1"></i> 
                    <strong>Otomasi Sistem:</strong> Pesanan Anda akan langsung terhubung ke sistem kasir admin dan staff, serta memotong stok dessert secara otomatis dan akurat.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('shop') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-sweet-primary px-5 fs-6">
                        <i class="bi bi-bag-check-fill me-1"></i> Konfirmasi & Beli Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const unitPrice = {{ $product->selling_price }};
    function updateTotal(qty) {
        qty = parseInt(qty) || 0;
        const total = qty * unitPrice;
        document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endpush
