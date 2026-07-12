<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopInventory extends Model
{
    protected $table = 'shop_inventory';
    
    protected $fillable = [
        'shop_shelf_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function shopShelf()
    {
        return $this->belongsTo(ShopShelf::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
