<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_number',
        'supplier_id',
        'supplier_name',
        'user_id',
        'created_by_role',
        'status',
        'sync_status',
        'expected_delivery_date',
        'estimated_delivery_date',
        'notes',
        'rejection_reason',
        'total_amount',
        'approved_at',
        'rejected_at',
        'rejected_by',
        'sent_to_supplier_at',
        'in_transit_at',
        'completed_at',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'estimated_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sent_to_supplier_at' => 'datetime',
        'in_transit_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function ($po) {
            // Keep estimated_delivery_date and expected_delivery_date synchronized
            if ($po->isDirty('estimated_delivery_date') && !$po->isDirty('expected_delivery_date')) {
                $po->expected_delivery_date = $po->estimated_delivery_date;
            } elseif ($po->isDirty('expected_delivery_date') && !$po->isDirty('estimated_delivery_date')) {
                $po->estimated_delivery_date = $po->expected_delivery_date;
            } elseif ($po->estimated_delivery_date && !$po->expected_delivery_date) {
                $po->expected_delivery_date = $po->estimated_delivery_date;
            } elseif ($po->expected_delivery_date && !$po->estimated_delivery_date) {
                $po->estimated_delivery_date = $po->expected_delivery_date;
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function defectiveReturnRequests()
    {
        return $this->hasMany(DefectiveReturnRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
