<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $lowStock = $request->boolean('low_stock');

        // Eager load category to prevent N+1 queries (Slide 10)
        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($lowStock, function ($query) {
                return $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'lowStock'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang berwenang menambah produk dessert baru.');
        }
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang berwenang menambah produk dessert baru.');
        }
        // Validasi Ketat Sesuai Slide 15
        $validated = $request->validate([
            'sku'            => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name'           => ['required', 'string', 'max:150'],
            'category_id'    => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'minimum_stock'  => ['required', 'integer', 'min:0'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('image'), $imageName);
            $validated['image'] = $imageName;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk dessert berhasil ditambahkan! 🍰');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'saleItems.sale', 'purchaseItems.purchase']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang berwenang mengubah data produk dessert.');
        }
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang berwenang mengubah data produk dessert.');
        }
        $validated = $request->validate([
            'sku'            => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name'           => ['required', 'string', 'max:150'],
            'category_id'    => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'minimum_stock'  => ['required', 'integer', 'min:0'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('image'), $imageName);
            $validated['image'] = $imageName;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk dessert berhasil diperbarui! ✨');
    }

    public function destroy(Product $product)
    {
        // Business Rule Slide 8: Cegah penghapusan fisik jika ada histori transaksi
        $hasHistory = $product->saleItems()->exists() || $product->purchaseItems()->exists();

        if ($hasHistory) {
            // Soft delete
            $product->delete();
            return redirect()->route('products.index')->with('info', "Produk '{$product->name}' diarsipkan (soft delete) karena memiliki riwayat transaksi.");
        }

        $product->forceDelete();
        return redirect()->route('products.index')->with('success', 'Produk dessert berhasil dihapus permanen. 🗑️');
    }
}
