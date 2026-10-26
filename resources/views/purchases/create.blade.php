@extends('layouts.app')

@section('title', 'Buat Pembelian Stok Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-sweet p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark mb-0">📦 Transaksi Pembelian Baru (Stock-In)</h4>
                </div>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-light border rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Pilih Supplier / Mitra <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplierSelect" class="form-select" required onchange="onSupplierChanged()">
                            <option value="">-- Pilih Supplier / Mitra --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tanggal & Waktu Pembelian <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="purchased_at" class="form-control" value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Contoh: Pengadaan batch weekend, invoice vendor #9921" value="{{ old('notes') }}">
                    </div>
                </div>

                <!-- Multiple Items Table (Slide 7 & 13: Add multiple items per transaction) -->
                <div class="card card-sweet p-3 mb-4 bg-light-subtle">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-1"></i> Item Produk yang Dibeli / Di-restok</h6>
                        <button type="button" class="btn btn-sm btn-sweet-primary" id="btnAddItem">
                            <i class="bi bi-plus-circle"></i> Tambah Baris Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered bg-white align-middle" id="itemsTable">
                            <thead class="table-light small">
                                <tr>
                                    <th style="width: 45%;">Produk Dessert</th>
                                    <th style="width: 20%;">Jumlah (Qty)</th>
                                    <th style="width: 20%;">Harga Beli Satuan (Rp)</th>
                                    <th style="width: 15%;">Subtotal (Rp)</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-select product-select" required onchange="updateProductCost(this)">
                                            <option value="">-- Pilih Mitra terlebih dahulu --</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control item-qty" min="1" value="1" required oninput="calculateSubtotal(this)">
                                    </td>
                                    <td>
                                        <input type="number" step="100" name="items[0][unit_cost]" class="form-control item-cost" min="0" value="0" required oninput="calculateSubtotal(this)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-subtotal bg-light" value="0" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeItemRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3" class="text-end fw-bold">Estimasi Total Pembelian:</th>
                                    <th class="text-end fw-bold text-danger fs-5" id="grandTotalDisplay">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-sweet-primary px-4">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi & Simpan Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productsData = @json($productsJson);

    let rowIndex = 1;

    function onSupplierChanged() {
        const supplierSelect = document.getElementById('supplierSelect');
        const supplierId = supplierSelect.value;

        // Re-populate all rows
        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.product-select');
            populateProductSelect(select, supplierId);
        });
    }

    function populateProductSelect(select, supplierId) {
        const previousVal = select.value;
        select.innerHTML = '';

        if (!supplierId) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '-- Pilih Mitra Terlebih Dahulu --';
            select.appendChild(opt);
            select.disabled = true;
            updateProductCost(select);
            return;
        }

        select.disabled = false;
        const filtered = productsData.filter(p => String(p.supplier_id) === String(supplierId));

        if (filtered.length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '-- Tidak ada menu untuk mitra ini --';
            select.appendChild(opt);
            updateProductCost(select);
            return;
        }

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = `-- Pilih Dessert (${filtered.length} menu tersedia) --`;
        select.appendChild(defaultOpt);

        let foundPrev = false;
        filtered.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.setAttribute('data-cost', p.cost);
            opt.textContent = `${p.name} (Stok: ${p.stock})`;
            if (String(p.id) === String(previousVal)) {
                opt.selected = true;
                foundPrev = true;
            }
            select.appendChild(opt);
        });

        if (!foundPrev) {
            select.value = '';
        }
        updateProductCost(select);
    }

    document.getElementById('btnAddItem').addEventListener('click', function() {
        const supplierId = document.getElementById('supplierSelect').value;
        if (!supplierId) {
            alert('Harap pilih Mitra / Supplier terlebih dahulu sebelum menambah baris produk.');
            document.getElementById('supplierSelect').focus();
            return;
        }

        const tbody = document.getElementById('itemsBody');
        const firstRow = document.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        // Update name attributes with unique rowIndex
        const select = newRow.querySelector('.product-select');
        select.name = `items[${rowIndex}][product_id]`;
        populateProductSelect(select, supplierId);

        const qtyInput = newRow.querySelector('.item-qty');
        qtyInput.name = `items[${rowIndex}][quantity]`;
        qtyInput.value = 1;

        const costInput = newRow.querySelector('.item-cost');
        costInput.name = `items[${rowIndex}][unit_cost]`;
        costInput.value = 0;

        newRow.querySelector('.item-subtotal').value = '0';

        tbody.appendChild(newRow);
        rowIndex++;
        calculateGrandTotal();
    });

    function updateProductCost(select) {
        const row = select.closest('tr');
        if (!row) return;

        const option = select.options[select.selectedIndex];
        const cost = (option && option.getAttribute('data-cost')) ? parseFloat(option.getAttribute('data-cost')) : 0;
        
        row.querySelector('.item-cost').value = cost;
        calculateSubtotal(select);
    }

    function calculateSubtotal(element) {
        const row = element.closest('tr');
        if (!row) return;

        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
        const subtotal = qty * cost;
        row.querySelector('.item-subtotal').value = subtotal.toLocaleString('id-ID');
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
            grandTotal += (qty * cost);
        });
        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    function removeItemRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateGrandTotal();
        } else {
            alert('Minimal harus ada 1 item produk dalam transaksi pembelian.');
        }
    }

    // Jalankan filter saat halaman selesai dimuat (jika supplier sudah terpilih)
    document.addEventListener('DOMContentLoaded', function() {
        onSupplierChanged();
    });
</script>
@endpush
