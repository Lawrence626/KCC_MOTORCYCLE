<?php

namespace App\Listeners;

use App\Events\POSTransactionCompleted;
use App\Jobs\RecalculateDeadStockAnalysisJob;

class RecalculateDeadStockOnSale
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(POSTransactionCompleted $event): void
    {
        // Dispatch job to recalculate dead stock analysis
        // Use delay to prevent excessive recalculation if multiple transactions
        RecalculateDeadStockAnalysisJob::dispatch()->delay(now()->addSeconds(5));
    }
}
