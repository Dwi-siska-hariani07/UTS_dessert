@extends('layouts.app')

@section('title', 'Keranjang Belanja Dessert')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">🛒 Keranjang Belanja Dessert</h3>
        <p class="text-muted small mb-0">Periksa seluruh menu dessert pilihan Anda sebelum melakukan pembayaran.</p>
    </div>
    <a href="{{ route('shop') }}" class="btn btn-sweet-outline">
        <i class="bi bi-arrow-left me-1"></i> + Tambah Menu Lain
    </a>
</div>

@if(empty($cart))
    <div class="card card-sweet p-5 text-center shadow-sm">
        <div class="py-4">
            <span class="fs-1 d-block mb-3 text-muted">🛒</span>
            <h4 class="fw-bold text-dark mb-2">Keranjang Belanja Anda Masih Kosong</h4>
            <p class="text-muted small mb-4">Belum ada dessert manis yang Anda masukkan ke keranjang.</p>
            <a href="{{ route('shop') }}" class="btn btn-sweet-primary px-4 py-2">
                <i class="bi bi-cake2-fill me-1"></i> Lihat Menu & Mulai Belanja
            </a>
        </div>
    </div>
@else
    <div class="row g-4">
        <!-- List Item Keranjang -->
        <div class="col-lg-8">
            <div class="card card-sweet p-4 shadow-sm mb-4">
                <form id="cartUpdateForm" action="{{ route('shop.cart.update') }}" method="POST">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold text-dark mb-0">Rincian Menu ({{ count($cart) }} Macam)</h5>
                            <span id="autoSaveIndicator" class="badge bg-light text-muted border small d-none">
                                <i class="bi bi-check2-circle text-success"></i> Tersimpan otomatis
                            </span>
                        </div>
                        <button type="submit" class="btn btn-sm btn-light border rounded-pill d-none d-md-inline-block">
                            <i class="bi bi-arrow-repeat me-1"></i> Simpan Manual
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sweet align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Dessert</th>
                                    <th>Harga</th>
                                    <th style="width: 145px;">Jumlah Porsi</th>
                                    <th class="text-end">Subtotal</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                <tr id="cartRow-{{ $id }}" class="cart-item-row" data-id="{{ $id }}" data-price="{{ $item['price'] }}" data-stock="{{ $item['stock'] }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $item['image'] }}" alt="" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $item['name'] }}</div>
                                                <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">{{ $item['category'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small fw-semibold">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <!-- Plus / Minus Stepper ala Shopee -->
                                        <div class="input-group input-group-sm" style="width: 130px;">
                                            <button type="button" class="btn btn-outline-secondary px-2 btn-qty-minus" data-id="{{ $id }}">−</button>
                                            <input type="number" 
                                                   name="quantities[{{ $id }}]" 
                                                   id="qtyInput-{{ $id }}" 
                                                   class="form-control text-center fw-bold cart-qty-input px-1" 
                                                   data-id="{{ $id }}" 
                                                   min="1" 
                                                   max="{{ $item['stock'] }}" 
                                                   value="{{ $item['quantity'] }}">
                                            <button type="button" class="btn btn-outline-secondary px-2 btn-qty-plus" data-id="{{ $id }}">+</button>
                                        </div>
                                        <div class="text-muted text-center mt-1" style="font-size: 0.7rem;">Sisa stok: {{ $item['stock'] }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        <span id="subtotal-{{ $id }}" class="row-subtotal">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="document.getElementById('removeForm{{ $id }}').submit();" title="Hapus menu ini">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Hidden Delete Forms -->
                @foreach($cart as $id => $item)
                    <form id="removeForm{{ $id }}" action="{{ route('shop.cart.remove', $id) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endforeach
            </div>
        </div>

        <!-- Form Pengiriman & Checkout -->
        <div class="col-lg-4">
            <div class="card card-sweet p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Ringkasan Pembayaran</h5>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Total Harga Menu</span>
                    <span id="totalMenuPrice" class="fw-semibold">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Biaya Pengantaran</span>
                    <span class="badge bg-success-subtle text-success">GRATIS PROMO</span>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 mb-4 border-top">
                    <span class="fw-bold fs-6">Total Tagihan:</span>
                    <span id="totalBillPrice" class="fw-bold text-danger fs-4">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>

                @auth
                    <form id="checkoutForm" action="{{ route('shop.cart.checkout') }}" method="POST">
                        @csrf
                        <!-- Hidden inputs for synced quantities -->
                        <div id="checkoutQuantitiesContainer">
                            @foreach($cart as $id => $item)
                                <input type="hidden" name="quantities[{{ $id }}]" id="checkoutQty-{{ $id }}" value="{{ $item['quantity'] }}">
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Alamat Lengkap Pengantaran <span class="text-danger">*</span></label>
                            <textarea name="delivery_address" class="form-control" rows="3" placeholder="Jl. Mawar No. 12, Kelurahan..." required>{{ old('delivery_address', $customer->address ?? '') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Catatan Khusus (Opsional)</label>
                            <input type="text" name="notes" class="form-control" placeholder="Contoh: Titip di satpam / Jangan terlalu manis" value="{{ old('notes') }}">
                        </div>

                        <button type="submit" class="btn btn-sweet-primary w-100 py-3 fw-bold fs-6" onclick="return confirm('Pesan semua menu dessert ini sekarang?');">
                            <i class="bi bi-bag-check-fill me-1"></i> Beli Sekarang (Checkout)
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning small rounded-3 mb-3">
                        <i class="bi bi-info-circle me-1"></i> Silakan masuk atau daftar terlebih dahulu untuk menyelesaikan pesanan Anda.
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-sweet-primary w-100 py-2 mb-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk (Login) untuk Beli
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-sweet-outline w-100 py-2">
                        <i class="bi bi-heart-fill me-1"></i> Daftar Akun Baru
                    </a>
                @endauth
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
    /* Styling stepper input ala Shopee */
    .cart-qty-input::-webkit-outer-spin-button,
    .cart-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .cart-qty-input[type=number] {
        -moz-appearance: textfield;
    }
    .btn-qty-minus, .btn-qty-plus {
        font-weight: bold;
        background-color: #f8f9fa;
        color: #495057;
        border-color: #ced4da;
    }
    .btn-qty-minus:hover, .btn-qty-plus:hover {
        background-color: #ffe4e9;
        color: var(--sweet-pink-dark);
        border-color: var(--sweet-pink-accent);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.cart-item-row');
    const totalMenuPriceEl = document.getElementById('totalMenuPrice');
    const totalBillPriceEl = document.getElementById('totalBillPrice');
    const autoSaveIndicator = document.getElementById('autoSaveIndicator');
    const cartUpdateForm = document.getElementById('cartUpdateForm');
    let autoSaveTimeout = null;

    // Helper format rupiah
    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    // Fungsi hitung ulang otomatis di browser (Client-Side Realtime)
    function recalculateCart() {
        let grandTotal = 0;
        let totalItems = 0;

        rows.forEach(row => {
            const id = row.getAttribute('data-id');
            const price = parseFloat(row.getAttribute('data-price')) || 0;
            const stock = parseInt(row.getAttribute('data-stock')) || 999;
            const input = document.getElementById('qtyInput-' + id);
            const subtotalEl = document.getElementById('subtotal-' + id);
            const checkoutQtyEl = document.getElementById('checkoutQty-' + id);

            let qty = parseInt(input.value) || 1;
            if (qty < 1) {
                qty = 1;
                input.value = 1;
            } else if (qty > stock) {
                qty = stock;
                input.value = stock;
                alert(`Maksimal pesanan dessert ini adalah ${stock} porsi sesuai sisa stok.`);
            }

            const rowSubtotal = price * qty;
            grandTotal += rowSubtotal;
            totalItems += qty;

            // Update subtotal baris
            if (subtotalEl) {
                subtotalEl.innerText = formatRupiah(rowSubtotal);
            }

            // Sync hidden field checkout
            if (checkoutQtyEl) {
                checkoutQtyEl.value = qty;
            }
        });

        // Update total di ringkasan pembayaran
        if (totalMenuPriceEl) totalMenuPriceEl.innerText = formatRupiah(grandTotal);
        if (totalBillPriceEl) totalBillPriceEl.innerText = formatRupiah(grandTotal);

        // Update badge keranjang di navbar jika ada
        const navBadge = document.querySelector('.cart-shopee-badge');
        if (navBadge) {
            navBadge.innerText = totalItems;
        }

        // Trigger auto save ke backend session (AJAX debounce)
        triggerAutoSave();
    }

    // Fungsi kirim data ke server agar tersimpan di session otomatis
    function triggerAutoSave() {
        if (!cartUpdateForm) return;

        if (autoSaveTimeout) {
            clearTimeout(autoSaveTimeout);
        }

        if (autoSaveIndicator) {
            autoSaveIndicator.classList.remove('d-none');
            autoSaveIndicator.innerHTML = '<span class="spinner-border spinner-border-sm text-secondary me-1" style="width: 10px; height: 10px;"></span> Menyimpan...';
        }

        autoSaveTimeout = setTimeout(() => {
            const formData = new FormData(cartUpdateForm);

            fetch(cartUpdateForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (autoSaveIndicator) {
                    autoSaveIndicator.innerHTML = '<i class="bi bi-check2-circle text-success"></i> Tersimpan otomatis';
                    setTimeout(() => {
                        autoSaveIndicator.classList.add('d-none');
                    }, 2500);
                }
            })
            .catch(err => {
                if (autoSaveIndicator) {
                    autoSaveIndicator.innerHTML = '<i class="bi bi-exclamation-circle text-muted"></i> Otomatis diperbarui';
                }
            });
        }, 400); // 400ms debounce
    }

    // Pasang listener pada tombol plus dan minus
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const input = document.getElementById('qtyInput-' + id);
            const row = document.getElementById('cartRow-' + id);
            const stock = parseInt(row.getAttribute('data-stock')) || 999;

            let currentVal = parseInt(input.value) || 1;
            if (currentVal < stock) {
                input.value = currentVal + 1;
                recalculateCart();
            } else {
                alert(`Jumlah sudah mencapai batas maksimal stok (${stock} porsi).`);
            }
        });
    });

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const input = document.getElementById('qtyInput-' + id);

            let currentVal = parseInt(input.value) || 1;
            if (currentVal > 1) {
                input.value = currentVal - 1;
                recalculateCart();
            }
        });
    });

    // Pasang listener input langsung jika user mengetik angka
    document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.addEventListener('input', function () {
            recalculateCart();
        });
        input.addEventListener('change', function () {
            recalculateCart();
        });
    });
});
</script>
@endpush
@endsection
