<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DSSRecommendation;
use App\Services\DSSRecommendationEngineService;
use Illuminate\Http\Request;

class DSSRecommendationApiController extends Controller
{
    protected DSSRecommendationEngineService $recommendationService;

    public function __construct(DSSRecommendationEngineService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get recommendations for a product.
     */
    public function getByProduct(int $productId)
    {
        $recommendations = $this->recommendationService->getProductRecommendations($productId);

        return response()->json($recommendations);
    }

    /**
     * Get pending recommendations.
     */
    public function getPending(Request $request)
    {
        $limit = $request->get('limit', 50);

        $recommendations = $this->recommendationService->getPendingRecommendations($limit);

        return response()->json($recommendations);
    }

    /**
     * Get recommendations by type.
     */
    public function getByType(string $type, Request $request)
    {
        $validTypes = [
            'reorder_level',
            'inventory_reorder',
            'normal_stock',
            'low_sales_review',
            'promotion',
            'discount',
            'bundle',
            'relocate',
            'featured_display',
            'social_media',
            'supplier_return'
        ];

        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid recommendation type'], 400);
        }

        $recommendations = DSSRecommendation::where('is_active', true)
            ->where('recommendation_type', $type)
            ->with('product')
            ->orderBy('priority', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($recommendations);
    }

    /**
     * Mark recommendation as actioned.
     */
    public function markActioned(int $id, Request $request)
    {
        $request->validate([
            'action_notes' => 'nullable|string|max:1000',
        ]);

        $success = $this->recommendationService->markAsActioned($id, $request->input('action_notes'));

        if (!$success) {
            return response()->json(['error' => 'Recommendation not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Recommendation marked as actioned.',
        ]);
    }

    /**
     * Apply suggested reorder level.
     */
    public function applyReorderLevel(int $id)
    {
        $success = $this->recommendationService->applyReorderLevel($id);

        if (!$success) {
            return response()->json(['error' => 'Unable to apply reorder level for this recommendation.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Suggested reorder level successfully applied.',
        ]);
    }

    /**
     * Recalculate DSS recommendations.
     */
    public function recalculate()
    {
        $this->recommendationService->generateAllRecommendations();

        return response()->json([
            'success' => true,
            'message' => 'DSS recommendations successfully recalculated.',
        ]);
    }

    /**
     * Get pending recommendations count.
     */
    public function pendingCount()
    {
        $count = DSSRecommendation::where('is_active', true)
            ->whereNull('action_taken_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recommendations count by type.
     */
    public function countByType()
    {
        $counts = DSSRecommendation::where('is_active', true)
            ->selectRaw('recommendation_type, COUNT(*) as count')
            ->groupBy('recommendation_type')
            ->pluck('count', 'recommendation_type')
            ->toArray();

        return response()->json($counts);
    }
}
