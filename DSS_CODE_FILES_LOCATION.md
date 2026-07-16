# 🗂️ DSS Code Files - Complete Directory Structure

## 📍 Where Everything Is Located

```
KCC_MOTORCYCLE/
│
├── 📂 app/
│   ├── 📂 Models/
│   │   ├── ✅ DeadStock.php
│   │   ├── ✅ DSSRecommendation.php
│   │   ├── ✅ DSSSettings.php
│   │   ├── ✅ FastMovingProduct.php
│   │   └── ✅ SlowMovingProduct.php
│   │
│   ├── 📂 Services/
│   │   ├── ✅ DeadStockDetectionService.php
│   │   ├── ✅ SalesVelocityAnalysisService.php
│   │   └── ✅ DSSRecommendationEngineService.php
│   │
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/
│   │   │   ├── ✅ DeadStockController.php
│   │   │   ├── ✅ DSSRecommendationController.php
│   │   │   ├── ✅ DSSSettingsController.php
│   │   │   └── 📂 Api/
│   │   │       ├── ✅ DeadStockApiController.php
│   │   │       └── ✅ DSSRecommendationApiController.php
│   │   │
│   │   └── 📂 Middleware/ (existing)
│   │
│   ├── 📂 Events/
│   │   └── ✅ POSTransactionCompleted.php
│   │
│   ├── 📂 Listeners/
│   │   └── ✅ RecalculateDeadStockOnSale.php
│   │
│   ├── 📂 Jobs/
│   │   └── ✅ RecalculateDeadStockAnalysisJob.php
│   │
│   ├── 📂 Console/
│   │   └── 📂 Commands/
│   │       └── ✅ RecalculateDeadStockAnalysisCommand.php
│   │
│   └── 📂 Providers/
│       └── EventServiceProvider.php (⚠️ ADD LISTENER HERE)
│
├── 📂 database/
│   └── 📂 migrations/
│       ├── ✅ 2026_07_16_000001_create_dss_settings_table.php
│       ├── ✅ 2026_07_16_000002_create_dead_stocks_table.php
│       ├── ✅ 2026_07_16_000003_create_slow_moving_products_table.php
│       ├── ✅ 2026_07_16_000004_create_fast_moving_products_table.php
│       └── ✅ 2026_07_16_000005_create_dss_recommendations_table.php
│
├── 📂 resources/
│   ├── 📂 views/
│   │   ├── 📂 dead-stock/
│   │   │   ├── ✅ index.blade.php (Dashboard)
│   │   │   └── ✅ show.blade.php (Detail Page)
│   │   │
│   │   └── 📂 dss/
│   │       ├── 📂 recommendations/
│   │       │   └── ✅ index.blade.php
│   │       │
│   │       └── 📂 settings/
│   │           └── ✅ index.blade.php
│   │
│   └── 📂 css/ (existing)
│
├── 📂 routes/
│   └── web.php (✅ UPDATED - 30+ DSS routes added)
│
├── 📂 tests/
│   ├── 📂 Feature/
│   ├── 📂 Unit/
│   └── Pest.php
│
├── 📚 Documentation Files (Root Directory)
│   ├── ✅ DSS_FINAL_SUMMARY.md
│   ├── ✅ DSS_QUICK_REFERENCE.md
│   ├── ✅ DSS_COMPLETION_SUMMARY.md
│   ├── ✅ DSS_MODULE_DOCUMENTATION.md
│   ├── ✅ DSS_IMPLEMENTATION_GUIDE.md
│   ├── ✅ DSS_API_REFERENCE.md
│   ├── ✅ DSS_ADMINISTRATOR_GUIDE.md
│   ├── ✅ DSS_DEPLOYMENT_CHECKLIST.md
│   ├── ✅ DSS_DOCUMENTATION_INDEX.md
│   ├── ✅ DSS_FILE_MANIFEST.md
│   └── ✅ DSS_CODE_FILES_LOCATION.md (This file)
│
└── (other existing files)
```

---

## 📋 Quick Lookup Table

### Models
```
app/Models/
├── DeadStock.php
├── DSSRecommendation.php
├── DSSSettings.php
├── FastMovingProduct.php
└── SlowMovingProduct.php
```

### Services (Business Logic)
```
app/Services/
├── DeadStockDetectionService.php (Core detection)
├── SalesVelocityAnalysisService.php (Sales analysis)
└── DSSRecommendationEngineService.php (Recommendations)
```

### Controllers (UI)
```
app/Http/Controllers/
├── DeadStockController.php (Dashboard & detail)
├── DSSRecommendationController.php (Manage recommendations)
└── DSSSettingsController.php (Configure settings)
```

### API Controllers
```
app/Http/Controllers/Api/
├── DeadStockApiController.php (Dead stock API)
└── DSSRecommendationApiController.php (Recommendation API)
```

### Views (User Interface)
```
resources/views/
├── dead-stock/
│   ├── index.blade.php (Dashboard)
│   └── show.blade.php (Product detail)
├── dss/
│   ├── recommendations/
│   │   └── index.blade.php (Recommendations list)
│   └── settings/
│       └── index.blade.php (Settings page)
```

### Events, Jobs, Commands
```
app/
├── Events/POSTransactionCompleted.php
├── Listeners/RecalculateDeadStockOnSale.php
├── Jobs/RecalculateDeadStockAnalysisJob.php
└── Console/Commands/RecalculateDeadStockAnalysisCommand.php
```

### Database Migrations
```
database/migrations/
├── 2026_07_16_000001_create_dss_settings_table.php
├── 2026_07_16_000002_create_dead_stocks_table.php
├── 2026_07_16_000003_create_slow_moving_products_table.php
├── 2026_07_16_000004_create_fast_moving_products_table.php
└── 2026_07_16_000005_create_dss_recommendations_table.php
```

---

## 🔧 Files That Need Manual Updates

### 1. EventServiceProvider.php
**File:** `app/Providers/EventServiceProvider.php`

**Add:**
```php
protected $listen = [
    \App\Events\POSTransactionCompleted::class => [
        \App\Listeners\RecalculateDeadStockOnSale::class,
    ],
];
```

**Purpose:** Register the event listener for auto-recalculation

---

### 2. POS Transaction Controller
**File:** `app/Http/Controllers/POSTransactionController.php` (or wherever POS transactions are saved)

**Add After Transaction Save:**
```php
event(new \App\Events\POSTransactionCompleted($transaction));
```

**Purpose:** Emit event to trigger dead stock analysis

---

### 3. Navigation/Menu File
**File:** Wherever your navigation menu is (likely in `resources/views/layouts/`)

**Add:**
```html
<a href="{{ route('dss.dead-stock.index') }}">Dead Stock</a>
<a href="{{ route('dss.recommendations.index') }}">Recommendations</a>
<a href="{{ route('dss.settings.index') }}">DSS Settings</a>
```

**Purpose:** Add DSS links to navigation

---

### 4. Routes File
**File:** `routes/web.php`

**Status:** ✅ Already contains all DSS routes (just copied)

**Verify:** Routes should include:
- `/dss/dead-stock` (dashboard)
- `/dss/dead-stock/{id}` (detail)
- `/dss/recommendations` (list)
- `/dss/settings` (config)
- All API routes under `/api/dss/`

---

## 🎯 Directory Creation Steps

If directories don't exist, create them:

```bash
# Models (usually exists)
mkdir -p app/Models

# Services
mkdir -p app/Services

# Controllers
mkdir -p app/Http/Controllers/Api

# Events, Listeners, Jobs, Commands (usually exist)
mkdir -p app/Events
mkdir -p app/Listeners
mkdir -p app/Jobs
mkdir -p app/Console/Commands

# Views
mkdir -p resources/views/dead-stock
mkdir -p resources/views/dss/recommendations
mkdir -p resources/views/dss/settings

# Migrations (usually exists)
mkdir -p database/migrations
```

---

## 📂 File Organization Best Practices

### Services
- **Location:** `app/Services/`
- **Naming:** `{Feature}Service.php`
- **Purpose:** Business logic, reusable across controllers
- **Examples:** DeadStockDetectionService.php

### Models
- **Location:** `app/Models/`
- **Naming:** `{Entity}.php` (PascalCase)
- **Purpose:** Database table representation
- **Examples:** DeadStock.php, DSSRecommendation.php

### Controllers
- **Location:** `app/Http/Controllers/` (web) or `app/Http/Controllers/Api/` (API)
- **Naming:** `{Feature}Controller.php`
- **Purpose:** HTTP request handling
- **Examples:** DeadStockController.php, DeadStockApiController.php

### Views
- **Location:** `resources/views/{feature}/`
- **Naming:** `{action}.blade.php`
- **Purpose:** HTML rendering
- **Examples:** index.blade.php, show.blade.php

### Events/Listeners/Jobs
- **Location:** `app/Events/`, `app/Listeners/`, `app/Jobs/`
- **Purpose:** Event handling and async processing
- **Examples:** POSTransactionCompleted.php

### Migrations
- **Location:** `database/migrations/`
- **Naming:** `YYYY_MM_DD_HHMMSS_{description}.php`
- **Purpose:** Database schema creation
- **Examples:** 2026_07_16_000001_create_dss_settings_table.php

---

## 🔍 How to Find Files Quickly

### By Feature
- **Dead Stock Detection:** Look in `Services/DeadStockDetectionService.php` or `Models/DeadStock.php`
- **Recommendations:** Look in `Services/DSSRecommendationEngineService.php` or `Models/DSSRecommendation.php`
- **Sales Analysis:** Look in `Services/SalesVelocityAnalysisService.php`
- **Dashboard:** Look in `Controllers/DeadStockController.php` and `views/dead-stock/index.blade.php`
- **API:** Look in `Controllers/Api/` directory
- **Configuration:** Look in `Models/DSSSettings.php` and `views/dss/settings/`
- **Automation:** Look in `Events/`, `Listeners/`, `Jobs/` directories

### By Layer
- **Database:** `database/migrations/`
- **Data Access:** `app/Models/`
- **Business Logic:** `app/Services/`
- **HTTP Handling:** `app/Http/Controllers/`
- **User Interface:** `resources/views/`
- **Automation:** `app/Events/`, `app/Listeners/`, `app/Jobs/`
- **Command Line:** `app/Console/Commands/`

### By Extension
- **Models:** `.php` files in `app/Models/` with model logic
- **Services:** `.php` files in `app/Services/` with service suffix
- **Controllers:** `.php` files in `app/Http/Controllers/` with Controller suffix
- **Views:** `.blade.php` files in `resources/views/`
- **Migrations:** `.php` files in `database/migrations/` with timestamp prefix
- **Events:** `.php` files in `app/Events/`
- **Listeners:** `.php` files in `app/Listeners/`
- **Jobs:** `.php` files in `app/Jobs/`

---

## ✅ Verification Checklist

After deployment, verify:

- [ ] All 5 migrations created in `database/migrations/`
- [ ] All 5 models created in `app/Models/`
- [ ] All 3 services created in `app/Services/`
- [ ] All 5 controllers created in `app/Http/Controllers/` and `app/Http/Controllers/Api/`
- [ ] Event, Listener, Job created in respective folders
- [ ] Console command created in `app/Console/Commands/`
- [ ] All 4 views created in `resources/views/`
- [ ] Routes added to `routes/web.php`
- [ ] EventServiceProvider updated
- [ ] Navigation menu updated
- [ ] POS transaction controller updated (emit event)

---

## 🔗 Navigation Shortcuts

### From Dashboard
→ Click "Dead Stock Management" in sidebar

### From CLI
```bash
# Run migrations
php artisan migrate

# Run analysis
php artisan dss:analyze

# List all routes
php artisan route:list | grep dss
```

### From Browser
- Dashboard: `http://your-app.com/dss/dead-stock`
- Recommendations: `http://your-app.com/dss/recommendations`
- Settings: `http://your-app.com/dss/settings`

### From IDE
- Press Ctrl+P (or Cmd+P on Mac) in VS Code
- Type filename (e.g., `DeadStock` or `DeadStockController`)
- Press Enter to open file

---

## 📊 File Statistics

| Category | Files | Location |
|----------|-------|----------|
| Models | 5 | `app/Models/` |
| Services | 3 | `app/Services/` |
| Controllers | 5 | `app/Http/Controllers/` + `Api/` |
| Views | 4 | `resources/views/` |
| Migrations | 5 | `database/migrations/` |
| Events/Jobs/Commands | 4 | `app/Events/`, `Listeners/`, `Jobs/`, `Console/Commands/` |
| Routes | 1 | `routes/web.php` |
| Documentation | 11 | Root directory |
| **TOTAL** | **38** | **Various** |

---

## 🎯 Quick Links to Important Files

| Need | File |
|------|------|
| Dashboard UI | `resources/views/dead-stock/index.blade.php` |
| Dead Stock Model | `app/Models/DeadStock.php` |
| Detection Logic | `app/Services/DeadStockDetectionService.php` |
| Recommendations | `app/Services/DSSRecommendationEngineService.php` |
| API Endpoints | `app/Http/Controllers/Api/DeadStockApiController.php` |
| Database Schema | `database/migrations/2026_07_16_000002_create_dead_stocks_table.php` |
| Settings | `app/Models/DSSSettings.php` |
| Routes | `routes/web.php` (search for `dss`) |
| Automation | `app/Events/POSTransactionCompleted.php` |
| Manual Analysis | `app/Console/Commands/RecalculateDeadStockAnalysisCommand.php` |

---

## 🚀 Next Steps

1. **Copy all files to correct locations** (use this guide)
2. **Run migrations** (`php artisan migrate`)
3. **Update EventServiceProvider** (add listener)
4. **Update POS controller** (emit event)
5. **Update navigation** (add menu links)
6. **Run initial analysis** (`php artisan dss:analyze`)
7. **Test** (visit `/dss/dead-stock`)

---

## 📞 Troubleshooting File Issues

### "File not found" error
- Check file exists in correct location
- Check file name capitalization (Laravel is case-sensitive)
- Check namespace matches file path

### "Class not found" error
- Verify file is in correct directory
- Check `use` statements import correct path
- Run `composer dump-autoload`

### "Route not found" error
- Verify routes added to `routes/web.php`
- Run `php artisan route:clear`
- Reload browser

### "Migration failed" error
- Check migrations in correct directory
- Verify migration naming format (timestamp first)
- Check migration content for syntax errors

---

**Version:** 1.0.0
**Date:** July 16, 2026
**Status:** Complete

For detailed file information, see `DSS_FILE_MANIFEST.md`
For documentation index, see `DSS_DOCUMENTATION_INDEX.md`
