<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\SupplierAssessmentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnalyticsController;

Route::view('/', 'login')->name('home');
Route::view('/login', 'login')->name('login');
Route::view('/forgot-password', 'forgot-password')->name('forgot-password');

// Forgot Password routes (public)
Route::post('/forgot-password/send', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetCode'])->name('password.send-code');
Route::post('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyCode'])->name('password.verify-code');
Route::post('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.reset.code');

// Login routes
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/login/otp/send', [App\Http\Controllers\Auth\LoginOtpController::class, 'send'])->name('login.otp.send');
Route::post('/login/otp/verify', [App\Http\Controllers\Auth\LoginOtpController::class, 'verify'])->name('login.otp.verify');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - All authenticated users
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Profile update for authenticated users
    Route::match(['patch','post'], 'profile', [UserController::class, 'updateProfile'])->name('profile.update');
    // Profile display
    Route::view('profile', 'profile.show')->name('profile.show');

    // Point of Sales Routes - Admin and Cashier only
    Route::middleware('role:admin,cashier')->group(function () {
        Route::view('pos/terminal', 'point_of_sales.terminal')->name('pos.terminal');
        Route::view('pos/mobile-scanner', 'point_of_sales.mobile-scanner')->name('pos.mobile-scanner');
        Route::post('pos/scan', [App\Http\Controllers\PosController::class, 'handleScan'])->name('pos.scan');
        Route::view('replacing-items', 'point_of_sales.replacing-items')->name('replacing.items');
    });

    // Inventory Management Routes - Different access per role
    // Inventory Monitoring & Product Categorization - All roles except Admin
    Route::middleware('role:admin,inventory_clerk,cashier,warehouse_personnel')->group(function () {
        Route::view('inventory/monitoring', 'inventory.monitoring')->name('inventory.monitoring');
        Route::view('product/categorization', 'inventory.product-categorization')->name('product.categorization');
    });

    // Shared inventory APIs for monitoring and stock overview
    Route::get('api/products', [App\Http\Controllers\StockImportController::class, 'getProducts'])->name('api.products');
    Route::get('api/stats', [App\Http\Controllers\StockImportController::class, 'getStats'])->name('api.stats');
    Route::get('api/movements', [App\Http\Controllers\StockImportController::class, 'getMovements'])->name('api.movements');

    // POS API for mobile scanner sync
    Route::get('api/pos/check-scan', [App\Http\Controllers\PosController::class, 'checkScan'])->name('api.pos.check-scan');

    // All Stocks - Admin, Inventory Clerk, Warehouse Personnel
    Route::middleware('role:admin,inventory_clerk,warehouse_personnel')->group(function () {
        Route::view('all-stocks', 'inventory.allstocks')->name('allstocks');
        Route::view('archived-items', 'inventory.archived')->name('archived');
        Route::post('stock/import', [App\Http\Controllers\StockImportController::class, 'import'])->name('stock.import');
        Route::post('stock/add', [App\Http\Controllers\StockImportController::class, 'addStock'])->name('stock.add');
        Route::post('api/product/{id}/price', [App\Http\Controllers\StockImportController::class, 'updatePrice'])->name('api.product.update_price');
        Route::post('product/{id}', [App\Http\Controllers\StockImportController::class, 'updateProduct'])->name('product.update');
        Route::get('stock/export', [App\Http\Controllers\StockImportController::class, 'exportProducts'])->name('stock.export');
        Route::get('stock/import-status', [App\Http\Controllers\StockImportController::class, 'status'])->name('stock.import.status');
    });

    // Item Disposal & Reverse Logistics - Admin and Inventory Clerk only
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::get('item/disposal', [App\Http\Controllers\ItemDisposalController::class, 'index'])->name('item.disposal');
        Route::post('item/disposal/{id}/approve', [App\Http\Controllers\ItemDisposalController::class, 'approve'])->name('item.disposal.approve');
        Route::post('item/disposal/{id}/dispose', [App\Http\Controllers\ItemDisposalController::class, 'markAsDisposed'])->name('item.disposal.dispose');
        Route::post('item/disposal/from-reverse-logistics/{id}', [App\Http\Controllers\ItemDisposalController::class, 'createFromReverseLogistics'])->name('item.disposal.from-reverse-logistics');
        Route::view('reverse-logistics', 'inventory.reverse-logistics')->name('reverse-logistics');
        
        // Reverse Logistics API routes
        Route::get('api/reverse-logistics', [App\Http\Controllers\ReverseLogisticsController::class, 'index'])->name('api.reverse-logistics.index');
        Route::post('api/reverse-logistics', [App\Http\Controllers\ReverseLogisticsController::class, 'store'])->name('api.reverse-logistics.store');
        Route::get('api/reverse-logistics/{id}', [App\Http\Controllers\ReverseLogisticsController::class, 'show'])->name('api.reverse-logistics.show');
        Route::post('api/reverse-logistics/{id}', [App\Http\Controllers\ReverseLogisticsController::class, 'update'])->name('api.reverse-logistics.update');
        Route::delete('api/reverse-logistics/{id}', [App\Http\Controllers\ReverseLogisticsController::class, 'destroy'])->name('api.reverse-logistics.destroy');
    });

    // Data Analytics Routes - Sales analytics for Cashier and Warehouse, all for others
    Route::middleware('role:admin,cashier,inventory_clerk,warehouse_personnel')->group(function () {
        Route::get('analytics/sales', [AnalyticsController::class, 'sales'])->name('sales.analytics');
    });

    // Other analytics routes - Admin and Inventory Clerk only
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::get('analytics/pricing', [AnalyticsController::class, 'pricing'])->name('pricing.module');
        Route::get('analytics/overstocking', [AnalyticsController::class, 'overstocking'])->name('overstocking.report');
        Route::get('analytics/out-of-stock', [AnalyticsController::class, 'outOfStock'])->name('out.of.stock');
    });

    // Warehouse Management Route - Admin and Warehouse Personnel only
    Route::middleware('role:admin,warehouse_personnel')->group(function () {
        Route::get('warehouse-management', [App\Http\Controllers\WarehouseManagementController::class, 'index'])->name('warehouse.management');
        Route::get('warehouse-management/mobile-scanner', function() {
            return view('warehouse_management.mobile-scanner');
        })->name('warehouse.mobile.scanner');
        Route::post('warehouse-management/add-product', [App\Http\Controllers\WarehouseManagementController::class, 'addProduct'])->name('warehouse.management.add_product');
        Route::post('warehouse-management/save-shelf', [App\Http\Controllers\WarehouseManagementController::class, 'saveShelf'])->name('warehouse.management.save_shelf');
    });

    // Purchase Order Routes - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('purchase-order/management', [PurchaseOrderController::class, 'management'])->name('order.management');
        Route::post('purchase-order', [PurchaseOrderController::class, 'store'])->name('order.store');
        Route::get('purchase-order/received', [PurchaseOrderController::class, 'receivedOrders'])->name('received.orders');
    });

    // Offline Data Reconciliation Routes - Admin only
    Route::prefix('offline-reconciliation')->group(function () {
        Route::get('purchase-orders', [App\Http\Controllers\OfflineReconciliationController::class, 'index'])->name('offline.purchase-orders');
        Route::get('inventory-movements', [App\Http\Controllers\OfflineReconciliationController::class, 'inventoryMovements'])->name('offline.inventory-movements');
        Route::get('export', [App\Http\Controllers\ExportController::class, 'index'])->name('offline.export');
        Route::post('export/csv', [App\Http\Controllers\ExportController::class, 'exportCsv'])->name('offline.export.csv');
        Route::post('export/excel', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('offline.export.excel');
        Route::get('import', [App\Http\Controllers\ImportController::class, 'index'])->name('offline.import');
        Route::post('import', [App\Http\Controllers\ImportController::class, 'import'])->name('offline.import.store');
        Route::post('import/validate', [App\Http\Controllers\ImportController::class, 'validateFile'])->name('offline.import.validate');
        Route::get('pending-imports', [App\Http\Controllers\ImportController::class, 'pendingImports'])->name('offline.pending.imports');
        Route::get('pending-imports/{id}/review', [App\Http\Controllers\ImportController::class, 'review'])->name('offline.pending.review');
        Route::post('pending-imports/{id}/approve', [App\Http\Controllers\ImportController::class, 'approve'])->name('offline.pending.approve');
        Route::post('pending-imports/{id}/reject', [App\Http\Controllers\ImportController::class, 'reject'])->name('offline.pending.reject');
        Route::get('history', [App\Http\Controllers\OfflineReconciliationController::class, 'history'])->name('offline.history');
        Route::get('report/{id}', [App\Http\Controllers\OfflineReconciliationController::class, 'report'])->name('offline.report');
        Route::delete('history/{id}', [App\Http\Controllers\OfflineReconciliationController::class, 'destroyHistory'])->name('offline.history.destroy');
        Route::get('api/stats', [App\Http\Controllers\OfflineReconciliationController::class, 'stats'])->name('offline.api.stats');
        Route::get('local-orders', [App\Http\Controllers\OfflineReconciliationController::class, 'localOrders'])->name('offline.local.orders');
        Route::post('sync-order', [App\Http\Controllers\OfflineReconciliationController::class, 'syncOrder'])->name('offline.sync.order');
        Route::post('sync-movement', [App\Http\Controllers\OfflineReconciliationController::class, 'syncMovement'])->name('offline.sync.movement');
    });

    // Supplier Assessment Route - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('supplier-assessment', [SupplierAssessmentController::class, 'index'])->name('supplier.assessment');
        Route::get('supplier-assessment/archived', [SupplierAssessmentController::class, 'archived'])->name('supplier.assessment.archived');
        Route::post('supplier-assessment/suppliers', [SupplierAssessmentController::class, 'store'])->name('supplier.assessment.store');
        Route::post('supplier-assessment/suppliers/{supplier}/restore', [SupplierAssessmentController::class, 'restore'])->name('supplier.assessment.restore');
        Route::patch('supplier-assessment/suppliers/{supplier}', [SupplierAssessmentController::class, 'update'])->name('supplier.assessment.update');
        Route::delete('supplier-assessment/suppliers/{supplier}', [SupplierAssessmentController::class, 'destroy'])->name('supplier.assessment.destroy');
    });

    // User Management Routes (controller-driven) - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('user-management', [UserController::class, 'index'])->name('user.management');
        Route::get('user-management/archived', [UserController::class, 'archived'])->name('user.management.archived');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update_role');
        Route::match(['patch','post'], 'users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update_status');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Offline Reconciliation Route - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::view('offline-reconciliation', 'offline_reconciliation.offline_recon')->name('offline.reconciliation');
    });

    // Settings Routes - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::view('settings/general', 'settings.general')->name('settings.general');
        Route::view('settings/users', 'settings.users')->name('settings.users');
    });
});
