<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian - {{ $sale->sale_number }}</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Poppins:wght@400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">

    <style>
        :root {
            --sweet-pink: #c2185b;
            --sweet-pink-light: #fff0f3;
            --sweet-border: #f8bbd0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f9;
            color: #212529;
            padding: 24px 10px 40px 10px;
            min-height: 100vh;
        }

        /* 1. KERTAS STRUK PEMBELIAN DI ATAS */
        .receipt-paper {
            font-family: 'Courier Prime', 'Courier New', Courier, monospace;
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 24px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            color: #111;
        }

        .receipt-brand {
            font-family: 'Great Vibes', cursive;
            color: var(--sweet-pink);
            font-size: 2.3rem;
            line-height: 1.1;
            font-weight: bold;
        }

        .divider-dashed {
            border-top: 1.5px dashed #6c757d;
            margin: 12px 0;
        }

        .divider-double {
            border-top: 2px solid #212529;
            margin: 12px 0;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .receipt-table th {
            padding: 6px 0;
            border-bottom: 1px dashed #6c757d;
            font-weight: 700;
        }

        .receipt-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .badge-lunas {
            border: 2px solid #198754;
            color: #198754;
            padding: 4px 14px;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 0.85rem;
            display: inline-block;
            border-radius: 4px;
            margin-top: 6px;
        }

        /* 2. TOOLBAR DOWNLOAD & AKSI DI BAWAH */
        .toolbar-container {
            max-width: 440px;
            margin: 22px auto 0 auto;
        }

        .btn-sweet-action {
            background: linear-gradient(135deg, #ff758f 0%, #c2185b 100%);
            color: #fff;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            padding: 11px 20px;
            transition: all 0.2s ease;
        }

        .btn-sweet-action:hover {
            background: linear-gradient(135deg, #ff4d6d 0%, #a01048 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(194, 24, 91, 0.25);
        }

        /* Media Print Optimization */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .receipt-paper {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 10px !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>

    @if(session('success'))
    <div class="toolbar-container no-print mb-3" style="max-width: 440px; margin: 0 auto 16px auto;">
        <div class="alert alert-success rounded-4 shadow-sm d-flex align-items-center gap-2 mb-0">
            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
            <div class="small">
                <strong>Pemesanan Berhasil!</strong><br>
                {{ session('success') }}
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- 1. STRUK PEMBELIAN RESMI (DI POSISI ATAS)                 -->
    <!-- ======================================================== -->
    <div class="receipt-paper" id="receiptArea">
        <!-- Logo & Header Toko -->
        <div class="text-center">
            <div class="receipt-brand">Sweetie Dessert</div>
            <div style="font-size: 13px; font-weight: 700; letter-spacing: 0.5px;">HOMEMADE & PREMIUM DESSERT CAFE</div>
            <div style="font-size: 11px; color: #555;">Jl. Manis No. 07 Surabaya | Telp: (031) 8899-7766</div>
            <div style="font-size: 11px; color: #555;">IG: @sweetie.dessert | Web: sweetiedessert.id</div>
        </div>

        <div class="divider-double"></div>

        <!-- Detail Informasi Nota -->
        <table style="width: 100%; font-size: 12px;">
            <tr>
                <td style="width: 100px;"><strong>No. Nota</strong></td>
                <td>: <strong>{{ $sale->sale_number }}</strong></td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ $sale->sold_at->format('d/m/Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Customer</strong></td>
                <td>: {{ $sale->customer->name ?? (Auth::user()->name ?? 'Pelanggan Umum') }}</td>
            </tr>
            <tr>
                <td><strong>Kasir / Akun</strong></td>
                <td>: {{ $sale->user->name ?? 'Kasir Online' }}</td>
            </tr>
            @if($sale->notes)
            <tr>
                <td style="vertical-align: top;"><strong>Catatan</strong></td>
                <td>: <span style="font-size: 11px;">{{ $sale->notes }}</span></td>
            </tr>
            @endif
        </table>

        <div class="divider-dashed"></div>

        <!-- Tabel Daftar Menu yang Dibeli -->
        <table class="receipt-table">
            <thead>
                <tr>
                    <th align="left">MENU DESSERT</th>
                    <th align="center" style="width: 45px;">QTY</th>
                    <th align="right" style="width: 90px;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td colspan="3" style="font-weight: 700; padding-top: 6px;">
                        {{ $item->product->name }}
                    </td>
                </tr>
                <tr style="border-bottom: 1px dotted #ccc;">
                    <td style="font-size: 11px; color: #555;">
                        @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                    </td>
                    <td align="center" style="font-size: 12px; font-weight: 600;">
                        {{ $item->quantity }}
                    </td>
                    <td align="right" style="font-weight: 600;">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider-dashed"></div>

        <!-- Ringkasan Total Pembayaran -->
        <table style="width: 100%; font-size: 13px;">
            <tr>
                <td>Subtotal Menu ({{ $sale->items->sum('quantity') }} porsi)</td>
                <td align="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Pengantaran</td>
                <td align="right"><strong>GRATIS (PROMO)</strong></td>
            </tr>
            <tr style="font-size: 16px; font-weight: 700;">
                <td style="padding-top: 8px;">TOTAL AKHIR</td>
                <td align="right" style="padding-top: 8px; color: var(--sweet-pink);">
                    Rp {{ number_format($sale->total, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="divider-double"></div>

        <!-- Status Pembayaran & Ucapan -->
        <div class="text-center">
            <div class="badge-lunas">
                <i class="bi bi-check2-circle"></i> LUNAS (COMPLETED)
            </div>

            <div style="font-size: 11px; color: #555; margin-top: 14px; line-height: 1.5;">
                Terima kasih telah memesan di <strong>Sweetie Dessert</strong>! ❤️<br>
                Pesanan Anda sedang dipersiapkan dengan penuh cinta.<br>
                <em>Semoga harimu selalu manis & menyenangkan!</em>
            </div>
            
            <div style="font-size: 9px; color: #888; margin-top: 10px; border-top: 1px dotted #bbb; padding-top: 6px;">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 2. OPSI DOWNLOAD & CETAK KE FILE (DI POSISI BAWAH)        -->
    <!-- ======================================================== -->
    <div class="toolbar-container no-print">
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <h6 class="fw-bold text-dark mb-1 text-center">
                <i class="bi bi-download text-danger me-1"></i> Simpan Struk Pembelian
            </h6>
            <p class="text-muted small text-center mb-3">
                Simpan struk di atas sebagai bukti pembayaran pesanan Anda.
            </p>

            <div class="d-grid gap-2">
                <!-- Tombol Cetak / Simpan ke PDF -->
                <button type="button" class="btn btn-sweet-action w-100" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan ke File (PDF)
                </button>

                <!-- Tombol Download File Teks Langsung -->
                <button type="button" class="btn btn-outline-dark rounded-pill fw-semibold py-2" onclick="downloadReceiptText()">
                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download File Struk (.txt)
                </button>
            </div>

            <div class="alert alert-light border small rounded-3 mt-3 mb-0 text-muted">
                <i class="bi bi-info-circle-fill text-primary me-1"></i>
                <strong>Tips Simpan PDF:</strong> Saat jendela print muncul, pada bagian <em>Destination / Tujuan</em> pilih <strong>"Save as PDF" (Simpan sebagai PDF)</strong>, lalu klik Simpan.
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center px-1">
            <a href="{{ route('shop.my-orders') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Riwayat Pesanan
            </a>
            <a href="{{ route('shop') }}" class="btn btn-sm btn-sweet-action rounded-pill px-3">
                <i class="bi bi-cake2 me-1"></i> Belanja Lagi
            </a>
        </div>
    </div>

    <!-- SCRIPT DOWNLOAD TEXT & AUTO PRINT -->
    <script>
        function downloadReceiptText() {
            let text = "";
            text += "================================================\n";
            text += "                SWEETIE DESSERT                 \n";
            text += "        HOMEMADE & PREMIUM DESSERT CAFE         \n";
            text += "      Jl. Manis No. 07 Surabaya | (031) 8899    \n";
            text += "================================================\n";
            text += "No. Nota   : {{ $sale->sale_number }}\n";
            text += "Tanggal    : {{ $sale->sold_at->format('d/m/Y H:i') }} WIB\n";
            text += "Customer   : {{ $sale->customer->name ?? (Auth::user()->name ?? 'Pelanggan Umum') }}\n";
            text += "Kasir/User : {{ $sale->user->name ?? 'Online' }}\n";
            @if($sale->notes)
            text += "Catatan    : {{ $sale->notes }}\n";
            @endif
            text += "------------------------------------------------\n";
            text += "ITEM                           QTY        TOTAL \n";
            text += "------------------------------------------------\n";
            @foreach($sale->items as $item)
            text += "{{ str_pad(substr($item->product->name, 0, 28), 28) }} {{ str_pad($item->quantity, 3, ' ', STR_PAD_LEFT) }}   Rp {{ str_pad(number_format($item->subtotal, 0, ',', '.'), 10, ' ', STR_PAD_LEFT) }}\n";
            text += "  @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}\n";
            @endforeach
            text += "================================================\n";
            text += "TOTAL PEMBAYARAN            : Rp {{ number_format($sale->total, 0, ',', '.') }}\n";
            text += "STATUS                      : LUNAS (COMPLETED)\n";
            text += "================================================\n";
            text += "     Terima kasih telah memesan dessert kami!   \n";
            text += "          Semoga harimu selalu manis! ❤️         \n";
            text += "================================================\n";

            const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = "Struk-{{ $sale->sale_number }}.txt";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Jika baru saja checkout, otomatis tampilkan prompt print/simpan setelah 600ms
        @if(request('from') === 'checkout')
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
        @endif
    </script>
</body>
</html>