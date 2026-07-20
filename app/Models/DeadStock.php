<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeadStock extends Model
{
    protected $table = 'dead_stocks';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'days_without_sale',
        'last_sold_date',
        'current_stock',
        'stock_value',
        'priority_level',
        'analysis_notes',
        'is_active',
        'detected_at',
        'last_analyzed_at',
    ];

    protected $casts = [
        'last_sold_date' => 'datetime',
        'detected_at' => 'datetime',
        'last_analyzed_at' => 'datetime',
        'is_active' => 'boolean',
        'current_stock' => 'integer',
        'days_without_sale' => 'integer',
        'stock_value' => 'decimal:2',
    ];

    /**
     * Get the product associated with this dead stock record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse associated with this dead stock record.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get recommendations for this dead stock product.
     */
    public function recommendations()
    {
        return $this->hasMany(DSSRecommendation::class, 'product_id', 'product_id')
            ->where('is_active', true)
            ->orderBy('priority', 'desc');
    }

    /**
     * Scope: Get only critical priority dead stocks.
     */
    public function scopeCritical($query)
    {
        return $query->where('priority_level', 'Critical');
    }

    /**
     * Scope: Get only high priority dead stocks.
     */
    public function scopeHigh($query)
    {
        return $query->where('priority_level', 'High');
    }

    /**
     * Scope: Get only medium priority dead stocks.
     */
    public function scopeMedium($query)
    {
        return $query->where('priority_level', 'Medium');
    }

    /**
     * Scope: Get only low priority dead stocks.
     */
    public function scopeLow($query)
    {
        return $query->where('priority_level', 'Low');
    }

    /**
     * Scope: Get only active dead stocks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the priority badge color.
     */
    public function getPriorityColor(): string
    {
        return match ($this->priority_level) {
            'Critical' => 'red',
            'High' => 'orange',
            'Medium' => 'yellow',
            'Low' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get the priority badge class for Bootstrap.
     */
    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority_level) {
            'Critical' => 'badge-danger',
            'High' => 'badge-warning',
            'Medium' => 'badge-info',
            'Low' => 'badge-primary',
            default => 'badge-secondary',
        };
    }
}
