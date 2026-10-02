<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\SupplierAssessmentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\POSTransactionController;
use App\Http\Controllers\ShopInventoryController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\ProductDescriptionController;
use App\Http\Controllers\InventoryNotificationController;

Route::view('/', 'login')->name('home');
Route::view('/login', 'login')->name('login');

// Forgot Password routes (public)
Route::post('/forgot-password/send', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetCode'])->name('password.send-code');
Route::post('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyCode'])->name('password.verify-code');
Route::post('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.reset.code');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset.token');

// Diagnostic route to test and debug live email delivery directly from Render
Route::get('/api/test-email', function (\Illuminate\Http\Request $request) {
    $targetEmail = $request->query('to', 'ilanolawrence04@gmail.com');
    $resendApiKey = env('RESEND_API_KEY');
    $config = [
        'resend_configured' => !empty($resendApiKey),
        'resend_key_prefix' => !empty($resendApiKey) ? substr($resendApiKey, 0, 7) . '...' : 'NONE',
        'default_mailer'    => config('mail.default'),
        'smtp_host'         => config('mail.mailers.smtp.host'),
        'smtp_port'         => config('mail.mailers.smtp.port'),
        'from'              => config('mail.from'),
    ];

    $testCode = (string) rand(100000, 999999);
    $html = "<p>Live test email from KCC Motorcycle Cloud System.</p><p><strong>Test Security Code: {$testCode}</strong></p>";
    $sent = \App\Services\ResendEmailService::send($targetEmail, 'KCC Motorcycle Live Cloud Email Test', $html);

    return response()->json([
        'success'     => $sent,
        'message'     => $sent ? "Email sent successfully to {$targetEmail}!" : "Failed to send email to {$targetEmail}.",
        'test_code'   => $testCode,
        'config_used' => $config,
    ]);
});

// Login routes
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/login/otp/send', [App\Http\Controllers\Auth\LoginOtpController::class, 'send'])->name('login.otp.send');
Route::post('/login/otp/verify', [App\Http\Controllers\Auth\LoginOtpController::class, 'verify'])->name('login.otp.verify');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Forgot Password routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password/otp/send', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOtp'])->name('forgot-password.otp.send');
Route::get('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showVerifyOtp'])->name('verify-otp');
Route::post('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('verify-otp.verify');
Route::get('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetPassword'])->name('reset-password');
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('reset-password.update');


Route::middleware(['auth'])->group(function () {
    // Dashboard - All authenticated users
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/data', [App\Http\Controllers\DashboardController::class, 'data'])->name('dashboard.data');

    // Inventory Notification API Routes
    Route::get('api/inventory-notifications', [InventoryNotificationController::class, 'index'])->name('api.inventory-notifications.index');
    Route::get('api/inventory-notifications/dashboard', [InventoryNotificationController::class, 'dashboardAlerts'])->name('api.inventory-notifications.dashboard');
    Route::get('api/inventory-notifications/unread-count', [InventoryNotificationController::class, 'unreadCount'])->name('api.inventory-notifications.unread-count');
    Route::post('api/inventory-notifications/{id}/dismiss', [InventoryNotificationController::class, 'dismiss'])->name('api.inventory-notifications.dismiss');
    Route::post('api/inventory-notifications/{id}/read', [InventoryNotificationController::class, 'markAsRead'])->name('api.inventory-notifications.read');
    Route::post('api/inventory-notifications/mark-all-read', [InventoryNotificationController::class, 'markAllAsRead'])->name('api.inventory-notifications.mark-all-read');
    Route::post('api/inventory-notifications/sync', [InventoryNotificationController::class, 'sync'])->name('api.inventory-notifications.sync');

    // Active status heartbeat check
    Route::get('api/user/active-status', function(\Illuminate\Http\Request $request) {
        $user = $request->user();
        if ($user && ! ($user->is_active ?? true)) {
            \Illuminate\Support\Facades\Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return response()->json(['deactivated' => true, 'message' => 'Your account has been deactivated.'], 401);
        }
        return response()->json(['active' => true]);
    })->name('api.user.active-status');

    // Profile update for authenticated users
    Route::match(['patch','post'], 'profile', [UserController::class, 'updateProfile'])->name('profile.update');
    // Profile display
    Route::get('profile', [UserController::class, 'showProfile'])->name('profile.show');

    // Point of Sales Routes - Admin and Cashier only
    Route::middleware('role:admin,cashier')->group(function () {
        Route::view('pos/terminal', 'point_of_sales.terminal')->name('pos.terminal');
        Route::view('pos/archived-items', 'inventory.archived')->name('pos.archived');
        Route::view('pos/mobile-scanner', 'point_of_sales.mobile-scanner')->name('pos.mobile-scanner');
        Route::post('pos/scan', [App\Http\Controllers\PosController::class, 'handleScan'])->name('pos.scan');
        Route::view('replacing-items', 'point_of_sales.replacing-items')->name('replacing.items');

        // Replacements API routes
        Route::get('api/replacements', [App\Http\Controllers\ReplacementController::class, 'index'])->name('api.replacements.index');
        Route::post('api/replacements', [App\Http\Controllers\ReplacementController::class, 'store'])->name('api.replacements.store');
        Route::put('api/replacements/{id}', [App\Http\Controllers\ReplacementController::class, 'update'])->name('api.replacements.update');
        Route::delete('api/replacements/{id}', [App\Http\Controllers\ReplacementController::class, 'destroy'])->name('api.replacements.destroy');
    });

    // Inventory Management Routes - Different access per role
    // Inventory Monitoring & Product Categorization - All roles except Admin
    Route::middleware('role:admin,inventory_clerk,cashier,warehouse_personnel')->group(function () {
        Route::view('inventory/monitoring', 'inventory.monitoring')->name('inventory.monitoring');
        Route::get('product/categorization', [ProductCatalogController::class, 'index'])->name('product.categorization');
    });

    // Product Catalog Resource Routes - Admin and Inventory Clerk only
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::resource('product-catalog', ProductCatalogController::class);
        Route::get('product-catalog/{product_catalog}/download-qr', [ProductCatalogController::class, 'downloadQR'])->name('product-catalog.download-qr');
        Route::get('product-catalog/{product_catalog}/print-qr', [ProductCatalogController::class, 'printQR'])->name('product-catalog.print-qr');
        Route::post('product-catalog/{product_catalog}/regenerate-qr', [ProductCatalogController::class, 'regenerateQR'])->name('product-catalog.regenerate-qr');
        Route::get('api/product-catalog/generate-sku', [ProductCatalogController::class, 'generateSKU'])->name('product-catalog.generate-sku');
        Route::delete('product-catalog/bulk-delete', [ProductCatalogController::class, 'bulkDelete'])->name('product-catalog.bulk-delete');
        // Trash / Restore routes
        Route::get('api/product-catalog/trash', [ProductCatalogController::class, 'trash'])->name('product-catalog.trash');
        Route::post('product-catalog/{id}/restore', [ProductCatalogController::class, 'restore'])->name('product-catalog.restore');
        Route::post('product-catalog/bulk-restore', [ProductCatalogController::class, 'bulkRestore'])->name('product-catalog.bulk-restore');
        Route::delete('product-catalog/force-delete', [ProductCatalogController::class, 'forceDelete'])->name('product-catalog.force-delete');
        // Product Description routes
        Route::get('product-descriptions', [ProductDescriptionController::class, 'index'])->name('product-descriptions.index');
        Route::post('product-descriptions', [ProductDescriptionController::class, 'store'])->name('product-descriptions.store');
    });

    // Shared inventory APIs for monitoring and stock overview
    Route::get('api/products', [App\Http\Controllers\StockImportController::class, 'getProducts'])->name('api.products');
    Route::get('api/product-descriptions', [App\Http\Controllers\StockImportController::class, 'getProductDescriptions'])->name('api.product-descriptions');
    Route::get('api/products/archived', [App\Http\Controllers\StockImportController::class, 'getArchivedProducts'])->name('api.products.archived');
    Route::get('api/products/{id}', [App\Http\Controllers\StockImportController::class, 'getProduct'])->name('api.product.show');
    Route::get('api/suppliers', [App\Http\Controllers\StockImportController::class, 'getSuppliers'])->name('api.suppliers');
    Route::get('api/inventory/location-quantities/{product_id}', [App\Http\Controllers\StockImportController::class, 'getLocationQuantities'])->name('api.inventory.location-quantities');
    Route::get('api/stats', [App\Http\Controllers\StockImportController::class, 'getStats'])->name('api.stats');
    Route::get('api/movements', [App\Http\Controllers\StockImportController::class, 'getMovements'])->name('api.movements');

    // POS API for mobile scanner & live cross-device cart sync
    Route::get('api/pos/check-scan', [App\Http\Controllers\PosController::class, 'checkScan'])->name('api.pos.check-scan');
    Route::post('api/pos/sync-cart', [App\Http\Controllers\PosController::class, 'syncCart'])->name('api.pos.sync-cart');
    Route::get('api/pos/sync-cart', [App\Http\Controllers\PosController::class, 'getActiveCart'])->name('api.pos.get-cart');


    // POS Transaction APIs - Admin and Cashier only
    Route::middleware('role:admin,cashier')->group(function () {
        Route::post('api/pos/transactions', [POSTransactionController::class, 'store'])->name('api.pos.transactions.store');
        Route::post('api/pos/validate-stock', [POSTransactionController::class, 'validateStock'])->name('api.pos.validate_stock');
        Route::post('api/pos/remove-product-discount', [App\Http\Controllers\DeadStockController::class, 'removeDiscount'])->name('api.pos.remove-discount');
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
        Route::post('product/{id}', [App\Http\Controllers\StockImportController::class, 'updateProduct'])->name('product.update');
        Route::get('stock/export', [App\Http\Controllers\StockImportController::class, 'exportProducts'])->name('stock.export');
        Route::get('stock/import-status', [App\Http\Controllers\StockImportController::class, 'status'])->name('stock.import.status');
        Route::post('api/product/{id}/archive', [App\Http\Controllers\StockImportController::class, 'archiveProduct'])->name('api.product.archive');
        Route::post('api/product/{id}/restore', [App\Http\Controllers\StockImportController::class, 'restoreProduct'])->name('api.product.restore');
        Route::delete('api/product/{id}/permanent', [App\Http\Controllers\StockImportController::class, 'permanentDeleteProduct'])->name('api.product.permanent_delete');
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
        Route::post('api/reverse-logistics/{id}/restock', [App\Http\Controllers\ReverseLogisticsController::class, 'processRestock'])->name('api.reverse-logistics.restock');
        Route::delete('api/reverse-logistics/{id}', [App\Http\Controllers\ReverseLogisticsController::class, 'destroy'])->name('api.reverse-logistics.destroy');
    });

    // Data Analytics Routes - Sales analytics for Admin, Cashier, and Warehouse only (NOT inventory clerk)
    Route::middleware('role:admin,cashier,warehouse_personnel')->group(function () {
        Route::get('analytics/sales', [AnalyticsController::class, 'sales'])->name('sales.analytics');
        Route::get('api/analytics/sales-widgets', [AnalyticsController::class, 'salesFilteredWidgets'])->name('api.analytics.sales_widgets');
        Route::get('analytics/sales/export', [AnalyticsController::class, 'exportSales'])->name('analytics.sales.export');
    });

    // Pricing Module - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('analytics/pricing', [AnalyticsController::class, 'pricing'])->name('pricing.module');
        Route::post('analytics/pricing/dismiss/{id}', [AnalyticsController::class, 'dismissAlert'])->name('pricing.dismiss');
        Route::get('analytics/pricing/export', [AnalyticsController::class, 'exportPricing'])->name('analytics.pricing.export');
    });

    // Inventory analytics routes - Admin and Inventory Clerk
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::get('analytics/overstocking', [AnalyticsController::class, 'overstocking'])->name('overstocking.report');
        Route::get('analytics/overstocking/export', [AnalyticsController::class, 'exportOverstocking'])->name('analytics.overstocking.export');
        Route::get('analytics/out-of-stock', [AnalyticsController::class, 'outOfStock'])->name('out.of.stock');
        Route::get('analytics/out-of-stock/export', [AnalyticsController::class, 'exportOutOfStock'])->name('analytics.out_of_stock.export');
    });

    // Warehouse Management Route - Admin and Warehouse Personnel only
    Route::middleware('role:admin,warehouse_personnel')->group(function () {
        Route::get('warehouse-management', [App\Http\Controllers\WarehouseManagementController::class, 'index'])->name('warehouse.management');
        Route::get('warehouse-management/mobile-scanner', function() {
            return view('warehouse_management.mobile-scanner');
        })->name('warehouse.mobile.scanner');
        Route::post('warehouse-management/add-product', [App\Http\Controllers\WarehouseManagementController::class, 'addProduct'])->name('warehouse.management.add_product');
        Route::post('warehouse-management/save-shelf', [App\Http\Controllers\WarehouseManagementController::class, 'saveShelf'])->name('warehouse.management.save_shelf');
        Route::post('warehouse-management/add-warehouse', [App\Http\Controllers\WarehouseManagementController::class, 'addWarehouse'])->name('warehouse.management.add_warehouse');
        Route::get('warehouse-management/list', [App\Http\Controllers\WarehouseManagementController::class, 'listWarehouses'])->name('warehouse.management.list');
        Route::post('warehouse-management/transfer-shelf', [App\Http\Controllers\WarehouseManagementController::class, 'transferShelf'])->name('warehouse.management.transfer_shelf');
        Route::get('warehouse/transfer/{warehouseId}/{slotIndex}', [App\Http\Controllers\WarehouseManagementController::class, 'showTransfer'])->name('warehouse.transfer');
        Route::post('warehouse/transfer/execute', [App\Http\Controllers\WarehouseManagementController::class, 'executeTransfer'])->name('warehouse.transfer.execute');
        Route::post('warehouse-management/archive/{id}', [App\Http\Controllers\WarehouseManagementController::class, 'archiveWarehouse'])->name('warehouse.management.archive');
        Route::post('warehouse-management/restore/{id}', [App\Http\Controllers\WarehouseManagementController::class, 'restoreWarehouse'])->name('warehouse.management.restore');
        Route::get('warehouse-management/archived', [App\Http\Controllers\WarehouseManagementController::class, 'archivedWarehouses'])->name('warehouse.management.archived');
        Route::post('warehouse-management/stock-arrival/{id}/assign', [App\Http\Controllers\WarehouseManagementController::class, 'assignStockArrival'])->name('warehouse.management.assign_arrival');
    });

    // Shop Inventory Management Routes - Admin and Inventory Clerk only
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::get('shop-inventory', [ShopInventoryController::class, 'index'])->name('shop.inventory');
        Route::get('shop-inventory/archived', [ShopInventoryController::class, 'archived'])->name('shop.inventory.archived');
        Route::get('shop-inventory/edit/{id}', [ShopInventoryController::class, 'edit'])->name('shop.inventory.edit');
        Route::get('shop-inventory/shelf/{id}/data', [ShopInventoryController::class, 'getShelfData'])->name('shop.inventory.get_shelf_data');
        Route::post('shop-inventory/shelf', [ShopInventoryController::class, 'createShelf'])->name('shop.inventory.create_shelf');
        Route::put('shop-inventory/shelf/{id}', [ShopInventoryController::class, 'updateShelf'])->name('shop.inventory.update_shelf');
        Route::delete('shop-inventory/shelf/{id}', [ShopInventoryController::class, 'archiveShelf'])->name('shop.inventory.archive_shelf');
        Route::post('shop-inventory/shelf/{id}/restore', [ShopInventoryController::class, 'restoreShelf'])->name('shop.inventory.restore_shelf');
        Route::delete('shop-inventory/shelf/{id}/permanent', [ShopInventoryController::class, 'deleteShelf'])->name('shop.inventory.delete_shelf');
        Route::get('shop-inventory/transfer/{shelfId}', [ShopInventoryController::class, 'showTransfer'])->name('shop.inventory.show_transfer');
        Route::post('shop-inventory/transfer/execute', [ShopInventoryController::class, 'executeTransfer'])->name('shop.inventory.execute_transfer');
    });

    // Shop Inventory API Routes - Remove role middleware temporarily for testing
    Route::get('api/shop-inventory/shelves', [ShopInventoryController::class, 'getShelves'])->name('api.shop.inventory.shelves');
    Route::get('api/shop-inventory/warehouse-shelves', [ShopInventoryController::class, 'getWarehouseShelves'])->name('api.shop.inventory.warehouse_shelves');
    Route::get('api/shop-inventory/warehouse-products', [ShopInventoryController::class, 'getWarehouseProducts'])->name('api.shop.inventory.warehouse_products');
    Route::get('api/shop-inventory/shop-sections', [ShopInventoryController::class, 'getShopSections'])->name('api.shop.inventory.shop_sections');
    Route::get('api/shop-inventory/shop-products', [ShopInventoryController::class, 'getShopProducts'])->name('api.shop.inventory.shop_products');
    Route::get('api/shop-inventory/brands', [ShopInventoryController::class, 'getBrands'])->name('api.shop.inventory.brands');
    Route::post('api/shop-inventory/transfer-from-warehouse', [ShopInventoryController::class, 'transferFromWarehouse'])->name('api.shop.inventory.transfer_from_warehouse');
    Route::post('api/shop-inventory/transfer-between-shelves', [ShopInventoryController::class, 'transferBetweenShelves'])->name('api.shop.inventory.transfer_between_shelves');
    Route::post('api/shop-inventory/return-to-warehouse', [ShopInventoryController::class, 'returnToWarehouse'])->name('api.shop.inventory.return_to_warehouse');
    Route::get('api/shop-inventory/history', [ShopInventoryController::class, 'getHistory'])->name('api.shop.inventory.history');

    // Shop Inventory API for POS - Admin and Cashier only
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('api/shop-inventory/products', [ShopInventoryController::class, 'getProductsForPOS'])->name('api.shop.inventory.products');
        Route::post('api/shop-inventory/deduct', [ShopInventoryController::class, 'deductFromShopInventory'])->name('api.shop.inventory.deduct');
    });

    // Purchase Order Routes - inventory clerk can create orders, admin can approve and send, warehouse and inventory can receive
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::get('purchase-order/management', [PurchaseOrderController::class, 'management'])->name('order.management');
        Route::get('purchase-order/create', [PurchaseOrderController::class, 'create'])->name('order.create');
        Route::get('purchase-order/history', [PurchaseOrderController::class, 'history'])->name('order.history');
        Route::post('purchase-order', [PurchaseOrderController::class, 'store'])->name('order.store');
        // Intelligent purchasing workflow APIs
        Route::get('api/purchase-order/filtered-suppliers', [PurchaseOrderController::class, 'filteredSuppliers'])->name('api.order.filtered_suppliers');
        Route::get('api/purchase-order/supplier-details', [PurchaseOrderController::class, 'supplierDetails'])->name('api.order.supplier_details');
        Route::get('api/purchase-order/supplier-comparison', [PurchaseOrderController::class, 'supplierComparison'])->name('api.order.supplier_comparison');
    });

    Route::middleware('role:admin,inventory_clerk,warehouse_personnel')->group(function () {
        Route::get('purchase-order/received', [PurchaseOrderController::class, 'receivedOrders'])->name('received.orders');
        Route::get('purchase-order/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('order.show');
        Route::post('purchase-order/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('order.receive');
        Route::post('purchase-order/{purchaseOrder}/confirm-receive', [PurchaseOrderController::class, 'confirmReceive'])->name('order.confirm_receive');
        Route::post('purchase-order/{purchaseOrder}/defective-request/{defectiveRequest}/resolve', [PurchaseOrderController::class, 'resolveDefectiveRequest'])->name('order.defective.resolve');
        Route::post('purchase-order/{purchaseOrder}/defective-request/{defectiveRequest}/receive-replacement', [PurchaseOrderController::class, 'receiveReplacement'])->name('order.defective.receive_replacement');
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('purchase-order/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('order.approve');
        Route::post('purchase-order/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('order.reject');
        Route::match(['get', 'post'], 'purchase-order/{purchaseOrder}/send', [PurchaseOrderController::class, 'sendToSupplier'])->name('order.send');
        Route::post('purchase-order/{purchaseOrder}/in-transit', [PurchaseOrderController::class, 'markInTransit'])->name('order.in_transit');
        Route::put('purchase-order/{purchaseOrder}/estimated-delivery-date', [PurchaseOrderController::class, 'updateEstimatedDeliveryDate'])->name('order.update_estimated_delivery');
    });

    // Offline Data Reconciliation Routes - Admin only
    Route::prefix('offline-reconciliation')->group(function () {
        Route::get('purchase-orders', [App\Http\Controllers\OfflineReconciliationController::class, 'index'])->name('offline.purchase-orders');
        Route::get('export', [App\Http\Controllers\ExportController::class, 'index'])->name('offline.export');
        Route::match(['get', 'post'], 'export/csv', [App\Http\Controllers\ExportController::class, 'exportCsv'])->name('offline.export.csv');
        Route::match(['get', 'post'], 'export/excel', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('offline.export.excel');
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
        Route::post('check-synced-orders', [App\Http\Controllers\OfflineReconciliationController::class, 'checkSyncedOrders'])->name('offline.check.synced');
    });

    // Supplier Assessment Route - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('supplier-assessment', [SupplierAssessmentController::class, 'index'])->name('supplier.assessment');
        Route::get('supplier-assessment/archived', [SupplierAssessmentController::class, 'archived'])->name('supplier.assessment.archived');
        Route::post('supplier-assessment/suppliers', [SupplierAssessmentController::class, 'store'])->name('supplier.assessment.store');
        Route::post('supplier-assessment/suppliers/{supplier}/restore', [SupplierAssessmentController::class, 'restore'])->name('supplier.assessment.restore');
        Route::patch('supplier-assessment/suppliers/{supplier}', [SupplierAssessmentController::class, 'update'])->name('supplier.assessment.update');
        Route::delete('supplier-assessment/suppliers/{supplier}', [SupplierAssessmentController::class, 'destroy'])->name('supplier.assessment.destroy');
        Route::delete('supplier-assessment/suppliers/{supplier}/force-delete', [SupplierAssessmentController::class, 'forceDelete'])->name('supplier.assessment.force-delete');
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
        Route::get('offline-reconciliation', [App\Http\Controllers\OfflineReconciliationController::class, 'overview'])->name('offline.reconciliation');
        Route::view('offline-reconciliation/test', 'offline_reconciliation.test-offline')->name('offline.reconciliation.test');
    });

    // Settings Routes - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::view('settings/general', 'settings.general')->name('settings.general');
        Route::view('settings/users', 'settings.users')->name('settings.users');
    });

    // ===========================
    // Decision Support System (DSS) Routes
    // Dead Stock Detection & Recommendation Engine
    // ===========================
    
    // Dead Stock Management - Admin and Inventory Clerk only
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::prefix('dss')->group(function () {
            // Dead Stock Routes
            Route::get('dead-stock', [App\Http\Controllers\DeadStockController::class, 'index'])->name('dss.dead-stock.index');
            Route::get('dead-stock/export/excel', [App\Http\Controllers\DeadStockController::class, 'exportExcel'])->name('dss.dead-stock.export-excel');
            Route::get('dead-stock/export/pdf', [App\Http\Controllers\DeadStockController::class, 'exportPdf'])->name('dss.dead-stock.export-pdf');
            Route::get('dead-stock/{id}', [App\Http\Controllers\DeadStockController::class, 'show'])->name('dss.dead-stock.show');
            Route::post('dead-stock/{id}/apply-discount', [App\Http\Controllers\DeadStockController::class, 'applyDiscount'])->name('dss.dead-stock.apply-discount');
            Route::post('dead-stock/{id}/remove-discount', [App\Http\Controllers\DeadStockController::class, 'removeDiscountByDeadStockId'])->name('dss.dead-stock.remove-discount');

            // Recommendation Routes
            Route::get('recommendations', [App\Http\Controllers\DSSRecommendationController::class, 'index'])->name('dss.recommendations.index');
            Route::post('recommendations/recalculate', [App\Http\Controllers\DSSRecommendationController::class, 'recalculate'])->name('dss.recommendations.recalculate');
            Route::get('recommendations/{id}', [App\Http\Controllers\DSSRecommendationController::class, 'show'])->name('dss.recommendations.show');
            Route::post('recommendations/{id}/action', [App\Http\Controllers\DSSRecommendationController::class, 'markActioned'])->name('dss.recommendations.action');
            Route::post('recommendations/{id}/apply-reorder', [App\Http\Controllers\DSSRecommendationController::class, 'applyReorderLevel'])->name('dss.recommendations.apply-reorder');

            // Settings Routes
            Route::get('settings', [App\Http\Controllers\DSSSettingsController::class, 'index'])->name('dss.settings.index');
            Route::post('settings', [App\Http\Controllers\DSSSettingsController::class, 'update'])->name('dss.settings.update');
        });
    });

    // DSS API Routes - Admin and Inventory Clerk
    Route::middleware('role:admin,inventory_clerk')->group(function () {
        Route::prefix('api/dss')->group(function () {
            // Dead Stock API
            Route::get('dead-stocks', [App\Http\Controllers\Api\DeadStockApiController::class, 'index'])->name('api.dss.dead-stocks.index');
            Route::get('dead-stocks/{id}', [App\Http\Controllers\Api\DeadStockApiController::class, 'show'])->name('api.dss.dead-stocks.show');
            Route::post('dead-stocks/recalculate', [App\Http\Controllers\Api\DeadStockApiController::class, 'recalculate'])->name('api.dss.dead-stocks.recalculate');
            Route::post('dead-stocks/{id}/resolve', [App\Http\Controllers\Api\DeadStockApiController::class, 'markResolved'])->name('api.dss.dead-stocks.resolve');
            Route::get('dead-stocks/priority/{priority}', [App\Http\Controllers\Api\DeadStockApiController::class, 'getByPriority'])->name('api.dss.dead-stocks.by-priority');
            Route::get('dead-stocks/{id}/sales-history', [App\Http\Controllers\Api\DeadStockApiController::class, 'salesHistory'])->name('api.dss.dead-stocks.sales-history');
            Route::get('dashboard-stats', [App\Http\Controllers\Api\DeadStockApiController::class, 'dashboardStats'])->name('api.dss.dashboard-stats');
            Route::get('dead-stocks/export/csv', [App\Http\Controllers\Api\DeadStockApiController::class, 'exportCsv'])->name('api.dss.dead-stocks.export-csv');
            Route::get('top-fast-moving', [App\Http\Controllers\Api\DeadStockApiController::class, 'getTopFastMoving'])->name('api.dss.top-fast-moving');

            // Recommendation API
            Route::post('recommendations/recalculate', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'recalculate'])->name('api.dss.recommendations.recalculate');
            Route::get('recommendations/product/{productId}', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'getByProduct'])->name('api.dss.recommendations.by-product');
            Route::get('recommendations/pending', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'getPending'])->name('api.dss.recommendations.pending');
            Route::get('recommendations/type/{type}', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'getByType'])->name('api.dss.recommendations.by-type');
            Route::post('recommendations/{id}/action', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'markActioned'])->name('api.dss.recommendations.action');
            Route::post('recommendations/{id}/apply-reorder', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'applyReorderLevel'])->name('api.dss.recommendations.apply-reorder');
            Route::get('recommendations/pending-count', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'pendingCount'])->name('api.dss.recommendations.pending-count');
            Route::get('recommendations/count-by-type', [App\Http\Controllers\Api\DSSRecommendationApiController::class, 'countByType'])->name('api.dss.recommendations.count-by-type');
        });
    });

    // DSS Settings API - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('api/dss/settings', [App\Http\Controllers\DSSSettingsController::class, 'getSettings'])->name('api.dss.settings');
    });
});
