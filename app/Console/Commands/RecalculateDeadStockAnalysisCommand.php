<?php

namespace App\Console\Commands;

use App\Services\DeadStockDetectionService;
use App\Services\SalesVelocityAnalysisService;
use App\Services\DSSRecommendationEngineService;
use Illuminate\Console\Command;

class RecalculateDeadStockAnalysisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dss:analyze
                            {--force : Force recalculation even if recent}
                            {--products= : Only analyze specific products (comma-separated IDs)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate dead stock analysis, sales velocity, and generate recommendations';

    protected DeadStockDetectionService $detectionService;
    protected SalesVelocityAnalysisService $velocityService;
    protected DSSRecommendationEngineService $recommendationService;

    public function __construct(
        DeadStockDetectionService $detectionService,
        SalesVelocityAnalysisService $velocityService,
        DSSRecommendationEngineService $recommendationService
    ) {
        parent::__construct();
        $this->detectionService = $detectionService;
        $this->velocityService = $velocityService;
        $this->recommendationService = $recommendationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting DSS Analysis...');
        $this->newLine();

        // Phase 1: Detect Dead Stocks
        $this->info('📊 Phase 1: Analyzing dead stock...');
        $startTime = microtime(true);
        
        $this->detectionService->analyzeAllProducts();
        
        $duration = round(microtime(true) - $startTime, 2);
        $this->info("✓ Dead stock analysis completed in {$duration}s");

        // Phase 2: Analyze Sales Velocity
        $this->info('📈 Phase 2: Analyzing sales velocity...');
        $startTime = microtime(true);
        
        $this->velocityService->analyzeAllProducts();
        
        $duration = round(microtime(true) - $startTime, 2);
        $this->info("✓ Sales velocity analysis completed in {$duration}s");

        // Phase 3: Generate Recommendations
        $this->info('💡 Phase 3: Generating recommendations...');
        $startTime = microtime(true);
        
        $this->recommendationService->generateAllRecommendations();
        
        $duration = round(microtime(true) - $startTime, 2);
        $this->info("✓ Recommendations generated in {$duration}s");

        $this->newLine();
        $this->info('✅ DSS Analysis completed successfully!');
    }
}
