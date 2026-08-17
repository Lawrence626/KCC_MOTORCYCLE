<?php

namespace App\Services;

use App\Models\InventoryNotification;
use App\Models\Product;

class InventoryAlertService
{
    /**
     * Scan all active products and create / update / resolve inventory notifications.
     * Prevents duplicates by upserting on (product_id + active status).
     */
    public function syncAlerts(): void
    {
        // Immediately remove active notifications for archived or inactive products
        InventoryNotification::whereHas('product', function ($q) {
            $q->where('is_archived', true)->orWhere('is_active', false);
        })->orWhereDoesntHave('product')->delete();

        $products = Product::query()
            ->where('is_active', true)
            ->where('is_archived', false)
            ->get();

        foreach ($products as $product) {
            $this->syncProductAlert($product);
        }
    }

    /**
     * Remove all notifications for a given product (e.g. when archived).
     */
    public function removeProductAlerts(int $productId): void
    {
        InventoryNotification::where('product_id', $productId)->delete();
    }

    /**
     * Sync alert state for a single product.
     */
    public function syncProductAlert(Product $product): void
    {
        // If product is archived or inactive, remove any alerts immediately
        if ($product->is_archived || ! $product->is_active) {
            $this->removeProductAlerts($product->id);
            return;
        }

        $stock = (int) $product->stock_quantity;
        $reorderLevel = (int) $product->reorder_level;

        // Determine the notification type (if any)
        if ($stock <= 0) {
            $type = 'out_of_stock';
        } elseif ($stock <= $reorderLevel) {
            $type = 'low_stock';
        } else {
            // Stock is healthy — resolve any active alerts
            $this->resolveProductAlerts($product->id);
            return;
        }

        // Find existing active notification for this product
        $existing = InventoryNotification::where('product_id', $product->id)
            ->active()
            ->first();

        if ($existing) {
            $typeChanged = $existing->notification_type !== $type;
            $stockDecreased = $stock < (int) $existing->current_stock;
            
            $updateData = [
                'sku' => $product->sku,
                'notification_type' => $type,
                'current_stock' => $stock,
                'reorder_point' => $reorderLevel,
            ];

            if ($typeChanged || $stockDecreased) {
                $updateData['status'] = 'unread';
                $updateData['dismissed_at'] = null;
                $updateData['created_at'] = now();
            }

            $existing->forceFill($updateData)->save();
        } else {
            // Create new notification
            InventoryNotification::create([
                'product_id' => $product->id,
                'sku' => $product->sku,
                'notification_type' => $type,
                'current_stock' => $stock,
                'reorder_point' => $reorderLevel,
                'status' => 'unread',
            ]);
        }
    }

    /**
     * Get alerts visible on the dashboard (active + not dismissed), newest first.
     */
    public function getDashboardAlerts(): \Illuminate\Database\Eloquent\Collection
    {
        return InventoryNotification::dashboard()
            ->newestFirst()
            ->with('product')
            ->get();
    }

    public function getNotificationCenterItems(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return InventoryNotification::whereHas('product', function ($q) {
                $q->where('is_archived', false)->where('is_active', true);
            })
            ->orderByRaw("CASE WHEN status = 'resolved' THEN 1 ELSE 0 END")
            ->orderByDesc('created_at')
            ->with('product')
            ->limit($limit)
            ->get();
    }

    /**
     * Get paginated notifications for "View All" page.
     */
    public function getNotificationCenterPaginated(int $perPage = 20)
    {
        return InventoryNotification::whereHas('product', function ($q) {
                $q->where('is_archived', false)->where('is_active', true);
            })
            ->orderByRaw("CASE WHEN status = 'resolved' THEN 1 ELSE 0 END")
            ->orderByDesc('created_at')
            ->with('product')
            ->paginate($perPage);
    }

    /**
     * Dismiss an alert from the dashboard only. Keeps it in notification center.
     */
    public function dismissAlert(int $notificationId): bool
    {
        $notification = InventoryNotification::find($notificationId);

        if ($notification) {
            return $notification->dismiss();
        }

        return false;
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = InventoryNotification::find($notificationId);

        if ($notification) {
            return $notification->markAsRead();
        }

        return false;
    }

    /**
     * Get count of unread notifications (for bell badge).
     */
    public function getUnreadCount(): int
    {
        return InventoryNotification::unread()->count();
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): int
    {
        return InventoryNotification::unread()
            ->update(['status' => 'read']);
    }

    /**
     * Resolve all active alerts for a given product.
     * Called when stock has been replenished above reorder level.
     */
    public function resolveProductAlerts(int $productId): void
    {
        InventoryNotification::where('product_id', $productId)
            ->active()
            ->get()
            ->each(function (InventoryNotification $notification) {
                $notification->markAsResolved();
            });
    }

    /**
     * Check a specific product's stock and resolve if replenished.
     * Typically called after receiving a purchase order.
     */
    public function checkAndResolveProduct(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $stock = (int) $product->stock_quantity;
        $reorderLevel = (int) $product->reorder_level;

        if ($stock > $reorderLevel) {
            $this->resolveProductAlerts($productId);
        } else {
            // Stock still low/out — update existing alert with current stock
            $this->syncProductAlert($product);
        }
    }

    /**
     * Format a notification for JSON API response.
     */
    public function formatNotification(InventoryNotification $notification): array
    {
        $product = $notification->product;
        $productName = $product ? ($product->product_name ?? $product->name) : 'Unknown Product';

        return [
            'id' => $notification->id,
            'product_id' => $notification->product_id,
            'product_name' => $productName,
            'sku' => $notification->sku,
            'notification_type' => $notification->notification_type,
            'alert_type_label' => $notification->alert_type_label,
            'alert_priority' => $notification->alert_priority,
            'current_stock' => $notification->current_stock,
            'reorder_point' => $notification->reorder_point,
            'status' => $notification->status,
            'dismissed_at' => $notification->dismissed_at?->toISOString(),
            'resolved_at' => $notification->resolved_at?->toISOString(),
            'created_at' => $notification->created_at->toISOString(),
            'updated_at' => $notification->updated_at->toISOString(),
            'order_url' => route('order.create', ['product_id' => $notification->product_id]),
        ];
    }

    /**
     * Format a collection of notifications for JSON API response.
     */
    public function formatNotifications($notifications): array
    {
        return $notifications->map(fn ($n) => $this->formatNotification($n))->values()->all();
    }
}
