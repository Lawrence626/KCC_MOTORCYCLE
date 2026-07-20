# Dead Stock Detection & Recommendation Engine (DSS)

## Overview

The Decision Support System (DSS) is an intelligent module that helps identify and manage dead stock inventory in the KCC Motorcycle Inventory Management System. It automatically analyzes product sales history and provides data-driven recommendations to help move slow-moving and dead stock items.

## Features

### 1. Dead Stock Detection
- **Automatic Classification**: Products are automatically classified as dead stock when they haven't been sold for a configurable period (default: 90 days)
- **Priority Levels**: 
  - 🔴 **Critical** (180+ days) - Immediate action required
  - 🟠 **High** (120-179 days) - Urgent action needed
  - 🟡 **Medium** (90-119 days) - Action recommended
  - 🔵 **Low** (60-89 days) - Monitor closely

### 2. Sales Velocity Analysis
- **Fast Moving Products**: Identified for bundle recommendations
- **Slow Moving Products**: Tracked separately for analysis
- **Velocity Scoring**: Products ranked by sales speed

### 3. Intelligent Recommendations
The system generates multiple recommendation types for each dead stock item:

#### a) **Promotional Campaign**
- Suggests creating marketing campaigns
- Priority increases with age of dead stock

#### b) **Price Reduction (Discount)**
- Recommends discount percentages (10-20%)
- Higher discounts for older inventory
- Data-driven percentage suggestions

#### c) **Bundle Offers**
- Recommends pairing with fast-moving products
- Uses sales velocity analysis to select partners
- Creates value perception and increases sales velocity

#### d) **Warehouse Relocation**
- Suggests moving to high-demand locations
- Available for items unsold 120+ days
- Improves visibility and accessibility

#### e) **Featured Display**
- Place in prominent store locations
- Near cashier or entrance positioning
- Increases customer interaction

#### f) **Social Media Campaign**
- Advertise on Facebook/Instagram
- Reach broader customer base
- Drive online sales

#### g) **Supplier Return**
- For critical-priority items only (180+ days)
- Frees up cash and warehouse space
- Conditional on supplier agreements

## Architecture

### Database Schema

#### `dss_settings` Table
Stores configurable thresholds and feature flags

#### `dead_stocks` Table
Tracks dead stock records with:
- Product identification
- Sales analysis (days without sale, last sold date)
- Stock value tracking
- Priority classification
- Detected and analyzed timestamps

#### `slow_moving_products` Table
Caches slow-moving product data for quick analysis

#### `fast_moving_products` Table
Caches fast-moving product data for recommendations

#### `dss_recommendations` Table
Stores all generated recommendations with:
- Recommendation type
- Priority level
- Detailed descriptions
- Metadata (bundle products, discount ranges, etc.)
- Action tracking (when and how recommendation was acted upon)

### Services Layer

#### `DeadStockDetectionService`
- Core dead stock detection logic
- Analyzes all products for dead stock criteria
- Calculates priority levels
- Provides statistics and counts

**Key Methods:**
```php
analyzeAllProducts()        // Analyze all products
checkProductForDeadStock()  // Check single product
getLastSaleDate()           // Get product's last sale date
determinePriorityLevel()    // Calculate priority
getCountByPriority()        // Get statistics by priority
getTotalCount()             // Total dead stock count
getTotalValue()             // Total value at risk
clearDeadStock()            // Mark as resolved
getAtRiskProducts()         // Products approaching threshold
```

#### `SalesVelocityAnalysisService`
- Analyzes sales velocity over different periods (7, 30, 60, 90 days)
- Classifies products as fast/slow moving
- Calculates turnover rates

**Key Methods:**
```php
analyzeAllProducts()            // Analyze all products
analyzeProductVelocity()        // Analyze single product
getTopFastMovingProducts()      // Get fast movers for bundles
getTopSlowMovingProducts()      // Get slow movers for analysis
```

#### `DSSRecommendationEngineService`
- Generates recommendations using business rules
- No external AI API calls
- Uses predefined logic based on:
  - Days without sales
  - Inventory quantity
  - Stock value
  - Sales history
  - Fast/slow moving status

**Key Methods:**
```php
generateAllRecommendations()        // Generate for all dead stocks
generateProductRecommendations()    // Generate for single product
getProductRecommendations()         // Get active recommendations
getPendingRecommendations()         // Get unactioned recommendations
markAsActioned()                    // Track when acted upon
```

### Controllers

#### `DeadStockController`
Manages user interface for dead stock management

#### `DSSRecommendationController`
Manages recommendations display and tracking

#### `DSSSettingsController`
Manages configuration settings

#### API Controllers
- `DeadStockApiController` - AJAX endpoints for dead stock operations
- `DSSRecommendationApiController` - AJAX endpoints for recommendations

### Events & Jobs

#### `POSTransactionCompleted` Event
Triggered when a POS transaction completes

#### `RecalculateDeadStockOnSale` Listener
Listens for POS transactions and queues re-analysis

#### `RecalculateDeadStockAnalysisJob` Job
Background job that recalculates dead stock analysis with 5-second delay to prevent excessive recalculation

## Usage

### For Administrators

#### 1. Accessing Dead Stock Module
Navigate to: **DSS > Dead Stock Management**

#### 2. Viewing Dead Stocks
- Dashboard shows statistics by priority level
- Critical items highlighted with immediate action needed
- At-risk products shown separately
- Click product name to view detailed analysis

#### 3. Managing Recommendations
- View all active recommendations
- Filter by type, priority, or status
- Mark recommendations as actioned
- Track completion rate

#### 4. Configuring Settings
Navigate to: **DSS > Settings**

Adjustable parameters:
- Dead stock threshold (default: 90 days)
- Slow moving threshold (default: 60 days)
- Fast moving units per month (default: 50 units)
- Enable/disable recommendation types
- Toggle automatic analysis on/off

#### 5. Manual Recalculation
Use button in UI: **"Recalculate Analysis"**

Or via command line:
```bash
php artisan dss:analyze
```

### For Developers

#### Installation
1. Run migrations to create DSS tables:
```bash
php artisan migrate
```

2. Register event listener in `EventServiceProvider`:
```php
protected $listen = [
    \App\Events\POSTransactionCompleted::class => [
        \App\Listeners\RecalculateDeadStockOnSale::class,
    ],
];
```

#### Integration Points

**Trigger Analysis After POS Transaction:**
```php
// In your POS controller
event(new POSTransactionCompleted($transaction));
```

**Trigger Manual Analysis:**
```php
app(DeadStockDetectionService::class)->analyzeAllProducts();
app(SalesVelocityAnalysisService::class)->analyzeAllProducts();
app(DSSRecommendationEngineService::class)->generateAllRecommendations();
```

**Get Dashboard Stats:**
```php
$detectionService = app(DeadStockDetectionService::class);

$total = $detectionService->getTotalCount();
$byPriority = $detectionService->getCountByPriority();
$totalValue = $detectionService->getTotalValue();
```

**Access Recommendations:**
```php
$recommendationService = app(DSSRecommendationEngineService::class);

// Get recommendations for a product
$recommendations = $recommendationService->getProductRecommendations($productId);

// Get pending recommendations
$pending = $recommendationService->getPendingRecommendations(50);

// Mark as actioned
$recommendationService->markAsActioned($recommendationId, $notes);
```

## API Endpoints

### Dead Stock Endpoints
- `GET /api/dss/dead-stocks` - List with filters
- `GET /api/dss/dead-stocks/{id}` - Show detail
- `POST /api/dss/dead-stocks/recalculate` - Force recalculation
- `POST /api/dss/dead-stocks/{id}/resolve` - Mark as resolved
- `GET /api/dss/dead-stocks/priority/{priority}` - Filter by priority
- `GET /api/dss/dashboard-stats` - Get statistics
- `GET /api/dss/dead-stocks/export/csv` - Export as CSV
- `GET /api/dss/top-fast-moving` - Get fast movers for bundles

### Recommendation Endpoints
- `GET /api/dss/recommendations/product/{productId}` - Get product recommendations
- `GET /api/dss/recommendations/pending` - Get pending recommendations
- `GET /api/dss/recommendations/type/{type}` - Filter by type
- `POST /api/dss/recommendations/{id}/action` - Mark as actioned
- `GET /api/dss/recommendations/pending-count` - Count pending
- `GET /api/dss/recommendations/count-by-type` - Count by type

### Settings Endpoints
- `GET /api/dss/settings` - Get all settings
- `POST /dss/settings` - Update settings

## Routes

### User Interface Routes
```
dss/dead-stock                      # Dead stock dashboard
dss/dead-stock/{id}                 # Dead stock detail
dss/recommendations                 # Recommendations list
dss/recommendations/{id}            # Recommendation detail
dss/settings                        # Configuration settings
```

### API Routes
```
api/dss/dead-stocks
api/dss/recommendations
api/dss/settings
```

## Business Rules

### Dead Stock Criteria
A product is classified as dead stock when:
- ✓ Available stock > 0
- ✓ No sales in configured period (default: 90 days)
- ✓ Not archived or inactive
- ✓ Has inventory movement history (received stock)

### Recommendation Generation Rules

**Promotion Recommendations:**
- Generated for all dead stock items
- Description adjusts based on days without sale

**Discount Recommendations:**
- Only for items 90+ days unsold
- Suggested discount: 10-20% based on age

**Bundle Recommendations:**
- Requires available fast-moving products
- Maximum 5 bundle suggestions
- Only with products currently in stock

**Relocation Recommendations:**
- Only for items 120+ days unsold
- Suggests moving to higher-demand locations

**Featured Display Recommendations:**
- For items 90+ days unsold
- Suggests prominent in-store placement

**Social Media Recommendations:**
- For items 90+ days unsold
- Suggests Facebook/Instagram promotion

**Supplier Return Recommendations:**
- Only for critical-priority items (180+ days)
- Only generated if supplier agreements allow

## Performance Considerations

### Optimization
- Indexing on `product_id`, `priority_level`, `is_active`, `days_without_sale`
- Unique constraint on `product_id` + `warehouse_id` in dead_stocks
- FTS (Full Text Search) capable on recommendations

### Execution Times
- Small inventories (< 1,000 products): < 5 seconds
- Medium inventories (1,000-10,000): 5-30 seconds
- Large inventories (10,000+): 30-120 seconds

### Recommendations
- Run manual analysis during off-peak hours
- Use queued jobs for automatic recalculation
- Consider scheduling analysis as a cron job

```bash
# Add to schedule in `app/Console/Kernel.php`
$schedule->command('dss:analyze')->daily();
```

## Best Practices

### 1. Regular Analysis
- Enable automatic analysis when POS transactions occur
- Manually recalculate daily during inventory reviews
- More frequent analysis improves accuracy

### 2. Configuration Tuning
- Adjust thresholds based on seasonal patterns
- Use 90 days for retail, 60 for fast-moving items
- Review and adjust quarterly

### 3. Recommendation Actions
- Document actions taken on recommendations
- Use action notes for audit trail
- Track completion rate to measure effectiveness

### 4. Dashboard Integration
- Add DSS widget to main dashboard
- Show critical alerts prominently
- Monitor at-risk products weekly

### 5. Data Quality
- Ensure POS transactions are completed accurately
- Maintain current product information
- Archive obsolete products instead of deleting

## Troubleshooting

### No Dead Stocks Detected
- Check if products have stock (must be > 0)
- Verify products aren't archived/inactive
- Check threshold setting matches your business cycle

### Missing Recommendations
- Verify DSS analysis is enabled in settings
- Check if feature-specific toggles are enabled
- Run manual recalculation

### Slow Performance
- Check database indexes are created
- Reduce product count for analysis (filter by category)
- Consider implementing pagination in large lists

### Stale Data
- Run `php artisan dss:analyze` manually
- Check if automatic analysis is enabled
- Verify POS transactions are being recorded

## Future Enhancements

- [ ] Machine learning integration for demand prediction
- [ ] Seasonal pattern analysis
- [ ] Multi-location analytics
- [ ] Predictive pricing recommendations
- [ ] Automated discount scheduling
- [ ] Integration with external analytics platforms
- [ ] Historical analysis trends

## Support

For issues, questions, or feature requests related to the DSS module, contact the development team.

---

**Version:** 1.0.0
**Last Updated:** July 2026
**Module Type:** Decision Support System (DSS)
