<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FastMovingProduct extends Model
{
    protected $table = 'fast_moving_products';

    protected $fillable = [
        'product_id',
        'units_sold_7_days',
        'units_sold_30_days',
        'units_sold_60_days',
        'units_sold_90_days',
        'velocity_score',
        'turnover_rate',
        'current_stock',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'units_sold_7_days' => 'integer',
        'units_sold_30_days' => 'integer',
        'units_sold_60_days' => 'integer',
        'units_sold_90_days' => 'integer',
        'velocity_score' => 'decimal:2',
        'turnover_rate' => 'decimal:2',
    ];

    /**
     * Get the product associated with this record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: Get fast moving products ordered by velocity score (highest first).
     */
    public function scopeOrderedByVelocity($query)
    {
        return $query->orderBy('velocity_score', 'desc');
    }

    /**
     * Scope: Get top fast moving products.
     */
    public function scopeTop($query, int $limit = 10)
    {
        return $query->orderBy('velocity_score', 'desc')->limit($limit);
    }
}
