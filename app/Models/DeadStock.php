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
     * Get automatic recommendation based primarily on Days Unsold.
     *
     * Rules:
     * 31–60 days:
     *   Recommendation: Apply a discount to increase sales.
     *   Suggested Discount: 5%
     *   Reason: Item has been unsold for (no. of days) days.
     * 61–90 days:
     *   Recommendation: Apply a discount to increase sales.
     *   Suggested Discount: 10%–15% discount.
     *   Reason: Item has been unsold for (no. of days) days.
     * 91 and more days:
     *   Recommendation: Apply a discount to increase sales.
     *   Suggested Discount: 20% discount.
     *   Reason: Item has been unsold for (no. of days) days.
     */
    public function getAutomaticRecommendation(): array
    {
        $days = (int) $this->days_without_sale;

        if ($days >= 91) {
            return [
                'recommendation' => 'Apply a discount to increase sales.',
                'suggested_discount' => '20% discount',
                'suggested_discount_value' => 20,
                'reason' => "Item has been unsold for {$days} days.",
                'badge_color' => 'bg-slate-100 text-slate-900 border-slate-200',
                'range' => '91+ days',
            ];
        } elseif ($days >= 61) {
            return [
                'recommendation' => 'Apply a discount to increase sales.',
                'suggested_discount' => '10%–15% discount',
                'suggested_discount_value' => 15,
                'reason' => "Item has been unsold for {$days} days.",
                'badge_color' => 'bg-slate-100 text-slate-900 border-slate-200',
                'range' => '61–90 days',
            ];
        } elseif ($days >= 31) {
            return [
                'recommendation' => 'Apply a discount to increase sales.',
                'suggested_discount' => '5%',
                'suggested_discount_value' => 5,
                'reason' => "Item has been unsold for {$days} days.",
                'badge_color' => 'bg-slate-100 text-slate-900 border-slate-200',
                'range' => '31–60 days',
            ];
        }

        return [
            'recommendation' => 'Monitor sales activity.',
            'suggested_discount' => 'Monitor',
            'suggested_discount_value' => 0,
            'reason' => "Item has been unsold for {$days} days.",
            'badge_color' => 'bg-slate-100 text-slate-900 border-slate-200',
            'range' => 'Under 31 days',
        ];
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
