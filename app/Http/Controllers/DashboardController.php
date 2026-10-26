<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. Agregat Metrik Utama (Slide 18)
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();

        // Penjualan hari ini dan bulan ini
        $salesToday = Sale::where('status', 'completed')
            ->whereDate('sold_at', $today)
            ->sum('total');

        $salesTodayCount = Sale::where('status', 'completed')
            ->whereDate('sold_at', $today)
            ->count();

        $itemsSoldToday = SaleItem::whereHas('sale', function ($q) use ($today) {
            $q->where('status', 'completed')->whereDate('sold_at', $today);
        })->sum('quantity');

        $salesMonth = Sale::where('status', 'completed')
            ->where('sold_at', '>=', $thisMonth)
            ->sum('total');

        $totalSalesCount = Sale::where('status', 'completed')->count();
        $totalPurchasesCount = Purchase::where('status', 'completed')->count();
        $totalTransactions = $totalSalesCount + $totalPurchasesCount;

        // Total Nilai Finansial Bisnis (Slide 18: Total Penjualan, Total Pembelian, Laba/Profit)
        $totalSalesAmount = Sale::where('status', 'completed')->sum('total');
        $totalPurchasesAmount = Purchase::where('status', 'completed')->sum('total');
        $estimatedProfit = $totalSalesAmount - $totalPurchasesAmount;

        // Data Grafik Tren Penjualan 7 Hari Terakhir
        $chartLabels = [];
        $chartSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $sum = Sale::where('status', 'completed')
                ->whereDate('sold_at', $date)
                ->sum('total');
            $chartSales[] = (float) $sum;
        }

        // 2. Produk Stok Menipis (Stok <= Minimum Stock)
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->get();

        // 3. Transaksi Penjualan Terakhir
        $recentSales = Sale::with(['customer', 'user'])
            ->latest('sold_at')
            ->take(5)
            ->get();

        // 4. Transaksi Pembelian Terakhir
        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->latest('purchased_at')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalCustomers',
            'salesToday',
            'salesTodayCount',
            'itemsSoldToday',
            'salesMonth',
            'totalTransactions',
            'totalSalesCount',
            'totalPurchasesCount',
            'lowStockProducts',
            'recentSales',
            'recentPurchases',
            'totalSalesAmount',
            'totalPurchasesAmount',
            'estimatedProfit',
            'chartLabels',
            'chartSales'
        ));
    }
}
