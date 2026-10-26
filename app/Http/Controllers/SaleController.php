<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $customerId = $request->query('customer_id');
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $sales = Sale::with(['customer', 'user'])
            ->when($search, function ($query, $search) {
                return $query->where('sale_number', 'like', "%{$search}%");
            })
            ->when($customerId, function ($query, $customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('sold_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('sold_at', '<=', $endDate);
            })
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();

        return view('sales.index', compact('sales', 'customers', 'search', 'customerId', 'status', 'startDate', 'endDate'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        // Hanya tampilkan produk dessert yang memiliki stok > 0
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'        => ['nullable', 'exists:customers,id'],
            'sold_at'            => ['required', 'date'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $sale = DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'user_id'     => Auth::id(),
                'sale_number' => $this->generateSaleNumber(),
                'total'       => 0,
                'status'      => 'completed',
                'sold_at'     => $request->sold_at,
                'notes'       => $request->notes,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                // Cegah race-condition / overselling dengan lockForUpdate() (Slide 16)
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];

                // Business Rule Slide 8 & 16: Penjualan tidak boleh melebihi stok yang tersedia
                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk '{$product->name}' tidak mencukupi. (Tersedia: {$product->stock}, Diminta: {$qty})",
                    ]);
                }

                // Ambil harga resmi dari database server, bukan dari browser! (Slide 8 & 16)
                $price = $product->selling_price;
                $subtotal = $price * $qty;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ]);

                // Stok berkurang otomatis hanya jika transaksi berhasil
                $product->decrement('stock', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => $total]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Transaksi penjualan berhasil dicatat dan stok berkurang! 🧁');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        // Jika akun customer, pastikan hanya melihat struk miliknya sendiri
        if (Auth::user()->isCustomer() && $sale->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berwenang mengakses struk pesanan ini.');
        }

        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.print', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang berwenang membatalkan transaksi.');
        }

        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($sale) {
            // Pulihkan stok kembali ke produk (Slide 8: memulihkan stok secara konsisten)
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $sale->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Transaksi penjualan berhasil dibatalkan dan stok produk telah dipulihkan.');
    }

    private function generateSaleNumber()
    {
        $date = date('Ymd');
        $last = Sale::whereDate('created_at', today())->count() + 1;
        return 'INV-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
