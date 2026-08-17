<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InventoryNotification extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'notification_type',
        'current_stock',
        'reorder_point',
        'status',
        'dismissed_at',
        'resolved_at',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'reorder_point' => 'integer',
        'dismissed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /* ───────────────────── Relationships ───────────────────── */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* ───────────────────── Scopes ───────────────────── */

    /**
     * Active notifications (not resolved) for non-archived, active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['unread', 'read'])
            ->whereHas('product', function (Builder $q) {
                $q->where('is_archived', false)->where('is_active', true);
            });
    }

    /**
     * Unread notifications only for non-archived, active products.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'unread')
            ->whereHas('product', function (Builder $q) {
                $q->where('is_archived', false)->where('is_active', true);
            });
    }

    /**
     * Dashboard-visible alerts: active AND not dismissed for non-archived, active products.
     */
    public function scopeDashboard(Builder $query): Builder
    {
        return $query->active()->whereNull('dismissed_at');
    }

    /**
     * Newest first ordering.
     */
    public function scopeNewestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /* ───────────────────── Helper Methods ───────────────────── */

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->status === 'unread') {
            return $this->update(['status' => 'read']);
        }

        return false;
    }

    /**
     * Mark notification as resolved.
     */
    public function markAsResolved(): bool
    {
        if ($this->status !== 'resolved') {
            return $this->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
        }

        return false;
    }

    /**
     * Dismiss from dashboard (keeps in notification center).
     */
    public function dismiss(): bool
    {
        if (is_null($this->dismissed_at)) {
            return $this->update(['dismissed_at' => now()]);
        }

        return false;
    }

    /* ───────────────────── Accessors ───────────────────── */

    /**
     * Human-readable alert type label.
     */
    public function getAlertTypeLabelAttribute(): string
    {
        return match ($this->notification_type) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            default => 'Unknown',
        };
    }

    /**
     * Alert priority level for visual indicators.
     */
    public function getAlertPriorityAttribute(): string
    {
        return match ($this->notification_type) {
            'out_of_stock' => 'critical',
            'low_stock' => 'warning',
            default => 'info',
        };
    }
}
