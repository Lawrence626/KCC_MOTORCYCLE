<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_name', 'name');
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
