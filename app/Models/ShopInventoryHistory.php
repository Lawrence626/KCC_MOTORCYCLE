<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopInventoryHistory extends Model
{
    protected $table = 'shop_inventory_history';
    
    protected $fillable = [
        'shop_shelf_id',
        'product_id',
        'action_type',
        'quantity_change',
        'source_type',
        'source_id',
        'destination_type',
        'destination_id',
        'user_id',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'metadata' => 'json',
    ];

    public function shopShelf()
    {
        return $this->belongsTo(ShopShelf::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
