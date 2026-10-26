@extends('layouts.app')

@section('title', 'Dashboard Ringkasan')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Dashboard Ringkasan</h2>
</div>

<!-- Widget Statistik Asli P4 Teman -->
<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 200px; border-top: 4px solid #3b82f6;">
        <h3 style="margin-top: 0; color: #6b7280; font-size: 16px;">Total Produk</h3>
        <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #1f2937;">{{ $totalProducts }}</p>
    </div>
    
    <div class="card" style="flex: 1; min-width: 200px; border-top: 4px solid #ef4444;">
        <h3 style="margin-top: 0; color: #6b7280; font-size: 16px;">Stok Menipis / Habis</h3>
        <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #ef4444;">{{ $lowStockCount }}</p>
    </div>
    
    <div class="card" style="flex: 1; min-width: 200px; border-top: 4px solid #10b981;">
        <h3 style="margin-top: 0; color: #6b7280; font-size: 16px;">Total Barang Masuk</h3>
        <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #1f2937;">{{ $totalIn }}</p>
    </div>
    
    <div class="card" style="flex: 1; min-width: 200px; border-top: 4px solid #f59e0b;">
        <h3 style="margin-top: 0; color: #6b7280; font-size: 16px;">Total Barang Keluar</h3>
        <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #1f2937;">{{ $totalOut }}</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h3>Panduan Sistem</h3>
    <p>Selamat datang di sistem manajemen inventaris. Aplikasi ini memungkinkan Anda untuk mengelola master data produk dan melacak pergerakan stok (Barang Masuk & Keluar).</p>
    <ul>
        <li>Gunakan menu <strong>Data Produk</strong> untuk menambah, mengubah, atau menghapus master data produk.</li>
        <li>Gunakan menu <strong>Transaksi Stok</strong> untuk mencatat mutasi barang masuk atau keluar gudang.</li>
    </ul>
</div>
@endsection
