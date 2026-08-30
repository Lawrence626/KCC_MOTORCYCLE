<?php

use App\Http\Controllers\StockImportController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

describe('product editing', function () {
    it('updates only the changed fields and preserves the existing values', function () {
        $product = Product::create([
            'name' => 'Yamaha R15',
            'product_name' => 'Sport Bike',
            'sku' => 'SKU-001',
            'brand' => 'Yamaha',
            'size' => '150cc',
            'color' => 'Blue',
            'stock_quantity' => 10,
            'unit_price' => 150.00,
            'supplier_name' => 'KCC Parts',
            'category' => 'engine_oil',
            'last_restock_date' => '2025-01-15',
            'expiry_date' => '2026-12-31',
            'reorder_level' => 5,
            'barcode' => 'ABC123',
            'description' => 'Original part',
            'is_active' => true,
            'is_archived' => false,
        ]);

        $request = new Request([
            'product_name' => 'Updated Sport Bike',
        ]);

        $response = (new StockImportController())->updateProduct($request, $product->id);

        expect($response->getStatusCode())->toBe(200);

        $payload = json_decode($response->getContent(), true);
        expect($payload['success'])->toBeTrue();

        $product->refresh();
        expect($product->product_name)->toBe('Updated Sport Bike');
        expect($product->name)->toBe('Yamaha R15');
        expect($product->sku)->toBe('SKU-001');
        expect($product->stock_quantity)->toBe(10);
        expect(floatval($product->unit_price))->toBe(150.00);
    });

    it('updates multiple suppliers via supplier_ids and syncs the supplier_products pivot table', function () {
        $supplier1 = \App\Models\Supplier::create(['name' => 'Honda Motorcycle Parts', 'status' => 'active']);
        $supplier2 = \App\Models\Supplier::create(['name' => 'Yamaha Philippines', 'status' => 'active']);
        $supplier3 = \App\Models\Supplier::create(['name' => 'Kawasaki Motors Supply', 'status' => 'active']);

        $product = Product::create([
            'name' => 'Click 125',
            'product_name' => 'Honda Click 125i',
            'sku' => 'SKU-CLICK-125',
            'stock_quantity' => 20,
            'unit_price' => 250.00,
        ]);

        $request = new Request([
            'supplier_ids' => [$supplier1->id, $supplier2->id],
        ]);

        $response = (new StockImportController())->updateProduct($request, $product->id);
        expect($response->getStatusCode())->toBe(200);

        $payload = json_decode($response->getContent(), true);
        expect($payload['success'])->toBeTrue();

        $product->refresh();
        $linkedSupplierIds = $product->suppliers->pluck('id')->all();
        expect($linkedSupplierIds)->toContain($supplier1->id)
            ->and($linkedSupplierIds)->toContain($supplier2->id)
            ->and($linkedSupplierIds)->not->toContain($supplier3->id);

        expect($product->supplier_name)->toBe('Honda Motorcycle Parts, Yamaha Philippines');

        // Test updating/deselection
        $request2 = new Request([
            'supplier_ids' => [$supplier3->id],
        ]);
        $response2 = (new StockImportController())->updateProduct($request2, $product->id);
        expect($response2->getStatusCode())->toBe(200);

        $product->refresh();
        expect($product->suppliers->pluck('id')->all())->toBe([$supplier3->id]);
        expect($product->supplier_name)->toBe('Kawasaki Motors Supply');
    });

    it('returns linked suppliers when fetching product via api/products/{id}', function () {
        $supplier1 = \App\Models\Supplier::create(['name' => 'Honda Motorcycle Parts', 'status' => 'active']);
        $supplier2 = \App\Models\Supplier::create(['name' => 'Yamaha Philippines', 'status' => 'active']);

        $product = Product::create([
            'name' => 'Aerox 155',
            'product_name' => 'Yamaha Aerox',
            'sku' => 'SKU-AEROX-155',
            'stock_quantity' => 15,
            'unit_price' => 300.00,
        ]);
        $product->suppliers()->sync([$supplier1->id, $supplier2->id]);

        $response = (new StockImportController())->getProduct($product->id);
        expect($response->getStatusCode())->toBe(200);

        $payload = json_decode($response->getContent(), true);
        expect($payload['success'])->toBeTrue();
        expect(count($payload['product']['suppliers']))->toBe(2);
        $names = array_column($payload['product']['suppliers'], 'name');
        expect($names)->toContain('Honda Motorcycle Parts')
            ->and($names)->toContain('Yamaha Philippines');
    });

    it('fetches active suppliers from api/suppliers', function () {
        \App\Models\Supplier::create(['name' => 'Honda Motorcycle Parts', 'status' => 'active']);
        \App\Models\Supplier::create(['name' => 'Yamaha Philippines', 'status' => 'active']);
        \App\Models\Supplier::create(['name' => 'Inactive Supplier', 'status' => 'inactive']);

        $response = (new StockImportController())->getSuppliers();
        expect($response->getStatusCode())->toBe(200);

        $payload = json_decode($response->getContent(), true);
        expect($payload['success'])->toBeTrue();
        $names = array_column($payload['suppliers'], 'name');
        expect($names)->toContain('Honda Motorcycle Parts')
            ->and($names)->toContain('Yamaha Philippines')
            ->and($names)->not->toContain('Inactive Supplier');
    });

    it('allows clearing all suppliers by passing empty supplier_ids array', function () {
        $supplier1 = \App\Models\Supplier::create(['name' => 'Honda Motorcycle Parts', 'status' => 'active']);
        $product = Product::create([
            'name' => 'Mio Sporty',
            'product_name' => 'Yamaha Mio',
            'sku' => 'SKU-MIO',
            'stock_quantity' => 5,
            'unit_price' => 200.00,
        ]);
        $product->suppliers()->sync([$supplier1->id]);

        $request = new Request(['supplier_ids' => []]);
        $response = (new StockImportController())->updateProduct($request, $product->id);
        expect($response->getStatusCode())->toBe(200);

        $product->refresh();
        expect($product->suppliers)->toBeEmpty();
        expect($product->supplier_name)->toBeNull();
    });

    it('treats all selected suppliers equally without a default supplier', function () {
        $s1 = \App\Models\Supplier::create(['name' => 'Honda Motorcycle Parts', 'status' => 'active']);
        $s2 = \App\Models\Supplier::create(['name' => 'Yamaha Philippines', 'status' => 'active']);
        $s3 = \App\Models\Supplier::create(['name' => 'Kawasaki Motors Supply', 'status' => 'active']);
        $s4 = \App\Models\Supplier::create(['name' => 'Suzuki Parts Depot', 'status' => 'active']);
        $s5 = \App\Models\Supplier::create(['name' => 'Universal Motorcycle Parts', 'status' => 'active']);

        $product = Product::create([
            'name' => 'NMAX 155',
            'product_name' => 'Yamaha NMAX',
            'sku' => 'SKU-NMAX',
            'stock_quantity' => 8,
            'unit_price' => 350.00,
        ]);

        $request = new Request([
            'supplier_ids' => [$s1->id, $s2->id, $s3->id, $s4->id, $s5->id],
        ]);

        $response = (new StockImportController())->updateProduct($request, $product->id);
        expect($response->getStatusCode())->toBe(200);

        $product->refresh();
        expect($product->suppliers->count())->toBe(5);

        // Verify each supplier is linked in supplier_products pivot table
        $pivotCount = \Illuminate\Support\Facades\DB::table('supplier_products')
            ->where('product_id', $product->id)
            ->count();
        expect($pivotCount)->toBe(5);
    });
});
