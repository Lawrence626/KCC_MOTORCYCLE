<?php

namespace App\Http\Controllers;

use App\Models\DeadStock;
use App\Models\FastMovingProduct;
use App\Models\DSSSettings;
use App\Models\DSSRecommendation;
use App\Models\Product;
use App\Services\DeadStockDetectionService;
use App\Services\SalesVelocityAnalysisService;
use App\Services\DSSRecommendationEngineService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeadStockController extends Controller
{
    protected DeadStockDetectionService $detectionService;
    protected SalesVelocityAnalysisService $velocityService;
    protected DSSRecommendationEngineService $recommendationService;

    public function __construct(
        DeadStockDetectionService $detectionService,
        SalesVelocityAnalysisService $velocityService,
        DSSRecommendationEngineService $recommendationService
    ) {
        $this->detectionService = $detectionService;
        $this->velocityService = $velocityService;
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display the dead stock management page.
     * Automatically recalculates dead stock on every page load.
     */
    public function index(Request $request)
    {
        // Automatically recalculate dead stock analysis on full page load (skip on AJAX pagination for speed)
        if (! $request->ajax() && ! $request->wantsJson()) {
            $this->detectionService->analyzeAllProducts();
        }

        $totalDeadStocks = $this->detectionService->getTotalCount();
        $totalValue = $this->detectionService->getTotalValue();
        $thresholdDays = $this->detectionService->getThresholdDays();

        // 10 items per page by default
        $deadStocks = $this->detectionService->getDeadStocksWithDetails(
            search: $request->get('search'),
            sortBy: $request->get('sort_by', 'days_without_sale'),
            sortOrder: $request->get('sort_order', 'desc'),
            perPage: (int) $request->get('per_page', 10)
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('dead-stock.partials.table', [
                    'deadStocks' => $deadStocks,
                ])->render(),
            ]);
        }

        return view('dead-stock.index', [
            'totalDeadStocks' => $totalDeadStocks,
            'totalValue' => $totalValue,
            'thresholdDays' => $thresholdDays,
            'deadStocks' => $deadStocks,
        ]);
    }

    /**
     * Display a specific dead stock record with detailed analysis.
     */
    public function show(int $id): View
    {
        $deadStock = DeadStock::with(['product', 'warehouse'])->findOrFail($id);
        $recommendations = $this->recommendationService->getProductRecommendations($deadStock->product_id);

        // Get sales history
        $salesHistory = $this->detectionService->getProductSalesHistory($deadStock->product_id);

        // Get fast moving products for bundle suggestions
        $fastMovingProducts = FastMovingProduct::orderBy('velocity_score', 'desc')
            ->limit(10)
            ->with('product')
            ->get();

        return view('dead-stock.show', [
            'deadStock' => $deadStock,
            'recommendations' => $recommendations,
            'salesHistory' => $salesHistory,
            'fastMovingProducts' => $fastMovingProducts,
        ]);
    }

    /**
     * Apply a discount to a dead stock product.
     * Stores discount info on the product so the POS can auto-apply it at checkout.
     * Does NOT change the product's original unit_price.
     */
    public function applyDiscount(int $id, Request $request)
    {
        $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $deadStock = DeadStock::with('product')->findOrFail($id);
        $product = $deadStock->product;

        $discountType = $request->input('discount_type');
        $discountValue = (float) $request->input('discount_value');

        // Store the discount on the product (does NOT change unit_price)
        $product->discount_type = $discountType;
        $product->discount_value = $discountValue;
        $product->save();

        $productName = $product->description ?? $product->product_name ?? $product->name ?? 'Product';
        $notes = $discountType === 'percentage'
            ? "Applied {$discountValue}% discount to {$productName}"
            : "Applied ₱" . number_format($discountValue, 2) . " discount to {$productName}";

        // Update dead stock analysis notes
        $deadStock->analysis_notes = $notes;
        $deadStock->save();

        // Mark the discount recommendation as actioned or create record
        $discountRecommendation = DSSRecommendation::where('product_id', $deadStock->product_id)
            ->where('recommendation_type', 'discount')
            ->where('is_active', true)
            ->first();

        if ($discountRecommendation) {
            $discountRecommendation->markAsActioned($notes);
        } else {
            DSSRecommendation::create([
                'product_id' => $deadStock->product_id,
                'recommendation_type' => 'discount',
                'title' => 'Apply Discount',
                'description' => $notes,
                'priority' => 'Medium',
                'metadata' => [
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                ],
                'is_active' => false,
                'generated_at' => now(),
                'last_updated_at' => now(),
                'action_taken_at' => now(),
                'action_notes' => $notes,
            ]);
        }

        return redirect()->back()->with('success', $notes);
    }

    /**
     * Permanently remove discount from a product / dead stock item.
     */
    public function removeDiscount(Request $request)
    {
        $productId = $request->input('product_id');
        $productIds = (array) $request->input('product_ids', []);

        if ($productId) {
            $productIds[] = $productId;
        }

        $deadStockId = $request->input('dead_stock_id');
        if ($deadStockId) {
            $ds = DeadStock::find($deadStockId);
            if ($ds && $ds->product_id) {
                $productIds[] = $ds->product_id;
            }
        }

        $productIds = array_values(array_unique(array_filter($productIds)));

        if (empty($productIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No product specified to remove discount.',
            ], 400);
        }

        $products = Product::whereIn('id', $productIds)->get();

        foreach ($products as $product) {
            $product->discount_type = null;
            $product->discount_value = null;
            $product->save();

            // Reset notes on dead stock record if present
            $deadStock = DeadStock::where('product_id', $product->id)->first();
            if ($deadStock) {
                $deadStock->analysis_notes = 'Promotion';
                $deadStock->save();
            }

            // Mark any active discount recommendations as inactive
            DSSRecommendation::where('product_id', $product->id)
                ->where('recommendation_type', 'discount')
                ->update([
                    'is_active' => false,
                    'action_notes' => 'Discount removed permanently',
                    'last_updated_at' => now(),
                ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Discount removed permanently.',
                'product_ids' => $productIds,
            ]);
        }

        return redirect()->back()->with('success', 'Discount removed permanently.');
    }

    /**
     * Permanently remove discount by dead stock id.
     */
    public function removeDiscountByDeadStockId(int $id, Request $request)
    {
        $deadStock = DeadStock::with('product')->findOrFail($id);
        if ($deadStock->product) {
            $deadStock->product->discount_type = null;
            $deadStock->product->discount_value = null;
            $deadStock->product->save();
        }

        $deadStock->analysis_notes = 'Promotion';
        $deadStock->save();

        DSSRecommendation::where('product_id', $deadStock->product_id)
            ->where('recommendation_type', 'discount')
            ->update([
                'is_active' => false,
                'action_notes' => 'Discount removed permanently',
                'last_updated_at' => now(),
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Discount removed permanently.',
            ]);
        }

        return redirect()->back()->with('success', 'Discount removed permanently.');
    }

    /**
     * Export dead stocks as Excel (CSV format).
     */
    public function exportExcel(Request $request)
    {
        // Auto-recalculate before export to ensure fresh data
        $this->detectionService->analyzeAllProducts();

        $deadStocks = DeadStock::where('is_active', true)
            ->with('product')
            ->orderBy('days_without_sale', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="dead-stocks-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($deadStocks) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Product Description', 'Brand', 'Product Name', 'Compatible Model',
                'SKU', 'Warehouse', 'Current Stock', 'Stock Value (₱)',
                'Last Sold Date', 'Days Since Last Sale', 'Suggested Action'
            ]);

            foreach ($deadStocks as $ds) {
                $product = $ds->product;
                fputcsv($file, [
                    $product->description ?? '',
                    $product->brand ?? '',
                    $product->product_name ?? $product->name ?? '',
                    $product->compatibility ?? '',
                    $product->sku ?? '',
                    $ds->warehouse->name ?? 'Main',
                    $ds->current_stock,
                    number_format($ds->stock_value, 2),
                    $ds->last_sold_date ? $ds->last_sold_date->format('M d, Y') : 'Never',
                    $ds->days_without_sale,
                    $ds->analysis_notes ?? 'Monitor',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export dead stocks as PDF (simplified HTML-based for download).
     */
    public function exportPdf(Request $request)
    {
        // Auto-recalculate before export to ensure fresh data
        $this->detectionService->analyzeAllProducts();

        $deadStocks = DeadStock::where('is_active', true)
            ->with('product')
            ->orderBy('days_without_sale', 'desc')
            ->get();

        $thresholdDays = $this->detectionService->getThresholdDays();

        // Generate a print-friendly HTML that can be printed as PDF
        $html = view('dead-stock.export-pdf', [
            'deadStocks' => $deadStocks,
            'thresholdDays' => $thresholdDays,
            'generatedAt' => now()->format('M d, Y h:i A'),
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    /**
     * Get dead stock statistics for dashboard.
     */
    public function dashboardStats()
    {
        return response()->json([
            'total' => $this->detectionService->getTotalCount(),
            'totalValue' => $this->detectionService->getTotalValue(),
            'thresholdDays' => $this->detectionService->getThresholdDays(),
        ]);
    }
}

