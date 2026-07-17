<?php

namespace App\Http\Controllers;

use App\Models\DSSRecommendation;
use App\Models\DeadStock;
use App\Services\DSSRecommendationEngineService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DSSRecommendationController extends Controller
{
    protected DSSRecommendationEngineService $recommendationService;

    public function __construct(DSSRecommendationEngineService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display all active recommendations.
     */
    public function index(Request $request): View
    {
        $query = DSSRecommendation::where('is_active', true);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('recommendation_type', $request->get('type'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        // Filter by status
        if ($request->get('status') === 'pending') {
            $query->whereNull('action_taken_at');
        } elseif ($request->get('status') === 'actioned') {
            $query->whereNotNull('action_taken_at');
        }

        $recommendations = $query->with('product')
            ->orderBy('priority', 'desc')
            ->orderBy('generated_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => DSSRecommendation::where('is_active', true)->count(),
            'pending' => DSSRecommendation::where('is_active', true)->whereNull('action_taken_at')->count(),
            'actioned' => DSSRecommendation::where('is_active', true)->whereNotNull('action_taken_at')->count(),
        ];

        return view('dss.recommendations.index', [
            'recommendations' => $recommendations,
            'stats' => $stats,
        ]);
    }

    /**
     * Display a specific recommendation.
     */
    public function show(int $id): View
    {
        $recommendation = DSSRecommendation::findOrFail($id);
        $deadStock = DeadStock::where('product_id', $recommendation->product_id)
            ->where('is_active', true)
            ->first();

        return view('dss.recommendations.show', [
            'recommendation' => $recommendation,
            'deadStock' => $deadStock,
        ]);
    }

    /**
     * Mark recommendation as actioned.
     */
    public function markActioned(int $id, Request $request)
    {
        $request->validate([
            'action_notes' => 'nullable|string|max:1000',
        ]);

        $this->recommendationService->markAsActioned($id, $request->input('action_notes'));

        return redirect()->back()->with('success', 'Recommendation marked as actioned.');
    }

    /**
     * Get recommendations by product ID.
     */
    public function getByProduct(int $productId)
    {
        $recommendations = DSSRecommendation::where('product_id', $productId)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json($recommendations);
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
}
