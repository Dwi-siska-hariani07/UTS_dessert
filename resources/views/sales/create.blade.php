@extends('layouts.app')

@section('title', 'Buat Transaksi Penjualan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-sweet p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark mb-0">🧁 Kasir Penjualan Baru (Sales)</h4>
                </div>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-light border rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form action="{{ route('sales.store') }}" method="POST" id="salesForm">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Pilih Customer / Pembeli <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select">
                            <option value="">-- Pelanggan Umum (Walk-In) --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->phone ?: 'No telp -' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted">Pilih nama pelanggan terdaftar (member kafe) atau biarkan kosong untuk pembeli umum (walk-in).</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tanggal & Waktu Transaksi <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="sold_at" class="form-control" value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Catatan Pesanan (Opsional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Contoh: Tambah pita ucapan ulang tahun / Takeaway" value="{{ old('notes') }}">
                    </div>
                </div>

                <!-- Items Table (Slide 7, 8, 16) -->
                <div class="card card-sweet p-3 mb-4 bg-light-subtle">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-cart3 me-1"></i> Rincian Menu Dessert yang Dipesan</h6>
                        <button type="button" class="btn btn-sm btn-sweet-primary" id="btnAddSaleItem">
                            <i class="bi bi-plus-circle"></i> Tambah Menu Lain
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered bg-white align-middle" id="saleItemsTable">
                            <thead class="table-light small">
                                <tr>
                                    <th style="width: 45%;">Pilih Menu Dessert</th>
                                    <th style="width: 15%;">Harga Satuan</th>
                                    <th style="width: 15%;">Jumlah (Qty)</th>
                                    <th style="width: 20%;">Subtotal</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="saleItemsBody">
                                <tr class="sale-item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-select product-select" required onchange="handleProductChange(this)">
                                            <option value="">-- Pilih Dessert --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->selling_price }}" data-stock="{{ $p->stock }}">
                                                    {{ $p->name }} (Sisa Stok: {{ $p->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="small text-danger stock-warning d-none mt-1">
                                            <i class="bi bi-exclamation-triangle"></i> Jumlah melebihi stok yang ada!
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-price bg-light" value="0" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control item-qty" min="1" max="999" value="1" required oninput="handleQtyChange(this)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-subtotal bg-light fw-bold text-end" value="Rp 0" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeSaleRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3" class="text-end fw-bold fs-6">Total Pembayaran:</th>
                                    <th class="text-end fw-bold text-success fs-4" id="saleGrandTotal">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sales.index') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-sweet-primary px-4" id="btnSubmitSale">
                        <i class="bi bi-check2-circle me-1"></i> Proses Penjualan & Cetak Struk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let saleRowIndex = 1;

    document.getElementById('btnAddSaleItem').addEventListener('click', function() {
        const tbody = document.getElementById('saleItemsBody');
        const firstRow = document.querySelector('.sale-item-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('select').name = `items[${saleRowIndex}][product_id]`;
        newRow.querySelector('select').value = '';
        newRow.querySelector('.item-price').value = 0;
        newRow.querySelector('.item-qty').name = `items[${saleRowIndex}][quantity]`;
        newRow.querySelector('.item-qty').value = 1;
        newRow.querySelector('.item-subtotal').value = 'Rp 0';
        newRow.querySelector('.stock-warning').classList.add('d-none');

        tbody.appendChild(newRow);
        saleRowIndex++;
        calculateSaleTotal();
    });

    function handleProductChange(select) {
        const option = select.options[select.selectedIndex];
        const price = parseFloat(option.getAttribute('data-price')) || 0;
        const stock = parseInt(option.getAttribute('data-stock')) || 0;
        const row = select.closest('tr');

        row.querySelector('.item-price').value = price.toLocaleString('id-ID');
        row.querySelector('.item-qty').max = stock;

        handleQtyChange(row.querySelector('.item-qty'));
    }

    function handleQtyChange(input) {
        const row = input.closest('tr');
        const select = row.querySelector('.product-select');
        const option = select.options[select.selectedIndex];
        const price = parseFloat(option.getAttribute('data-price')) || 0;
        const stock = parseInt(option.getAttribute('data-stock')) || 0;
        const qty = parseInt(input.value) || 0;

        const warning = row.querySelector('.stock-warning');
        if (qty > stock) {
            warning.classList.remove('d-none');
            input.classList.add('is-invalid');
        } else {
            warning.classList.add('d-none');
            input.classList.remove('is-invalid');
        }

        const subtotal = qty * price;
        row.querySelector('.item-subtotal').value = 'Rp ' + subtotal.toLocaleString('id-ID');
        calculateSaleTotal();
    }

    function calculateSaleTotal() {
        let total = 0;
        document.querySelectorAll('.sale-item-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const option = select.options[select.selectedIndex];
            const price = parseFloat(option.getAttribute('data-price')) || 0;
            const qty = parseInt(row.querySelector('.item-qty').value) || 0;
            total += (qty * price);
        });
        document.getElementById('saleGrandTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function removeSaleRow(btn) {
        const rows = document.querySelectorAll('.sale-item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateSaleTotal();
        } else {
            alert('Minimal harus ada 1 item pesanan.');
        }
    }
</script>
@endpush
