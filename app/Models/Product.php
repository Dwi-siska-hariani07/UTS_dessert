<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
        'image',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isLowStock()
    {
        return $this->stock <= $this->minimum_stock;
    }

    /**
     * Relasi logis spesialisasi dessert ke masing-masing mitra:
     * 1 = Dapur Cake & Tart Artisan (Cake & Tart, Cheesecake)
     * 2 = Pastry & Croissant Fresh Bakery (Pastry & Cookies)
     * 3 = Sweet Pudding & Dessert Box House (Tiramisu & Pudding, Dessert Box)
     */
    public function getSupplierIdAttribute()
    {
        if (in_array($this->category_id, [1, 3])) {
            return 1;
        }
        if (in_array($this->category_id, [4])) {
            return 2;
        }
        return 3;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            if (str_starts_with($this->image, 'image/')) {
                return asset($this->image);
            }
            return asset('image/' . $this->image);
        }
        return 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&q=80';
    }
}
