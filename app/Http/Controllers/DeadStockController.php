<?php

namespace App\Http\Controllers;

use App\Models\DeadStock;
use App\Models\DSSSettings;
use App\Models\DSSRecommendation;
use App\Models\FastMovingProduct;
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
     */
    public function index(Request $request): View
    {
        $countByPriority = $this->detectionService->getCountByPriority();
        $totalDeadStocks = $this->detectionService->getTotalCount();
        $totalValue = $this->detectionService->getTotalValue();
        $thresholdDays = $this->detectionService->getThresholdDays();

        // Get all dead stocks with full details, search, filter, sort
        $deadStocks = $this->detectionService->getDeadStocksWithDetails(
            search: $request->get('search'),
            priority: $request->get('priority'),
            sortBy: $request->get('sort_by', 'days_without_sale'),
            sortOrder: $request->get('sort_order', 'desc'),
            perPage: $request->get('per_page', 25)
        );

        // Get at-risk products
        $atRiskProducts = $this->detectionService->getAtRiskProducts();

        // Get fast moving products for bundle suggestions
        $fastMovingProducts = FastMovingProduct::orderBy('velocity_score', 'desc')
            ->limit(10)
            ->with('product')
            ->get();

        return view('dead-stock.index', [
            'countByPriority' => $countByPriority,
            'totalDeadStocks' => $totalDeadStocks,
            'totalValue' => $totalValue,
            'thresholdDays' => $thresholdDays,
            'deadStocks' => $deadStocks,
            'atRiskProducts' => $atRiskProducts,
            'fastMovingProducts' => $fastMovingProducts,
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

        // Get fast moving products for bundle modal
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
     * Recalculate all dead stocks.
     */
    public function recalculate(Request $request)
    {
        $this->detectionService->analyzeAllProducts();
        $this->velocityService->analyzeAllProducts();
        $this->recommendationService->generateAllRecommendations();

        return redirect()->back()->with('success', 'Dead stock analysis recalculated successfully.');
    }

    /**
     * Mark a dead stock as resolved.
     */
    public function markResolved(int $id, Request $request)
    {
        $deadStock = DeadStock::findOrFail($id);
        $deadStock->update([
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'Dead stock marked as resolved.');
    }

    /**
     * Apply a discount to a dead stock product.
     */
    public function applyDiscount(int $id, Request $request)
    {
        $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $deadStock = DeadStock::with('product')->findOrFail($id);

        // Mark the discount recommendation as actioned
        $discountRecommendation = DSSRecommendation::where('product_id', $deadStock->product_id)
            ->where('recommendation_type', 'discount')
            ->where('is_active', true)
            ->first();

        $discountType = $request->input('discount_type');
        $discountValue = $request->input('discount_value');

        $notes = $discountType === 'percentage'
            ? "Applied {$discountValue}% discount to {$deadStock->product->name}"
            : "Applied ₱" . number_format($discountValue, 2) . " discount to {$deadStock->product->name}";

        if ($discountRecommendation) {
            $discountRecommendation->markAsActioned($notes);
        }

        return redirect()->back()->with('success', $notes);
    }

    /**
     * Export dead stocks as Excel (CSV format).
     */
    public function exportExcel(Request $request)
    {
        $query = DeadStock::where('is_active', true)->with('product');

        if ($request->filled('priority')) {
            $query->where('priority_level', $request->get('priority'));
        }

        $deadStocks = $query->orderBy('days_without_sale', 'desc')->get();

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
                'Last Sold Date', 'Days Since Last Sale', 'Suggested Action', 'Priority'
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
                    $ds->priority_level,
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
        $query = DeadStock::where('is_active', true)->with('product');

        if ($request->filled('priority')) {
            $query->where('priority_level', $request->get('priority'));
        }

        $deadStocks = $query->orderBy('days_without_sale', 'desc')->get();
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
            'countByPriority' => $this->detectionService->getCountByPriority(),
            'totalValue' => $this->detectionService->getTotalValue(),
            'thresholdDays' => $this->detectionService->getThresholdDays(),
        ]);
    }
}
