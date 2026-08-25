<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DefectiveReturnRequest extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'purchase_order_item_id',
        'supplier_id',
        'supplier_name',
        'product_id',
        'product_name',
        'sku',
        'defective_quantity',
        'defect_reason',
        'warehouse',
        'status',
        'resolution',
        'resolution_notes',
        'resolved_at',
        'replacement_order_number',
        'replacement_purchase_order_id',
        'expected_replacement_date',
        'replacement_received_quantity',
        'replacement_received_at',
    ];

    protected $casts = [
        'defective_quantity' => 'integer',
        'replacement_received_quantity' => 'integer',
        'expected_replacement_date' => 'date',
        'resolved_at' => 'datetime',
        'replacement_received_at' => 'datetime',
    ];

    /* ───────────────────── Relationships ───────────────────── */

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function replacementPurchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'replacement_purchase_order_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /* ───────────────────── Scopes ───────────────────── */

    public function scopePendingResponse(Builder $query): Builder
    {
        return $query->where('status', 'Pending Supplier Response');
    }

    public function scopeActiveReplacements(Builder $query): Builder
    {
        return $query->where('resolution', 'Replacement')
            ->whereIn('status', ['Replacement Approved', 'Awaiting Replacement']);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'Completed');
    }
}
