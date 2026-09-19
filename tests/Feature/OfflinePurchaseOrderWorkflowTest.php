<?php

use App\Models\PendingImport;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Models\SynchronizationHistory;
use App\Models\User;
use App\Services\OfflineReconciliationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('it renders offline purchase orders page with products, suppliers, and assessment metrics', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create([
        'name' => 'Yamaha Genuine Parts',
        'contact_person' => 'Juan Dela Cruz',
        'email' => 'yamaha@example.com',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Yamalube 4T 10W-40',
        'product_name' => 'Yamalube 4T 10W-40',
        'sku' => 'YAM-LUBE-01',
        'category' => 'Oil',
        'brand' => 'Yamaha',
        'unit_price' => 250.00,
        'stock_quantity' => 5,
        'reorder_level' => 10,
        'supplier_name' => $supplier->name,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $product->suppliers()->attach($supplier->id);

    $response = $this->actingAs($admin)->get(route('offline.purchase-orders'));

    $response->assertStatus(200);
    $response->assertViewHas('products');
    $response->assertViewHas('suppliers');
    $response->assertViewHas('categories');
    $response->assertViewHas('brands');
    $response->assertSee('Offline Purchase Orders');
    $response->assertSee('Yamalube 4T 10W-40');
    $response->assertSee('Yamaha Genuine Parts');
});

test('it exports offline purchase orders without expected delivery date header', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create([
        'name' => 'Honda Parts PH',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Honda Brake Pad',
        'product_name' => 'Honda Brake Pad',
        'sku' => 'HND-BP-01',
        'unit_price' => 450.00,
        'stock_quantity' => 20,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-20260918-0001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'total_amount' => 9000.00,
        'status' => 'pending',
        'sync_status' => 'pending_sync',
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 20,
        'unit_price' => 450.00,
        'total_price' => 9000.00,
    ]);

    $response = $this->actingAs($admin)->get(route('offline.export.csv'));
    $response->assertStatus(200);

    $content = $response->streamedContent();
    expect($content)->toContain('PO-20260918-0001');
    expect($content)->toContain('Honda Brake Pad');
    expect($content)->not->toContain('Expected Delivery Date');
});

test('it validates offline import and flags ineligible supplier products', function () {
    $service = app(OfflineReconciliationService::class);

    $supplierA = Supplier::create([
        'name' => 'Supplier Alpha',
        'status' => 'active',
    ]);

    $supplierB = Supplier::create([
        'name' => 'Supplier Beta',
        'status' => 'active',
    ]);

    $productA = Product::create([
        'name' => 'Alpha Spark Plug',
        'product_name' => 'Alpha Spark Plug',
        'sku' => 'SPK-A',
        'unit_price' => 120.00,
        'stock_quantity' => 10,
        'supplier_name' => $supplierA->name,
        'is_active' => true,
    ]);
    $productA->suppliers()->attach($supplierA->id);

    // Record with Supplier Beta (ineligible for Product A)
    $ineligibleRecord = [
        'type' => 'purchase_order',
        'order_number' => 'PO-INELIGIBLE-01',
        'supplier_id' => $supplierB->id,
        'supplier_name' => $supplierB->name,
        'total_amount' => 600.00,
        'items' => [
            [
                'product_id' => $productA->id,
                'product_name' => $productA->name,
                'sku' => $productA->sku,
                'quantity' => 5,
                'unit_price' => 120.00,
                'subtotal' => 600.00,
            ]
        ]
    ];

    $validation = $service->validateImport([$ineligibleRecord]);
    expect(count($validation['invalid']))->toBe(1);
    expect(count($validation['valid']))->toBe(0);
    expect(implode(' ', $validation['invalid'][0]['errors']))->toContain('not authorized to supply product');
});

test('it flags items with invalid or zero purchase price during offline import validation', function () {
    $service = app(OfflineReconciliationService::class);

    $supplier = Supplier::create([
        'name' => 'Zero Price Supplier',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Unpriced Spark Plug',
        'product_name' => 'Unpriced Spark Plug',
        'sku' => 'SPK-ZERO',
        'unit_price' => 0.00,
        'stock_quantity' => 10,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);
    $product->suppliers()->attach($supplier->id);

    $unpricedRecord = [
        'type' => 'purchase_order',
        'order_number' => 'PO-UNPRICED-01',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'total_amount' => 0.00,
        'items' => [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'quantity' => 5,
                'unit_price' => 0.00,
                'subtotal' => 0.00,
            ]
        ]
    ];

    $validation = $service->validateImport([$unpricedRecord]);
    expect(count($validation['invalid']))->toBe(1);
    expect(implode(' ', $validation['invalid'][0]['errors']))->toContain('does not have a valid purchase price');
});

test('it applies supplier-specific purchase price from price history during validation and import', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplierA = Supplier::create([
        'name' => 'Tire Supplier A',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Motorcycle Tire 17-inch',
        'product_name' => 'Motorcycle Tire 17-inch',
        'sku' => 'TIRE-17',
        'unit_price' => 1200.00,
        'stock_quantity' => 10,
        'supplier_name' => $supplierA->name,
        'is_active' => true,
    ]);
    $product->suppliers()->attach($supplierA->id);

    // Supplier A specific price in SupplierPriceHistory is 1500.00
    SupplierPriceHistory::create([
        'product_id' => $product->id,
        'supplier_id' => $supplierA->id,
        'previous_cost' => 1400.00,
        'supplier_cost' => 1500.00,
        'change_percentage' => 7.14,
    ]);

    $service = app(OfflineReconciliationService::class);

    // Import payload with recorded price snapshot of 1500.00
    $record = [
        'type' => 'purchase_order',
        'order_number' => 'PO-TIRE-001',
        'supplier_id' => $supplierA->id,
        'supplier_name' => $supplierA->name,
        'total_amount' => 15000.00,
        'items' => [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'quantity' => 10,
                'unit_price' => 1500.00,
                'subtotal' => 15000.00,
            ]
        ]
    ];

    $validation = $service->validateImport([$record]);
    expect(count($validation['valid']))->toBe(1);
    expect(count($validation['invalid']))->toBe(0);

    // Sync records
    $service->importRecords($validation['valid'], 'test_tire_import.csv', $admin->id);

    $po = PurchaseOrder::where('order_number', 'PO-TIRE-001')->with('items')->first();
    expect($po)->not->toBeNull();
    expect((float) $po->total_amount)->toBe(15000.0);
    expect((float) $po->items->first()->unit_price)->toBe(1500.0);
    expect((float) $po->items->first()->total_price)->toBe(15000.0);
});

test('it validates offline import and detects duplicate orders in database and file batch', function () {
    $service = app(OfflineReconciliationService::class);

    $supplier = Supplier::create([
        'name' => 'Castrol Oil Supply',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Castrol Power 1',
        'product_name' => 'Castrol Power 1',
        'sku' => 'CAS-01',
        'unit_price' => 300.00,
        'stock_quantity' => 10,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);
    $product->suppliers()->attach($supplier->id);

    // Existing PO in DB
    PurchaseOrder::create([
        'order_number' => 'PO-EXISTING-01',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'total_amount' => 3000.00,
        'status' => 'pending',
        'sync_status' => 'synchronized',
    ]);

    $records = [
        // 1. Existing DB duplicate
        [
            'type' => 'purchase_order',
            'order_number' => 'PO-EXISTING-01',
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'total_amount' => 3000.00,
            'items' => [
                ['product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku, 'quantity' => 10, 'unit_price' => 300.00, 'subtotal' => 3000.00]
            ]
        ],
        // 2. Valid PO
        [
            'type' => 'purchase_order',
            'order_number' => 'PO-NEW-BATCH-01',
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'total_amount' => 1500.00,
            'items' => [
                ['product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku, 'quantity' => 5, 'unit_price' => 300.00, 'subtotal' => 1500.00]
            ]
        ],
        // 3. Batch duplicate of #2
        [
            'type' => 'purchase_order',
            'order_number' => 'PO-NEW-BATCH-01',
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'total_amount' => 1500.00,
            'items' => [
                ['product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku, 'quantity' => 5, 'unit_price' => 300.00, 'subtotal' => 1500.00]
            ]
        ]
    ];

    $results = $service->validateImport($records);
    expect(count($results['valid']))->toBe(1);
    expect(count($results['duplicates']))->toBe(2);
});

test('it stages CSV upload into pending imports and allows admin approval', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create([
        'name' => 'Suzuki Genuine Parts',
        'status' => 'active',
    ]);

    $product1 = Product::create([
        'name' => 'Suzuki Air Filter',
        'product_name' => 'Suzuki Air Filter',
        'sku' => 'SZK-AF-01',
        'unit_price' => 200.00,
        'stock_quantity' => 10,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);
    $product1->suppliers()->attach($supplier->id);

    $product2 = Product::create([
        'name' => 'Suzuki Drive Belt',
        'product_name' => 'Suzuki Drive Belt',
        'sku' => 'SZK-DB-01',
        'unit_price' => 800.00,
        'stock_quantity' => 5,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);
    $product2->suppliers()->attach($supplier->id);

    // Create a 2-item CSV
    $csvContent = implode("\n", [
        'type,order_number,product_id,product_name,sku,supplier_id,supplier_name,quantity,quantity_change,unit_price,subtotal,total_amount,status,sync_status,movement_type,notes,created_at,updated_at',
        "purchase_order,PO-MULTI-100,{$product1->id},{$product1->name},{$product1->sku},{$supplier->id},{$supplier->name},5,,200.00,1000.00,2600.00,pending,exported,,Bulk order,2026-09-18 10:00:00,2026-09-18 10:00:00",
        "purchase_order,PO-MULTI-100,{$product2->id},{$product2->name},{$product2->sku},{$supplier->id},{$supplier->name},2,,800.00,1600.00,2600.00,pending,exported,,Bulk order,2026-09-18 10:00:00,2026-09-18 10:00:00",
    ]);

    $file = UploadedFile::fake()->createWithContent('offline_orders.csv', $csvContent);

    // 1. Upload file
    $uploadResponse = $this->actingAs($admin)
        ->from(route('offline.import'))
        ->post(route('offline.import.store'), [
            'file' => $file,
        ]);

    $uploadResponse->assertRedirect(route('offline.import'));
    $uploadResponse->assertSessionHas('success');

    $pending = PendingImport::where('file_name', 'offline_orders.csv')->first();
    expect($pending)->not->toBeNull();
    expect($pending->total_records)->toBe(1);
    expect($pending->valid_records)->toBe(1);
    expect($pending->status)->toBe('pending');

    // 2. Review endpoint
    $reviewResponse = $this->actingAs($admin)->get("/offline-reconciliation/pending-imports/{$pending->id}/review");
    $reviewResponse->assertStatus(200);
    $reviewResponse->assertJsonFragment(['success' => true]);
    $reviewData = $reviewResponse->json();
    expect($reviewData['html'])->toContain('PO-MULTI-100');
    expect($reviewData['html'])->toContain('Suzuki Air Filter');
    expect($reviewData['html'])->toContain('Suzuki Drive Belt');

    // 3. Approve staged import
    $approveResponse = $this->actingAs($admin)
        ->from(route('offline.pending.imports'))
        ->post(route('offline.pending.approve', $pending->id));
    $approveResponse->assertRedirect(route('offline.pending.imports'));
    $approveResponse->assertSessionHas('success');

    $pending->refresh();
    expect($pending->status)->toBe('approved');
    expect($pending->reviewed_by)->toBe($admin->id);

    // 4. Verify DB records created
    $createdPO = PurchaseOrder::where('order_number', 'PO-MULTI-100')->with('items')->first();
    expect($createdPO)->not->toBeNull();
    expect($createdPO->sync_status)->toBe('synchronized');
    expect($createdPO->supplier_name)->toBe($supplier->name);
    expect((float) $createdPO->total_amount)->toBe(2600.0);
    expect($createdPO->items->count())->toBe(2);

    // 5. Verify synchronization history recorded
    $history = SynchronizationHistory::where('file_name', 'offline_orders.csv')->first();
    expect($history)->not->toBeNull();
    expect($history->synchronization_status)->toBe('completed');
});

test('it syncs individual offline order via sync-order endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create([
        'name' => 'Kawasaki Genuine Parts',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Kawasaki Spark Plug',
        'product_name' => 'Kawasaki Spark Plug',
        'sku' => 'KWK-SP-01',
        'unit_price' => 150.00,
        'stock_quantity' => 15,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);
    $product->suppliers()->attach($supplier->id);

    $payload = [
        'order_number' => 'PO-SYNC-SINGLE-01',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'total_amount' => 1500.00,
        'notes' => 'Online sync test',
        'items' => [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'quantity' => 10,
                'unit_price' => 150.00,
                'subtotal' => 1500.00,
            ]
        ]
    ];

    $response = $this->actingAs($admin)->postJson(route('offline.sync.order'), $payload);
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $po = PurchaseOrder::where('order_number', 'PO-SYNC-SINGLE-01')->with('items')->first();
    expect($po)->not->toBeNull();
    expect($po->sync_status)->toBe('synchronized');
    expect($po->items->count())->toBe(1);
    expect($po->items->first()->quantity)->toBe(10);
});

test('it renders offline reconciliation overview page with database orders and stats', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create([
        'name' => 'Suzuki Parts Center',
        'status' => 'active',
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-OVERVIEW-001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'total_amount' => 4500.00,
        'status' => 'pending',
        'sync_status' => 'synchronized',
    ]);

    $response = $this->actingAs($admin)->get(route('offline.reconciliation'));

    $response->assertStatus(200);
    $response->assertViewHas('purchaseOrders');
    $response->assertViewHas('stats');
    $response->assertSee('Database Orders (System Records)');
    $response->assertSee('PO-OVERVIEW-001');
    $response->assertSee('Suzuki Parts Center');
});

test('it renders offline export data page with local order selection and date filters', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('offline.export'));

    $response->assertStatus(200);
    $response->assertSee('Export Data');
    $response->assertSee('Select Local Orders to Export');
    $response->assertSee('Export The Selected Orders');
});

test('it passes categories and categoryBrandsMap to offline purchase orders page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    \App\Models\ProductDescription::create([
        'name' => 'Pipe',
        'sku_prefix' => 'PIP',
        'brands' => ['APIDO', 'TRC', 'KVIN'],
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('offline.purchase-orders'));

    $response->assertStatus(200);
    $response->assertViewHas('categories');
    $response->assertViewHas('brands');
    $response->assertViewHas('categoryBrandsMap');

    $categories = $response->viewData('categories');
    expect($categories->contains('Pipe'))->toBeTrue();

    $categoryBrandsMap = $response->viewData('categoryBrandsMap');
    expect($categoryBrandsMap)->toHaveKey('Pipe');
    expect($categoryBrandsMap['Pipe'])->toContain('APIDO');
});

