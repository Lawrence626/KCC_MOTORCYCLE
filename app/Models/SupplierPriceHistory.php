<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPriceHistory extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'purchase_order_id',
        'previous_cost',
        'supplier_cost',
        'change_percentage',
        'recommendation',
        'reason',
        'suggested_retail_price',
        'is_dismissed',
    ];

    protected $casts = [
        'previous_cost' => 'decimal:2',
        'supplier_cost' => 'decimal:2',
        'change_percentage' => 'decimal:2',
        'suggested_retail_price' => 'decimal:2',
        'is_dismissed' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
