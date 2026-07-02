<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\SupplierAssessmentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\POSTransactionController;

Route::view('/', 'login')->name('home');
Route::view('/login', 'login')->name('login');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

// Login routes
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/login/otp/send', [App\Http\Controllers\Auth\LoginOtpController::class, 'send'])->name('login.otp.send');
Route::post('/login/otp/verify', [App\Http\Controllers\Auth\LoginOtpController::class, 'verify'])->name('login.otp.verify');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/password/email', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::post('/password/reset', [App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');


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

    // POS Transaction APIs - Admin and Cashier only
    Route::middleware('role:admin,cashier')->group(function () {
        Route::post('api/pos/transactions', [POSTransactionController::class, 'store'])->name('api.pos.transactions.store');
    });
    // POS Transaction read access - Admin, Cashier, Inventory Clerk
    Route::middleware('role:admin,cashier,inventory_clerk')->group(function () {
        Route::get('api/pos/transactions', [POSTransactionController::class, 'index'])->name('api.pos.transactions.index');
        Route::get('api/pos/transactions/top-selling', [POSTransactionController::class, 'topSellingProducts'])->name('api.pos.transactions.top_selling');
    });

    // All Stocks - Admin, Inventory Clerk, Warehouse Personnel
    Route::middleware('role:admin,inventory_clerk,warehouse_personnel')->group(function () {
        Route::view('all-stocks', 'inventory.allstocks')->name('allstocks');
        Route::view('archived-items', 'inventory.archived')->name('archived');
        Route::post('stock/import', [App\Http\Controllers\StockImportController::class, 'import'])->name('stock.import');
        Route::post('stock/add', [App\Http\Controllers\StockImportController::class, 'addStock'])->name('stock.add');
        Route::post('api/product/{id}/price', [App\Http\Controllers\StockImportController::class, 'updatePrice'])->name('api.product.update_price');
        Route::get('stock/export', [App\Http\Controllers\StockImportController::class, 'exportProducts'])->name('stock.export');
        Route::get('stock/import-status', [App\Http\Controllers\StockImportController::class, 'status'])->name('stock.import.status');
    });

    // Item Disposal & Reverse Logistics - Admin and Inventory Clerk only
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::view('item/disposal', 'inventory.item-disposal')->name('item.disposal');
        Route::view('reverse-logistics', 'inventory.reverse-logistics')->name('reverse-logistics');
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
        Route::post('warehouse-management/add-product', [App\Http\Controllers\WarehouseManagementController::class, 'addProduct'])->name('warehouse.management.add_product');
    });

    // Purchase Order Routes - inventory clerk can create orders, admin can approve and send, warehouse and inventory can receive
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::get('purchase-order/management', [PurchaseOrderController::class, 'management'])->name('order.management');
        Route::get('purchase-order/create', [PurchaseOrderController::class, 'create'])->name('order.create');
        Route::get('purchase-order/history', [PurchaseOrderController::class, 'history'])->name('order.history');
        Route::post('purchase-order', [PurchaseOrderController::class, 'store'])->name('order.store');
    });

    Route::middleware('role:admin,inventory_clerk,warehouse_personnel')->group(function () {
        Route::get('purchase-order/received', [PurchaseOrderController::class, 'receivedOrders'])->name('received.orders');
        Route::get('purchase-order/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('order.show');
        Route::post('purchase-order/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('order.receive');
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('purchase-order/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('order.approve');
        Route::post('purchase-order/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('order.reject');
        Route::post('purchase-order/{purchaseOrder}/send', [PurchaseOrderController::class, 'sendToSupplier'])->name('order.send');
        Route::post('purchase-order/{purchaseOrder}/in-transit', [PurchaseOrderController::class, 'markInTransit'])->name('order.in_transit');
    });

    // Supplier Assessment Route - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('supplier-assessment', [SupplierAssessmentController::class, 'index'])->name('supplier.assessment');
        Route::post('supplier-assessment/suppliers', [SupplierAssessmentController::class, 'store'])->name('supplier.assessment.store');
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
