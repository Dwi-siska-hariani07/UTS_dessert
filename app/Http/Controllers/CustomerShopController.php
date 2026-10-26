<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerShopController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        // Produk Dessert yang Tersedia (Stok > 0) - Tampilkan Semua Menu Tanpa Halaman
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest()
            ->get();

        // 4 Produk Pilihan Promo Spesial
        $featuredPromos = Product::with('category')
            ->where('stock', '>', 0)
            ->take(4)
            ->get();

        $categories = Category::withCount('products')->get();

        return view('shop.index', compact('products', 'featuredPromos', 'categories', 'search', 'categoryId'));
    }

    // ==========================================
    // FITUR KERANJANG BELANJA (MULTI-ITEM CART)
    // ==========================================

    public function addToCart(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan masuk atau daftar akun terlebih dahulu untuk memesan dessert manis kami! 🍰');
        }

        if (Auth::user()->isAdmin() || Auth::user()->isStaff()) {
            return back()->with('info', 'Anda sedang login sebagai ' . strtoupper(Auth::user()->role) . '. Untuk transaksi penjualan di kafe, silakan gunakan menu Transaksi Kasir.');
        }

        if ($product->stock < 1) {
            return back()->with('error', "Maaf, stok dessert '{$product->name}' sedang habis.");
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id]['quantity'] + $qty;
            if ($newQty > $product->stock) {
                return back()->with('error', "Jumlah di keranjang melebihi sisa stok ({$product->stock} porsi).");
            }
            $cart[$product->id]['quantity'] = $newQty;
        } else {
            if ($qty > $product->stock) {
                return back()->with('error', "Jumlah melebihi sisa stok ({$product->stock} porsi).");
            }
            $cart[$product->id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'category' => $product->category->name ?? 'Dessert',
                'price'    => (float) $product->selling_price,
                'quantity' => $qty,
                'image'    => $product->image_url,
                'stock'    => $product->stock,
            ];
        }

        session()->put('cart', $cart);

        if ($request->input('action') === 'buy_now') {
            return redirect()->route('shop.cart');
        }

        return back()->with('success', "🧁 '{$product->name}' ({$qty} porsi) berhasil dimasukkan ke keranjang belanja!");
    }

    public function cart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan masuk atau daftar akun terlebih dahulu untuk melihat keranjang belanja Anda.');
        }

        $cart = session()->get('cart', []);
        $customer = Auth::check() ? Auth::user()->customerProfile : null;

        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += ($item['price'] * $item['quantity']);
        }

        return view('shop.cart', compact('cart', 'totalPrice', 'customer'));
    }

    public function updateCart(Request $request)
    {
        $quantities = $request->input('quantities', []);
        $cart = session()->get('cart', []);

        foreach ($quantities as $productId => $qty) {
            $qty = (int) $qty;
            if (isset($cart[$productId])) {
                $product = Product::find($productId);
                if ($product) {
                    if ($qty <= 0) {
                        unset($cart[$productId]);
                    } elseif ($qty > $product->stock) {
                        $cart[$productId]['quantity'] = $product->stock;
                    } else {
                        $cart[$productId]['quantity'] = $qty;
                    }
                }
            }
        }

        session()->put('cart', $cart);

        if ($request->ajax() || $request->wantsJson()) {
            $totalPrice = 0;
            $totalCount = 0;
            foreach ($cart as $item) {
                $totalPrice += ($item['price'] * $item['quantity']);
                $totalCount += $item['quantity'];
            }
            return response()->json([
                'success'    => true,
                'message'    => 'Keranjang berhasil diperbarui secara otomatis!',
                'totalPrice' => $totalPrice,
                'totalCount' => $totalCount,
                'cart'       => $cart,
            ]);
        }

        return redirect()->route('shop.cart')->with('success', 'Keranjang belanja berhasil diperbarui! ✨');
    }

    public function removeFromCart(Product $product)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart')->with('success', "Item '{$product->name}' telah dihapus dari keranjang.");
    }

    public function checkoutCart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk menyelesaikan pesanan Anda.');
        }

        // Sinkronisasi jumlah terkini jika dikirim dari form checkout
        $quantities = $request->input('quantities', []);
        $cart = session()->get('cart', []);
        if (!empty($quantities) && is_array($quantities)) {
            foreach ($quantities as $productId => $qty) {
                $qty = (int) $qty;
                if (isset($cart[$productId]) && $qty > 0) {
                    $cart[$productId]['quantity'] = $qty;
                }
            }
            session()->put('cart', $cart);
        }

        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $request->validate([
            'delivery_address' => ['required', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $customer = $user->customerProfile;

        if (!$customer) {
            $customer = Customer::firstOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'phone'   => '08123456789',
                    'address' => $request->delivery_address,
                ]
            );
        }

        $sale = DB::transaction(function () use ($cart, $request, $customer, $user) {
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'user_id'     => $user->id,
                'sale_number' => $this->generateSaleNumber(),
                'total'       => 0,
                'status'      => 'completed',
                'sold_at'     => now(),
                'notes'       => "Pesanan Keranjang Online: " . ($request->notes ?: '-') . " | Alamat: " . $request->delivery_address,
            ]);

            $grandTotal = 0;

            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);
                $qty = (int) $item['quantity'];

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok produk '{$product->name}' tidak mencukupi (Tersisa: {$product->stock}, di keranjang: {$qty}).",
                    ]);
                }

                $price = $product->selling_price;
                $subtotal = $price * $qty;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ]);

                // Stok produk otomatis berkurang
                $product->decrement('stock', $qty);
                $grandTotal += $subtotal;
            }

            // Hitung total dari server
            $sale->update(['total' => $grandTotal]);

            return $sale;
        });

        // Kosongkan keranjang belanja setelah checkout berhasil
        session()->forget('cart');

        return redirect()->route('sales.print', [$sale, 'from' => 'checkout'])->with('success', "🎉 Pesanan #{$sale->sale_number} berhasil diproses! Struk pembelian resmi Anda siap disimpan ke file atau dicetak.");
    }

    // ==========================================
    // PESANAN LANGSUNG 1 PRODUK
    // ==========================================

    public function orderForm(Product $product)
    {
        if ($product->stock < 1) {
            return redirect()->route('shop')->with('error', "Maaf, stok dessert '{$product->name}' sedang habis.");
        }
        $customer = Auth::check() ? Auth::user()->customerProfile : null;
        return view('shop.order', compact('product', 'customer'));
    }

    public function placeOrder(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk memesan.');
        }

        $request->validate([
            'quantity'         => ['required', 'integer', 'min:1', "max:{$product->stock}"],
            'delivery_address' => ['required', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $customer = $user->customerProfile;

        if (!$customer) {
            $customer = Customer::firstOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'phone'   => '08123456789',
                    'address' => $request->delivery_address,
                ]
            );
        }

        $qty = (int) $request->quantity;

        $sale = DB::transaction(function () use ($product, $qty, $request, $customer, $user) {
            $lockedProduct = Product::lockForUpdate()->findOrFail($product->id);

            if ($lockedProduct->stock < $qty) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok produk '{$lockedProduct->name}' tidak mencukupi (Tersisa: {$lockedProduct->stock}).",
                ]);
            }

            $price = $lockedProduct->selling_price;
            $subtotal = $price * $qty;

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'user_id'     => $user->id,
                'sale_number' => $this->generateSaleNumber(),
                'total'       => $subtotal,
                'status'      => 'completed',
                'sold_at'     => now(),
                'notes'       => "Pesanan Langsung: " . ($request->notes ?: '-') . " | Alamat: " . $request->delivery_address,
            ]);

            $sale->items()->create([
                'product_id' => $lockedProduct->id,
                'quantity'   => $qty,
                'unit_price' => $price,
                'subtotal'   => $subtotal,
            ]);

            $lockedProduct->decrement('stock', $qty);

            return $sale;
        });

        return redirect()->route('sales.print', [$sale, 'from' => 'checkout'])->with('success', "🎉 Pesanan #{$sale->sale_number} berhasil diproses! Struk pembelian resmi Anda siap disimpan ke file atau dicetak.");
    }

    // ==========================================
    // RIWAYAT PESANAN CUSTOMER
    // ==========================================

    public function myOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = Sale::with(['items.product'])
            ->where('user_id', Auth::id())
            ->latest('sold_at')
            ->paginate(10);

        return view('shop.my-orders', compact('orders'));
    }

    private function generateSaleNumber()
    {
        $date = date('Ymd');
        $last = Sale::whereDate('created_at', today())->count() + 1;
        return 'INV-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
