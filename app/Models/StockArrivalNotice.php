<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockArrivalNotice extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'sku',
        'quantity',
        'purchase_order_id',
        'purchase_order_number',
        'supplier_name',
        'arrived_at',
        'is_assigned',
        'assigned_warehouse_id',
        'assigned_warehouse_name',
        'note',
        'assigned_at',
        'assigned_by',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'assigned_at' => 'datetime',
        'is_assigned' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'assigned_warehouse_id');
    }

    public function scopePending($query)
    {
        return $query->where('is_assigned', false);
    }
}
