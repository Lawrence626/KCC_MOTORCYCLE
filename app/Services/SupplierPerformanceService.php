<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\ReverseLogistics;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SupplierPerformanceService
{
    /**
     * Calculate performance metrics for a supplier.
     *
     * @param Supplier|null $supplier
     * @param Collection $products
     * @param Collection $orders
     * @param Collection|null $arrivalNotices
     * @param Collection|null $reverseLogistics
     * @param Collection|null $priceHistories
     * @return array
     */
    public function calculateForSupplier(
        ?Supplier $supplier,
        Collection $products,
        Collection $orders,
        ?Collection $arrivalNotices = null,
        ?Collection $reverseLogistics = null,
        ?Collection $priceHistories = null
    ): array {
        $supplierId = $supplier?->id;
        $supplierName = $supplier?->name;

        $productIds = $products->pluck('id')->filter()->unique()->values()->all();
        $productSkus = $products->pluck('sku')->filter()->unique()->values()->all();

        // 1. Completed / Received Orders filter
        $receivedStatuses = ['completed', 'partially received', 'delivered'];
        $completedOrders = $orders->filter(function ($order) use ($receivedStatuses) {
            return in_array(strtolower((string) $order->status), $receivedStatuses, true);
        });

        $ordersCount = $orders->count();
        $completedCount = $completedOrders->count();

        // If arrival notices were not passed, fetch them for completed orders
        if ($arrivalNotices === null) {
            $poIds = $completedOrders->pluck('id')->filter()->all();
            $poNumbers = $completedOrders->pluck('order_number')->filter()->all();
            $arrivalNotices = StockArrivalNotice::query()
                ->where(function ($q) use ($poIds, $poNumbers) {
                    if (!empty($poIds)) {
                        $q->whereIn('purchase_order_id', $poIds);
                    }
                    if (!empty($poNumbers)) {
                        $q->orWhereIn('purchase_order_number', $poNumbers);
                    }
                })
                ->get();
        }

        $arrivalsByPoId = $arrivalNotices->groupBy('purchase_order_id');
        $arrivalsByPoNumber = $arrivalNotices->groupBy('purchase_order_number');

        // 2. On-Time Delivery %
        // completed orders delivered on/before Expected Delivery Date ÷ completed orders × 100
        $onTimeCount = 0;
        foreach ($completedOrders as $order) {
            $receivedDate = $this->resolveReceivedDate($order, $arrivalsByPoId, $arrivalsByPoNumber);
            $order->received_date = $receivedDate ? Carbon::parse($receivedDate)->toDateString() : null;

            $expectedDelivery = $order->estimated_delivery_date ?? $order->expected_delivery_date;
            if ($expectedDelivery && $receivedDate) {
                $expectedDateStr = Carbon::parse($expectedDelivery)->toDateString();
                $receivedDateStr = Carbon::parse($receivedDate)->toDateString();

                if ($receivedDateStr <= $expectedDateStr) {
                    $onTimeCount++;
                }
            }
        }

        $onTimeRate = $completedCount > 0 ? ($onTimeCount / $completedCount) * 100 : 0.0;

        // 3. Order Completion %
        // total quantity received ÷ total quantity ordered × 100 (accounts for backorders/short deliveries)
        $totalQuantityOrdered = 0;
        $totalQuantityReceived = 0;

        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                $totalQuantityOrdered += (int) $item->quantity;
                $totalQuantityReceived += (int) ($item->received_quantity ?? 0);
            }
        }

        $orderCompletionRate = $totalQuantityOrdered > 0
            ? min(100.0, ($totalQuantityReceived / $totalQuantityOrdered) * 100)
            : 0.0;

        // 4. Defect Rate %
        // defective/returned quantity ÷ total received quantity × 100
        $poItemsDefective = 0;
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                $poItemsDefective += (int) ($item->defective_quantity ?? 0);
            }
        }

        $rlDefectiveQuantity = 0;
        if (!empty($productIds) || !empty($productSkus)) {
            if ($reverseLogistics !== null) {
                $rlDefectiveQuantity = $reverseLogistics->filter(function ($rl) use ($productIds, $productSkus) {
                    $matchesProduct = ($rl->product_id && in_array($rl->product_id, $productIds, true))
                        || ($rl->sku && in_array($rl->sku, $productSkus, true));
                    return $matchesProduct;
                })->sum('quantity');
            } else {
                $rlDefectiveQuantity = ReverseLogistics::query()
                    ->where(function ($q) use ($productIds, $productSkus) {
                        if (!empty($productIds)) {
                            $q->whereIn('product_id', $productIds);
                        }
                        if (!empty($productSkus)) {
                            $q->orWhereIn('sku', $productSkus);
                        }
                    })
                    ->sum('quantity');
            }
        }

        $defectiveQuantity = max($poItemsDefective, (int) $rlDefectiveQuantity);

        $defectRate = $totalQuantityReceived > 0
            ? min(100.0, ($defectiveQuantity / $totalQuantityReceived) * 100)
            : 0.0;

        $qualityScore = $completedCount > 0
            ? max(0.0, 100.0 - $defectRate)
            : 0.0;

        // 5. Price Stability %
        if ($priceHistories === null) {
            $priceHistories = SupplierPriceHistory::query()
                ->where(function ($q) use ($supplierId, $productIds) {
                    if ($supplierId) {
                        $q->where('supplier_id', $supplierId);
                    }
                    if (!empty($productIds)) {
                        $q->orWhereIn('product_id', $productIds);
                    }
                })
                ->get();
        } else {
            $priceHistories = $priceHistories->filter(function ($h) use ($supplierId, $productIds) {
                return ($supplierId && $h->supplier_id == $supplierId)
                    || ($h->product_id && in_array($h->product_id, $productIds, true));
            });
        }

        if ($priceHistories->isNotEmpty()) {
            $avgPriceChange = (float) $priceHistories->avg(fn ($h) => abs((float) ($h->change_percentage ?? 0)));
            $priceStability = max(0.0, min(100.0, 100.0 - $avgPriceChange));
        } else {
            $priceStability = ($completedCount > 0 || $products->isNotEmpty()) ? 100.0 : 0.0;
        }

        // 6. Performance Score / 100
        // On-Time Delivery 40% + Order Completion 30% + Quality (100 - Defect Rate) 20% + Price Stability 10%
        if ($completedCount > 0) {
            $rawScore = ($onTimeRate * 0.40)
                + ($orderCompletionRate * 0.30)
                + ($qualityScore * 0.20)
                + ($priceStability * 0.10);
            $performanceScore = (int) max(0, min(100, round($rawScore)));
        } else {
            $performanceScore = 0;
        }

        return [
            'orders_count' => $ordersCount,
            'delivered_orders_count' => $completedCount,
            'on_time_deliveries' => $onTimeCount,
            'on_time_rate' => (int) round($onTimeRate),
            'on_time_rate_precise' => round($onTimeRate, 2),
            'completion_rate' => (int) round($orderCompletionRate),
            'completion_rate_precise' => round($orderCompletionRate, 2),
            'total_quantity_ordered' => $totalQuantityOrdered,
            'total_quantity_received' => $totalQuantityReceived,
            'defective_quantity' => (int) $defectiveQuantity,
            'defect_rate' => round($defectRate, 2),
            'quality_score' => (int) round($qualityScore),
            'price_stability' => (int) round($priceStability),
            'price_stability_precise' => round($priceStability, 2),
            'performance_score' => $performanceScore,
            'orders' => $orders->map(function ($order) use ($arrivalsByPoId, $arrivalsByPoNumber) {
                $receivedDate = $this->resolveReceivedDate($order, $arrivalsByPoId, $arrivalsByPoNumber);
                
                $statusLower = strtolower((string) $order->status);
                $isReceivedOrCompleted = in_array($statusLower, ['completed', 'delivered', 'partially received', 'awaiting confirmation'], true);

                if ($isReceivedOrCompleted && $receivedDate) {
                    $sortTimestamp = $receivedDate->getTimestamp();
                } else {
                    $activityDate = $order->updated_at ?? $order->created_at;
                    $sortTimestamp = $activityDate ? Carbon::parse($activityDate)->getTimestamp() : 0;
                }

                $deliveryDate = $order->estimated_delivery_date ?? $order->expected_delivery_date;
                $expectedDateFormatted = $deliveryDate
                    ? Carbon::parse($deliveryDate)->format('M j, Y')
                    : null;

                $receivedDateFormatted = $receivedDate
                    ? Carbon::parse($receivedDate)->format('M j, Y')
                    : ($order->completed_at ? Carbon::parse($order->completed_at)->format('M j, Y') : null);

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => ucwords($order->status),
                    'expected_delivery_date' => $expectedDateFormatted,
                    'completed_at' => optional($order->completed_at)->format('M j, Y'),
                    'received_date' => $receivedDateFormatted,
                    'updated_at' => optional($order->updated_at)->format('M j, Y'),
                    'created_at' => optional($order->created_at)->format('M j, Y'),
                    'sort_timestamp' => $sortTimestamp,
                    'total_amount' => (float) $order->total_amount,
                ];
            })
            ->sortByDesc('sort_timestamp')
            ->values(),
        ];
    }

    /**
     * Resolve the actual received date for an order.
     * Prefers completed_at, then StockArrivalNotice arrival dates, then updated_at if completed.
     */
    public function resolveReceivedDate(
        PurchaseOrder $order,
        Collection $arrivalsByPoId,
        Collection $arrivalsByPoNumber
    ): ?Carbon {
        if ($order->completed_at) {
            return Carbon::parse($order->completed_at);
        }

        $poArrivals = $arrivalsByPoId->get($order->id)
            ?? $arrivalsByPoNumber->get($order->order_number);

        if ($poArrivals && $poArrivals->isNotEmpty()) {
            $maxArrivedAt = $poArrivals->max('arrived_at');
            if ($maxArrivedAt) {
                return Carbon::parse($maxArrivedAt);
            }
        }

        if (in_array(strtolower((string) $order->status), ['completed', 'delivered'], true) && $order->updated_at) {
            return Carbon::parse($order->updated_at);
        }

        return null;
    }
}
