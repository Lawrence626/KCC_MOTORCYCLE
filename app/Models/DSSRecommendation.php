<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DSSRecommendation extends Model
{
    protected $table = 'dss_recommendations';

    protected $fillable = [
        'product_id',
        'recommendation_type',
        'title',
        'description',
        'priority',
        'metadata',
        'is_active',
        'generated_at',
        'last_updated_at',
        'action_taken_at',
        'action_notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'generated_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'action_taken_at' => 'datetime',
    ];

    /**
     * Get the product associated with this recommendation.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: Get only active recommendations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get recommendations by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('recommendation_type', $type);
    }

    /**
     * Scope: Get recommendations by priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: Get pending recommendations (no action taken).
     */
    public function scopePending($query)
    {
        return $query->whereNull('action_taken_at');
    }

    /**
     * Mark recommendation as actioned.
     */
    public function markAsActioned(?string $notes = null): void
    {
        $this->update([
            'action_taken_at' => now(),
            'action_notes' => $notes,
        ]);
    }

    /**
     * Get recommendation type label.
     */
    public function getTypeLabel(): string
    {
        return match ($this->recommendation_type) {
            'reorder_level' => 'Reorder Level Adjustment',
            'inventory_reorder' => 'Reorder Level Adjustment',
            'normal_stock' => 'Normal Stock Maintenance',
            'low_sales_review' => 'Low Sales Stock Review',
            'promotion' => 'Promotional Campaign',
            'discount' => 'Price Reduction',
            'bundle' => 'Bundle Offer',
            'relocate' => 'Warehouse Relocation',
            'featured_display' => 'Featured Display',
            'social_media' => 'Social Media Campaign',
            'supplier_return' => 'Supplier Return',
            default => ucwords(str_replace('_', ' ', $this->recommendation_type)),
        };
    }

    /**
     * Check if this recommendation is a reorder level recommendation.
     */
    public function isReorderRecommendation(): bool
    {
        return in_array($this->recommendation_type, ['reorder_level', 'inventory_reorder']);
    }

    /**
     * Get suggested reorder level from metadata.
     */
    public function getSuggestedReorderLevel(): ?int
    {
        return isset($this->metadata['suggested_reorder_level']) ? (int) $this->metadata['suggested_reorder_level'] : null;
    }

    /**
     * Get suggested stock quantity from metadata.
     */
    public function getSuggestedStockQuantity(): ?int
    {
        return isset($this->metadata['suggested_stock_quantity']) ? (int) $this->metadata['suggested_stock_quantity'] : null;
    }

    /**
     * Check if this recommendation warns of stockout risk.
     */
    public function hasStockoutRisk(): bool
    {
        return ($this->metadata['stockout_risk'] ?? '') === 'high';
    }

    /**
     * Get recommended bundle products if applicable.
     */
    public function getBundleProducts()
    {
        if ($this->recommendation_type !== 'bundle' || !isset($this->metadata['bundle_product_ids'])) {
            return collect();
        }

        return Product::whereIn('id', $this->metadata['bundle_product_ids'])->get();
    }
}
