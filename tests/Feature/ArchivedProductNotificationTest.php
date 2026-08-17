<?php

use App\Models\InventoryNotification;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it does not create notifications for archived products even when out of stock or low stock', function () {
    $alertService = app(InventoryAlertService::class);

    // Create an archived product with 0 stock
    $archivedProduct = Product::create([
        'name' => 'Archived Exhaust',
        'product_name' => 'Archived Exhaust',
        'sku' => 'ARCH-001',
        'category' => 'Exhaust',
        'unit_price' => 500,
        'stock_quantity' => 0,
        'reorder_level' => 10,
        'is_active' => true,
        'is_archived' => true,
    ]);

    $alertService->syncAlerts();

    // Verify no notifications exist for archived product
    $notifications = InventoryNotification::where('product_id', $archivedProduct->id)->get();
    expect($notifications)->toBeEmpty();

    $dashboardAlerts = $alertService->getDashboardAlerts();
    expect($dashboardAlerts->contains('product_id', $archivedProduct->id))->toBeFalse();

    $notificationItems = $alertService->getNotificationCenterItems();
    expect($notificationItems->contains('product_id', $archivedProduct->id))->toBeFalse();

    expect($alertService->getUnreadCount())->toBe(0);
});

test('it immediately removes active notification when a product is archived', function () {
    $alertService = app(InventoryAlertService::class);

    // Create active product with low stock
    $product = Product::create([
        'name' => 'Active Brake Rotor',
        'product_name' => 'Active Brake Rotor',
        'sku' => 'BRK-001',
        'category' => 'Braking',
        'unit_price' => 800,
        'stock_quantity' => 2,
        'reorder_level' => 5,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // Notification should be created
    $alertService->syncProductAlert($product);

    $notif = InventoryNotification::where('product_id', $product->id)->first();
    expect($notif)->not->toBeNull()
        ->and($notif->notification_type)->toBe('low_stock')
        ->and($alertService->getUnreadCount())->toBe(1);

    // Now archive the product
    $product->update(['is_archived' => true]);

    // Active notification must be immediately removed
    $notifAfter = InventoryNotification::where('product_id', $product->id)->first();
    expect($notifAfter)->toBeNull();

    // Verify not in dashboard alerts or notification center
    $dashboardAlerts = $alertService->getDashboardAlerts();
    expect($dashboardAlerts->contains('product_id', $product->id))->toBeFalse();

    expect($alertService->getUnreadCount())->toBe(0);
});

test('it restores notification when an archived product with low or zero stock is restored', function () {
    $alertService = app(InventoryAlertService::class);

    // Create archived product with 0 stock
    $product = Product::create([
        'name' => 'Archived Clutch Cable',
        'product_name' => 'Archived Clutch Cable',
        'sku' => 'CL-001',
        'category' => 'Controls',
        'unit_price' => 150,
        'stock_quantity' => 0,
        'reorder_level' => 10,
        'is_active' => true,
        'is_archived' => true,
    ]);

    expect(InventoryNotification::where('product_id', $product->id)->count())->toBe(0);

    // Restore the product
    $product->update(['is_archived' => false]);

    // Notification should now be created
    $notif = InventoryNotification::where('product_id', $product->id)->first();
    expect($notif)->not->toBeNull()
        ->and($notif->notification_type)->toBe('out_of_stock')
        ->and($alertService->getUnreadCount())->toBe(1);

    $dashboardAlerts = $alertService->getDashboardAlerts();
    expect($dashboardAlerts->contains('product_id', $product->id))->toBeTrue();
});

test('it does not create notification upon restoring if product stock is healthy', function () {
    $alertService = app(InventoryAlertService::class);

    // Create archived product with healthy stock (stock > reorder_level)
    $product = Product::create([
        'name' => 'Archived Healthy Item',
        'product_name' => 'Archived Healthy Item',
        'sku' => 'HLTH-001',
        'category' => 'Accessories',
        'unit_price' => 200,
        'stock_quantity' => 50,
        'reorder_level' => 10,
        'is_active' => true,
        'is_archived' => true,
    ]);

    // Restore the product
    $product->update(['is_archived' => false]);

    // No notification should be created
    expect(InventoryNotification::where('product_id', $product->id)->count())->toBe(0)
        ->and($alertService->getUnreadCount())->toBe(0);
});

test('api endpoints exclude archived product notifications from dropdown and notification center', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    // Active low stock product
    $activeProduct = Product::create([
        'name' => 'Active Chain',
        'product_name' => 'Active Chain',
        'sku' => 'CHN-001',
        'category' => 'Drivetrain',
        'unit_price' => 350,
        'stock_quantity' => 1,
        'reorder_level' => 5,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // Archived out of stock product
    $archivedProduct = Product::create([
        'name' => 'Archived Sprocket',
        'product_name' => 'Archived Sprocket',
        'sku' => 'SPK-001',
        'category' => 'Drivetrain',
        'unit_price' => 450,
        'stock_quantity' => 0,
        'reorder_level' => 10,
        'is_active' => true,
        'is_archived' => true,
    ]);

    $alertService = app(InventoryAlertService::class);
    $alertService->syncAlerts();

    // Call API endpoints
    $responseIndex = $this->actingAs($admin)->getJson(route('api.inventory-notifications.index'));
    $responseIndex->assertStatus(200);
    $dataIndex = $responseIndex->json();

    $responseDashboard = $this->actingAs($admin)->getJson(route('api.inventory-notifications.dashboard'));
    $responseDashboard->assertStatus(200);
    $dataDashboard = $responseDashboard->json();

    $responseUnread = $this->actingAs($admin)->getJson(route('api.inventory-notifications.unread-count'));
    $responseUnread->assertStatus(200);
    $dataUnread = $responseUnread->json();

    // Verify Active Chain is present, but Archived Sprocket is NOT
    $notifProductIds = collect($dataIndex['notifications'])->pluck('product_id')->all();
    expect($notifProductIds)->toContain($activeProduct->id)
        ->and($notifProductIds)->not->toContain($archivedProduct->id);

    $dashProductIds = collect($dataDashboard['alerts'])->pluck('product_id')->all();
    expect($dashProductIds)->toContain($activeProduct->id)
        ->and($dashProductIds)->not->toContain($archivedProduct->id);

    expect($dataUnread['unread_count'])->toBe(1);
});
