<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'contact_position',
        'address',
        'email',
        'phone',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Products directly linked via the supplier_name string column (legacy).
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_name', 'name');
    }

    /**
     * Products mapped to this supplier via the supplier_products pivot table.
     */
    public function suppliedProducts()
    {
        return $this->belongsToMany(Product::class, 'supplier_products');
    }

    public function getTotalValueAttribute()
    {
        return $this->products->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
    }

    public function getAveragePriceAttribute()
    {
        return $this->products->count()
            ? $this->products->avg('unit_price')
            : 0;
    }
}
