<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $supplierId = $request->query('supplier_id');
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $purchases = Purchase::with(['supplier', 'user'])
            ->when($search, function ($query, $search) {
                return $query->where('purchase_number', 'like', "%{$search}%");
            })
            ->when($supplierId, function ($query, $supplierId) {
                return $query->where('supplier_id', $supplierId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('purchased_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('purchased_at', '<=', $endDate);
            })
            ->latest('purchased_at')
            ->paginate(10)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers', 'search', 'supplierId', 'status', 'startDate', 'endDate'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('id')->get();
        $products = Product::with('category')->orderBy('name')->get();

        $productsJson = $products->map(function ($prod) {
            return [
                'id' => $prod->id,
                'name' => $prod->name,
                'stock' => $prod->stock,
                'cost' => (float) $prod->purchase_price,
                'supplier_id' => $prod->supplier_id,
                'category' => $prod->category->name ?? '',
            ];
        });

        return view('purchases.create', compact('suppliers', 'products', 'productsJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'      => ['required', 'exists:suppliers,id'],
            'purchased_at'     => ['required', 'date'],
            'notes'            => ['nullable', 'string', 'max:500'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'  => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'supplier_id'     => $request->supplier_id,
                'user_id'         => Auth::id(),
                'purchase_number' => $this->generatePurchaseNumber(),
                'total'           => 0,
                'status'          => 'completed',
                'purchased_at'    => $request->purchased_at,
                'notes'           => $request->notes,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $cost = (float) $item['unit_cost'];

                if ($qty < 1 || $cost < 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Kuantitas atau harga beli tidak valid.',
                    ]);
                }

                $subtotal = $cost * $qty;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_cost'  => $cost,
                    'subtotal'   => $subtotal,
                ]);

                // Stok bertambah otomatis (Business Rule Slide 7)
                $product->increment('stock', $qty);
                $total += $subtotal;
            }

            // Total dihitung oleh server, bukan dipercaya dari frontend (Slide 8)
            $purchase->update(['total' => $total]);
        });

        return redirect()->route('purchases.index')->with('success', 'Transaksi pembelian (stock-in) berhasil dicatat dan stok bertambah! 📦');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang berwenang membatalkan transaksi.');
        }

        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($purchase) {
            // Periksa apakah pembatalan menyebabkan stok negatif
            foreach ($purchase->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product && $product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cancel' => "Gagal membatalkan: Stok produk {$product->name} tersisa {$product->stock}, tidak cukup untuk dikurangkan kembali sejumlah {$item->quantity}.",
                    ]);
                }
            }

            // Pulihkan stok
            foreach ($purchase->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                }
            }

            $purchase->update(['status' => 'cancelled']);
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Transaksi pembelian berhasil dibatalkan dan stok telah dipulihkan.');
    }

    private function generatePurchaseNumber()
    {
        $date = date('Ymd');
        $last = Purchase::whereDate('created_at', today())->count() + 1;
        return 'PO-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
