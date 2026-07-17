<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeadStock;
use App\Models\DSSRecommendation;
use App\Models\FastMovingProduct;
use App\Services\DeadStockDetectionService;
use App\Services\SalesVelocityAnalysisService;
use App\Services\DSSRecommendationEngineService;
use Illuminate\Http\Request;

class DeadStockApiController extends Controller
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
     * Get list of dead stocks with filters.
     */
    public function index(Request $request)
    {
        $query = DeadStock::where('is_active', true);

        // Search by product name/sku
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('product_name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('compatibility', 'like', "%$search%");
            });
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority_level', $request->get('priority'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'days_without_sale');
        $sortOrder = $request->get('sort_order', 'desc');

        $validSortFields = ['days_without_sale', 'stock_value', 'priority_level', 'detected_at', 'current_stock', 'last_sold_date'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'days_without_sale';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $deadStocks = $query->with(['product', 'warehouse'])
            ->paginate($request->get('per_page', 20));

        return response()->json($deadStocks);
    }

    /**
     * Get dead stock detail.
     */
    public function show(int $id)
    {
        $deadStock = DeadStock::with(['product', 'warehouse', 'recommendations'])
            ->findOrFail($id);

        return response()->json($deadStock);
    }

    /**
     * Force recalculation of dead stocks.
     */
    public function recalculate(Request $request)
    {
        $this->detectionService->analyzeAllProducts();
        $this->velocityService->analyzeAllProducts();
        $this->recommendationService->generateAllRecommendations();

        return response()->json([
            'success' => true,
            'message' => 'Dead stock analysis recalculated successfully.',
        ]);
    }

    /**
     * Mark dead stock as resolved.
     */
    public function markResolved(int $id, Request $request)
    {
        $deadStock = DeadStock::findOrFail($id);
        $deadStock->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Dead stock marked as resolved.',
        ]);
    }

    /**
     * Get dashboard statistics.
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

    /**
     * Get dead stocks by priority.
     */
    public function getByPriority(string $priority)
    {
        $validPriorities = ['Critical', 'High', 'Medium', 'Low'];

        if (!in_array($priority, $validPriorities)) {
            return response()->json(['error' => 'Invalid priority'], 400);
        }

        $deadStocks = DeadStock::where('is_active', true)
            ->where('priority_level', $priority)
            ->with('product')
            ->orderBy('days_without_sale', 'desc')
            ->get();

        return response()->json($deadStocks);
    }

    /**
     * Export dead stocks as CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = DeadStock::where('is_active', true);

        if ($request->filled('priority')) {
            $query->where('priority_level', $request->get('priority'));
        }

        $deadStocks = $query->with('product')->get();

        $csv = "Product Description,Brand,Product Name,Compatible Model,SKU,Current Stock,Stock Value,Days Without Sale,Last Sold Date,Suggested Action,Priority\n";

        foreach ($deadStocks as $deadStock) {
            $product = $deadStock->product;
            $lastSoldDate = $deadStock->last_sold_date ? $deadStock->last_sold_date->format('Y-m-d') : 'Never';

            $csv .= sprintf(
                '"%s","%s","%s","%s","%s",%d,%.2f,%d,"%s","%s","%s"' . "\n",
                str_replace('"', '""', $product->description ?? ''),
                $product->brand ?? '',
                $product->product_name ?? $product->name ?? '',
                $product->compatibility ?? '',
                $product->sku ?? '',
                $deadStock->current_stock,
                $deadStock->stock_value,
                $deadStock->days_without_sale,
                $lastSoldDate,
                $deadStock->analysis_notes ?? 'Monitor',
                $deadStock->priority_level
            );
        }

        return response()->stream(
            function () use ($csv) {
                echo $csv;
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="dead-stocks-' . now()->format('Y-m-d-H-i-s') . '.csv"',
            ]
        );
    }

    /**
     * Get top fast moving products for bundle recommendations.
     */
    public function getTopFastMoving(Request $request)
    {
        $limit = $request->get('limit', 10);

        $fastMoving = FastMovingProduct::orderBy('velocity_score', 'desc')
            ->limit($limit)
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name ?? $item->product->product_name ?? '',
                    'sku' => $item->product->sku ?? '',
                    'brand' => $item->product->brand ?? '',
                    'velocity_score' => $item->velocity_score,
                    'units_sold_30_days' => $item->units_sold_30_days,
                ];
            });

        return response()->json($fastMoving);
    }

    /**
     * Get sales history for a specific dead stock product.
     */
    public function salesHistory(int $id)
    {
        $deadStock = DeadStock::findOrFail($id);
        $history = $this->detectionService->getProductSalesHistory($deadStock->product_id);

        return response()->json($history);
    }
}
