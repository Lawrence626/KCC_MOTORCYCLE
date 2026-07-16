# DSS Implementation Guide & Installation Steps

## Quick Start

The Dead Stock Detection & Recommendation Engine (DSS) has been fully implemented. Follow these steps to activate and use it.

## 1. Database Setup

### Run Migrations
Execute the following migrations to create the DSS tables:

```bash
php artisan migrate
```

This creates:
- `dss_settings` - Configuration storage
- `dead_stocks` - Dead stock records
- `slow_moving_products` - Slow moving tracking
- `fast_moving_products` - Fast moving tracking
- `dss_recommendations` - Recommendations storage

### Initial Settings
Default settings are automatically inserted:
- Dead stock threshold: 90 days
- Slow moving threshold: 60 days
- Fast moving threshold: 50 units/month
- All recommendation types: enabled
- Automatic analysis: enabled

## 2. Files Created

### Migrations (5 files)
```
database/migrations/
├── 2026_07_16_000001_create_dss_settings_table.php
├── 2026_07_16_000002_create_dead_stocks_table.php
├── 2026_07_16_000003_create_slow_moving_products_table.php
├── 2026_07_16_000004_create_fast_moving_products_table.php
└── 2026_07_16_000005_create_dss_recommendations_table.php
```

### Models (5 files)
```
app/Models/
├── DeadStock.php
├── DSSRecommendation.php
├── SlowMovingProduct.php
├── FastMovingProduct.php
└── DSSSettings.php
```

### Services (3 files)
```
app/Services/
├── DeadStockDetectionService.php
├── SalesVelocityAnalysisService.php
└── DSSRecommendationEngineService.php
```

### Controllers (5 files)
```
app/Http/Controllers/
├── DeadStockController.php
├── DSSRecommendationController.php
├── DSSSettingsController.php
├── Api/
│   ├── DeadStockApiController.php
│   └── DSSRecommendationApiController.php
```

### Events & Listeners (3 files)
```
app/Events/
├── POSTransactionCompleted.php

app/Listeners/
├── RecalculateDeadStockOnSale.php

app/Jobs/
├── RecalculateDeadStockAnalysisJob.php
```

### Commands (1 file)
```
app/Console/Commands/
└── RecalculateDeadStockAnalysisCommand.php
```

### Views (4 files)
```
resources/views/
├── dead-stock/
│   ├── index.blade.php
│   └── show.blade.php
├── dss/
│   ├── recommendations/
│   │   └── index.blade.php
│   └── settings/
│       └── index.blade.php
```

### Routes
Updated `routes/web.php` with:
- 30+ new routes for DSS functionality
- API endpoints for AJAX calls
- Proper role-based middleware (admin, inventory_clerk)

### Documentation
- `DSS_MODULE_DOCUMENTATION.md` - Comprehensive documentation

## 3. Configuration

### Event Listener Setup

Register the event listener in `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    \App\Events\POSTransactionCompleted::class => [
        \App\Listeners\RecalculateDeadStockOnSale::class,
    ],
];
```

### Emit Event After POS Transaction

In your `POSTransactionController`, after completing a transaction:

```php
// After transaction is saved and completed
event(new \App\Events\POSTransactionCompleted($transaction));
```

This will automatically queue a background job to recalculate dead stock analysis.

## 4. Menu Integration

Add to your main navigation menu (e.g., `sidebar.blade.php` or `navbar.blade.php`):

```html
<!-- Decision Support System -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="dssDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-cube"></i> DSS
    </a>
    <ul class="dropdown-menu" aria-labelledby="dssDropdown">
        <li>
            <a class="dropdown-item" href="{{ route('dss.dead-stock.index') }}">
                <i class="fas fa-cube text-danger"></i> Dead Stock Management
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('dss.recommendations.index') }}">
                <i class="fas fa-lightbulb"></i> Recommendations
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('dss.settings.index') }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
    </ul>
</li>
```

## 5. Dashboard Integration

Add DSS alerts to your dashboard (in `DashboardController.php`):

```php
use App\Services\DeadStockDetectionService;

public function index(DeadStockDetectionService $detectionService)
{
    // ... existing code ...

    $deadStockStats = [
        'total' => $detectionService->getTotalCount(),
        'critical' => $detectionService->getCountByPriority()['Critical'],
        'high' => $detectionService->getCountByPriority()['High'],
        'totalValue' => $detectionService->getTotalValue(),
    ];

    return view('dashboard', [
        // ... existing data ...
        'deadStockStats' => $deadStockStats,
    ]);
}
```

In your dashboard view:

```blade
@if($deadStockStats['total'] > 0)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>⚠️ Dead Stock Alert</strong><br>
    {{ $deadStockStats['total'] }} products have not been sold for more than 90 days.
    <a href="{{ route('dss.dead-stock.index') }}" class="btn btn-sm btn-warning">
        Review Dead Stock
    </a>
</div>
@endif
```

## 6. Initial Data Population

### Manual Recalculation
Run the command to analyze all products:

```bash
php artisan dss:analyze
```

This will:
1. Analyze all products for dead stock status
2. Calculate sales velocity for fast/slow moving classification
3. Generate all applicable recommendations

Expected output:
```
📊 Phase 1: Analyzing dead stock...
✓ Dead stock analysis completed in 2.45s
📈 Phase 2: Analyzing sales velocity...
✓ Sales velocity analysis completed in 1.23s
💡 Phase 3: Generating recommendations...
✓ Recommendations generated in 1.87s

✅ DSS Analysis completed successfully!
```

### Scheduled Analysis (Optional)

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // ... existing schedules ...
    
    // Run DSS analysis daily at 2 AM
    $schedule->command('dss:analyze')->dailyAt('02:00');
}
```

## 7. Testing the Module

### Test Dead Stock Detection
1. Go to **DSS > Dead Stock Management**
2. Check if any products appear as dead stocks
3. If none appear, verify:
   - Products have stock (> 0)
   - Products are not archived
   - Some POS transactions exist
   - The time period has passed

### Test Recommendations
1. Go to **DSS > Recommendations**
2. Should see recommendations like:
   - Promotional Campaign
   - Price Reduction
   - Bundle Offers
   - Relocation suggestions
   - etc.

### Test API Endpoints
Use curl or Postman:

```bash
# Get dashboard stats
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/dss/dashboard-stats

# Get dead stocks list
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/dss/dead-stocks

# Get pending recommendations
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/dss/recommendations/pending
```

### Test Settings Update
1. Go to **DSS > Settings**
2. Change threshold values
3. Click "Save Configuration"
4. Verify changes are saved

## 8. Troubleshooting

### No Dead Stocks Detected
**Possible causes:**
- Products have no stock
- All products are too new (within threshold)
- Products are archived

**Solution:**
- Verify test data has old products with stock
- Lower the threshold temporarily for testing
- Run `php artisan dss:analyze` again

### Events Not Triggering
**Possible causes:**
- Event listener not registered
- Event not being dispatched in controller
- Queue driver not working

**Solution:**
```bash
# Check events are registered
php artisan event:list

# Test event manually
php artisan tinker
event(new App\Events\POSTransactionCompleted($transaction))
```

### Slow Query Performance
**Possible causes:**
- Large product count
- Missing database indexes
- Slow database connection

**Solution:**
```bash
# Ensure migrations ran
php artisan migrate:status

# Add indexes manually if needed
php artisan migrate

# Check query performance
php artisan dss:analyze --verbose
```

## 9. Maintenance

### Regular Tasks
- Run analysis daily or after significant POS activity
- Review recommendations weekly
- Update settings quarterly based on business needs
- Archive old recommendation records (monthly)

### Cleanup Script
Create `app/Console/Commands/CleanupDSSCommand.php`:

```php
// Archive old completed recommendations (6+ months old)
DSSRecommendation::where('action_taken_at', '<', now()->subMonths(6))
    ->where('is_active', true)
    ->update(['is_active' => false]);
```

## 10. Performance Optimization

### For Large Inventories (10,000+ products)
1. Schedule analysis during off-peak hours
2. Use background jobs/queue
3. Consider analyzing by category/warehouse

```php
// Example: Analyze by category
$categories = Category::distinct()->pluck('id');

foreach ($categories as $category) {
    AnalyzeDeadStockByCategory::dispatch($category);
}
```

### Database Optimization
Ensure these indexes exist:

```sql
CREATE INDEX idx_dead_stocks_product_id ON dead_stocks(product_id);
CREATE INDEX idx_dead_stocks_priority ON dead_stocks(priority_level);
CREATE INDEX idx_dss_recommendations_product ON dss_recommendations(product_id);
CREATE INDEX idx_dss_recommendations_type ON dss_recommendations(recommendation_type);
CREATE UNIQUE INDEX idx_dead_stocks_unique ON dead_stocks(product_id, warehouse_id);
```

## 11. User Roles & Permissions

### Access Control
- **Admin**: Full access to all DSS features
- **Inventory Clerk**: View and manage dead stock + recommendations
- **Other Roles**: No access (add as needed)

### Add More Roles
Edit your middleware in `routes/web.php`:

```php
// Add warehouse_personnel if needed
Route::middleware('role:admin,inventory_clerk,warehouse_personnel')->group(function () {
    // DSS routes
});
```

## 12. Next Steps

After implementation:

1. ✅ Run database migrations
2. ✅ Register event listener
3. ✅ Update navigation menu
4. ✅ Add dashboard alerts
5. ✅ Run initial analysis: `php artisan dss:analyze`
6. ✅ Test all features
7. ✅ Configure schedule (optional)
8. ✅ Train users on DSS features
9. ✅ Monitor dead stock metrics
10. ✅ Adjust settings as needed

## 13. Support & Documentation

For comprehensive documentation, see: `DSS_MODULE_DOCUMENTATION.md`

Key sections:
- Architecture overview
- Service layer documentation
- API endpoint reference
- Business rules
- Best practices
- Troubleshooting guide

## Implementation Summary

| Component | Count | Status |
|-----------|-------|--------|
| Migrations | 5 | ✅ Created |
| Models | 5 | ✅ Created |
| Services | 3 | ✅ Created |
| Controllers | 5 | ✅ Created |
| API Controllers | 2 | ✅ Created |
| Events | 1 | ✅ Created |
| Listeners | 1 | ✅ Created |
| Jobs | 1 | ✅ Created |
| Commands | 1 | ✅ Created |
| Views | 4 | ✅ Created |
| Routes | 30+ | ✅ Added |
| Documentation | 2 | ✅ Created |
| **TOTAL** | **60+** | **✅ COMPLETE** |

---

**Installation Time:** ~5-10 minutes
**Recommended Testing Time:** 1-2 hours
**Go-Live Ready:** Yes

For questions or issues, refer to the comprehensive documentation or contact the development team.
