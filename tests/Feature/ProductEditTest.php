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
});
