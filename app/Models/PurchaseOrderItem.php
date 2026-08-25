<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_name',
        'sku',
        'quantity',
        'received_quantity',
        'defective_quantity',
        'accepted_quantity',
        'defect_reason',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'received_quantity' => 'integer',
        'defective_quantity' => 'integer',
        'accepted_quantity' => 'integer',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function defectiveReturnRequests()
    {
        return $this->hasMany(DefectiveReturnRequest::class);
    }
}
