@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            @if(Auth::user()->isAdmin())
                <h3 class="fw-bold text-dark mb-0">🍰 Dashboard Ringkasan Bisnis</h3>
                <span class="badge bg-danger rounded-pill px-3 py-1 small">ADMIN / OWNER</span>
            @else
                <h3 class="fw-bold text-dark mb-0">🍰 Dashboard Operasional Kasir & Toko</h3>
                <span class="badge bg-primary rounded-pill px-3 py-1 small">STAFF KASIR</span>
            @endif
        </div>
        @if(Auth::user()->isAdmin())
            <p class="text-muted small mb-0">Pemantauan inventori stok dessert, performa keuangan kafe, dan ringkasan laba rugi real-time.</p>
        @else
            <p class="text-muted small mb-0">Halo <strong>{{ Auth::user()->name }}</strong>! Semangat melayani pelanggan manis hari ini. Pantau shift kasir dan sisa stok etalase.</p>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-sweet-primary shadow-sm">
            <i class="bi bi-cart-plus-fill me-1"></i> + Penjualan Baru (Kasir)
        </a>
        <a href="{{ route('purchases.create') }}" class="btn btn-sweet-outline bg-white shadow-sm">
            <i class="bi bi-box-arrow-in-down me-1"></i> + Restok Pembelian
        </a>
    </div>
</div>

<!-- 4 Metrik Utama Finansial & Operasional (Dibedakan Berdasarkan Peran Admin vs Staff) -->
<div class="row g-3 mb-4">
    @if(Auth::user()->isAdmin())
        {{-- Tampilan Metrik Pemilik Bisnis (Admin/Owner) --}}
        <!-- 1. Total Penjualan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">TOTAL PENJUALAN</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-success mb-1">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</h3>
                <div class="small text-muted d-flex justify-content-between">
                    <span>Hari ini: <strong>Rp {{ number_format($salesToday, 0, ',', '.') }}</strong></span>
                    <span>({{ $totalSalesCount }} nota)</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Pembelian (Modal Bahan/Menu) -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">TOTAL PEMBELIAN (MODAL)</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                        <i class="bi bi-truck fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-primary mb-1">Rp {{ number_format($totalPurchasesAmount, 0, ',', '.') }}</h3>
                <div class="small text-muted">
                    Dari <strong>{{ $totalSuppliers }} Mitra</strong> &bull; {{ $totalPurchasesCount }} Faktur
                </div>
            </div>
        </div>

        <!-- 3. Estimasi Laba Bersih (Profit) -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">ESTIMASI LABA KOTOR</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(194, 24, 91, 0.15); color: #c2185b;">
                        <i class="bi bi-pie-chart-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($estimatedProfit, 0, ',', '.') }}</h3>
                <div class="small text-muted">
                    <i class="bi bi-arrow-up-right text-success"></i> Penjualan − Modal Pembelian
                </div>
            </div>
        </div>

        <!-- 4. Peringatan Stok Rendah -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">PERINGATAN STOK</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-danger mb-1">{{ $lowStockProducts->count() }} <span class="fs-6 fw-normal text-muted">Dessert</span></h3>
                <div class="small {{ $lowStockProducts->count() > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                    @if($lowStockProducts->count() > 0)
                        ⚠️ Sisa stok &le; Batas Minimum
                    @else
                        ✅ Seluruh stok dessert aman
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Tampilan Metrik Operasional Kasir & Toko (Staff) --}}
        <!-- 1. Kas Masuk Shift Hari Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">KAS MASUK HARI INI</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-success mb-1">Rp {{ number_format($salesToday, 0, ',', '.') }}</h3>
                <div class="small text-muted">
                    <strong>{{ $salesTodayCount }}</strong> Transaksi kasir berhasil hari ini
                </div>
            </div>
        </div>

        <!-- 2. Porsi Dessert Terjual Hari Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">DESSERT TERJUAL HARI INI</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;">
                        <i class="bi bi-bag-check-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $itemsSoldToday }} <span class="fs-6 fw-normal text-muted">Porsi</span></h3>
                <div class="small text-muted">
                    Total dessert laku dipesan pelanggan
                </div>
            </div>
        </div>

        <!-- 3. Total Menu di Etalase -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">VARIAN MENU AKTIF</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                        <i class="bi bi-cake2-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-primary mb-1">{{ $totalProducts }} <span class="fs-6 fw-normal text-muted">Menu</span></h3>
                <div class="small text-muted">
                    Dalam <strong>{{ $totalCategories }} Kategori</strong> dessert etalase
                </div>
            </div>
        </div>

        <!-- 4. Peringatan Stok Etalase Toko -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sweet p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">PERINGATAN STOK</span>
                    <span class="badge rounded-circle p-2" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-danger mb-1">{{ $lowStockProducts->count() }} <span class="fs-6 fw-normal text-muted">Dessert</span></h3>
                <div class="small {{ $lowStockProducts->count() > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                    @if($lowStockProducts->count() > 0)
                        ⚠️ Perlu restok dari mitra dapur
                    @else
                        ✅ Stok etalase aman & siap dijual
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Grafik Tren Penjualan 7 Hari Terakhir (Chart.js) -->
<div class="card card-sweet p-4 mb-4 shadow-sm border-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-graph-up-arrow text-danger me-2"></i> Tren Pendapatan Penjualan (7 Hari Terakhir)
            </h5>
            <p class="text-muted small mb-0">Visualisasi omset penjualan harian Sweetie Dessert secara otomatis.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                Bulan Ini: <strong>Rp {{ number_format($salesMonth, 0, ',', '.') }}</strong>
            </span>
            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill small fw-semibold">
                {{ $totalTransactions }} Transaksi Tercatat
            </span>
        </div>
    </div>
    <div style="height: 250px;">
        <canvas id="salesTrendsChart"></canvas>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tabel Produk Stok Rendah (Slide 18) -->
    <div class="col-lg-6">
        <div class="card card-sweet h-100 p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-danger mb-0"><i class="bi bi-exclamation-octagon-fill me-1"></i> Produk Perlu Restok</h5>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border rounded-pill">Kelola Katalog</a>
            </div>

            @if($lowStockProducts->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                    <div class="fw-semibold text-dark">Stok Sangat Aman!</div>
                    <small>Seluruh stok dessert saat ini berada di atas batas minimum.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sweet align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Min. Stok</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts->take(5) as $low)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $low->image_url }}" alt="{{ $low->name }}" class="rounded-3" style="width: 38px; height: 38px; object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ $low->name }}</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">SKU: {{ $low->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $low->category->name }}</td>
                                <td class="text-center"><span class="badge bg-danger rounded-pill px-2">{{ $low->stock }}</span></td>
                                <td class="text-center small text-muted">{{ $low->minimum_stock }}</td>
                                <td class="text-end">
                                    <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-sweet-primary rounded-pill py-1 px-3 small">Restok</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Transaksi Penjualan Terbaru (Kasir) -->
    <div class="col-lg-6">
        <div class="card card-sweet h-100 p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-receipt-cutoff me-1 text-primary"></i> Penjualan Kasir Terbaru</h5>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-light border rounded-pill">Semua Penjualan</a>
            </div>

            @if($recentSales->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x fs-1 mb-2 d-block text-secondary"></i>
                    Belum ada transaksi penjualan kasir yang tercatat.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sweet align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. Nota</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                            <tr>
                                <td class="fw-semibold small text-dark">{{ $sale->sale_number }}</td>
                                <td class="small">{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                                <td class="small fw-semibold text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('sales.print', $sale) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill py-1 px-2" title="Cetak Struk">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesTrendsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Penjualan Harian',
                        data: {!! json_encode($chartSales) !!},
                        borderColor: '#c2185b',
                        backgroundColor: 'rgba(255, 117, 143, 0.18)',
                        borderWidth: 3,
                        pointBackgroundColor: '#c2185b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
