<?php

namespace App\Jobs;

use App\Services\DeadStockDetectionService;
use App\Services\SalesVelocityAnalysisService;
use App\Services\DSSRecommendationEngineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecalculateDeadStockAnalysisJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        DeadStockDetectionService $detectionService,
        SalesVelocityAnalysisService $velocityService,
        DSSRecommendationEngineService $recommendationService
    ): void
    {
        // Analyze products for dead stock
        $detectionService->analyzeAllProducts();

        // Analyze sales velocity
        $velocityService->analyzeAllProducts();

        // Generate recommendations
        $recommendationService->generateAllRecommendations();
    }
}
