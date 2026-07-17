<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'sync_status',
        'from_location',
        'to_location',
        'quantity_change',
        'unit_price',
        'supplier_name',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'unit_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
