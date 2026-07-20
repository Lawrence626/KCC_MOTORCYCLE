<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlowMovingProduct extends Model
{
    protected $table = 'slow_moving_products';

    protected $fillable = [
        'product_id',
        'days_without_sale',
        'last_sold_date',
        'units_sold_30_days',
        'units_sold_60_days',
        'units_sold_90_days',
        'current_stock',
        'stock_value',
        'velocity_score',
    ];

    protected $casts = [
        'last_sold_date' => 'datetime',
        'current_stock' => 'integer',
        'days_without_sale' => 'integer',
        'units_sold_30_days' => 'integer',
        'units_sold_60_days' => 'integer',
        'units_sold_90_days' => 'integer',
        'stock_value' => 'decimal:2',
        'velocity_score' => 'decimal:2',
    ];

    /**
     * Get the product associated with this record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: Get slow moving products ordered by velocity score (lowest first).
     */
    public function scopeOrderedByVelocity($query)
    {
        return $query->orderBy('velocity_score', 'asc');
    }
}
