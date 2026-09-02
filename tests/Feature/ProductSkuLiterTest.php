<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductCatalogController;
use App\Models\ProductCatalog;
use App\Models\ProductDescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProductSkuLiterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create product descriptions
        ProductDescription::firstOrCreate(['name' => 'Engine Oil'], ['sku_prefix' => 'ENGINE_OIL', 'brands' => ['Motul', 'Castrol'], 'is_active' => true]);
        ProductDescription::firstOrCreate(['name' => 'Gear Oil'], ['sku_prefix' => 'GEAR_OIL', 'brands' => ['Motul'], 'is_active' => true]);
        ProductDescription::firstOrCreate(['name' => 'Pipe'], ['sku_prefix' => 'PIPE', 'brands' => ['Apido'], 'is_active' => true]);
        ProductDescription::firstOrCreate(['name' => 'Tire'], ['sku_prefix' => 'TIRE', 'brands' => ['Pirelli'], 'is_active' => true]);
    }

    public function test_normalizes_liter_and_milliliter_slugs_correctly(): void
    {
        $this->assertEquals('1L', ProductCatalogController::normalizeSizeSlug('1L', 'Engine Oil'));
        $this->assertEquals('1L', ProductCatalogController::normalizeSizeSlug('1 L', 'Engine Oil'));
        $this->assertEquals('1L', ProductCatalogController::normalizeSizeSlug('1 Liter', 'Engine Oil'));
        $this->assertEquals('1L', ProductCatalogController::normalizeSizeSlug('1000ml', 'Engine Oil'));
        $this->assertEquals('1L', ProductCatalogController::normalizeSizeSlug('1', 'Engine Oil'));

        $this->assertEquals('800ML', ProductCatalogController::normalizeSizeSlug('800mL', 'Engine Oil'));
        $this->assertEquals('800ML', ProductCatalogController::normalizeSizeSlug('800 ml', 'Engine Oil'));
        $this->assertEquals('800ML', ProductCatalogController::normalizeSizeSlug('0.8L', 'Engine Oil'));
        $this->assertEquals('800ML', ProductCatalogController::normalizeSizeSlug('800', 'Engine Oil'));

        $this->assertEquals('1.2L', ProductCatalogController::normalizeSizeSlug('1.2L', 'Engine Oil'));
        $this->assertEquals('120ML', ProductCatalogController::normalizeSizeSlug('120ml', 'Gear Oil'));
        $this->assertEquals('500ML', ProductCatalogController::normalizeSizeSlug('500mL', 'Brake Fluid (Brake Oil)'));
        $this->assertEquals('4L', ProductCatalogController::normalizeSizeSlug('4L', 'Engine Oil'));
    }

    public function test_extracts_volume_from_product_name_if_size_empty(): void
    {
        $this->assertEquals('800ML', ProductCatalogController::normalizeSizeSlug('', 'Engine Oil', 'Motul 3100 (800mL)'));
        $this->assertEquals('1L', ProductCatalogController::normalizeSizeSlug('', 'Engine Oil', 'Motul 5100 10W-40 (1L)'));
        $this->assertEquals('1.2L', ProductCatalogController::normalizeSizeSlug('', 'Engine Oil', 'Yamalube (1.2L)'));
    }

    public function test_generates_sku_with_liter_for_oil_via_api(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // 1. Engine Oil 1L Motul
        $response1 = $this->actingAs($user)->getJson(route('product-catalog.generate-sku', [
            'product_description' => 'Engine Oil',
            'brand' => 'Motul',
            'size' => '1L',
        ]));
        $response1->assertOk()
            ->assertJsonPath('sku', 'KCC_ENGINE_OIL_MOTUL_1L_001');

        // 2. Engine Oil 800mL Motul
        $response2 = $this->actingAs($user)->getJson(route('product-catalog.generate-sku', [
            'product_description' => 'Engine Oil',
            'brand' => 'Motul',
            'size' => '800mL',
        ]));
        $response2->assertOk()
            ->assertJsonPath('sku', 'KCC_ENGINE_OIL_MOTUL_800ML_001');

        // 3. Gear Oil 120ml Motul
        $response3 = $this->actingAs($user)->getJson(route('product-catalog.generate-sku', [
            'product_description' => 'Gear Oil',
            'brand' => 'Motul',
            'size' => '120ml',
        ]));
        $response3->assertOk()
            ->assertJsonPath('sku', 'KCC_GEAR_OIL_MOTUL_120ML_001');
    }

    public function test_can_create_general_product_without_motorcycle_models(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('product-catalog.store'), [
            'product_name' => 'Motul 7100 4T 10W-40',
            'brand' => 'Motul',
            'product_description' => 'Engine Oil',
            'sku' => 'KCC_ENGINE_OIL_MOTUL_1L_099',
            'warehouse' => 'Warehouse A',
            'size' => '1L',
            'status' => 'Active',
            'is_general' => '1',
            'stock_quantity' => 20,
            'reorder_level' => 10,
        ]);

        $response->assertRedirect(route('product-catalog.index'));
        $this->assertDatabaseHas('product_catalog', [
            'sku' => 'KCC_ENGINE_OIL_MOTUL_1L_099',
            'warehouse' => 'Warehouse A',
            'size' => '1L',
        ]);

        $product = ProductCatalog::where('sku', 'KCC_ENGINE_OIL_MOTUL_1L_099')->first();
        $this->assertCount(0, $product->motorcycleModels);
    }

    public function test_non_general_product_requires_motorcycle_models(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('product-catalog.store'), [
            'product_name' => 'Apido Racing Pipe',
            'brand' => 'Apido',
            'product_description' => 'Pipe',
            'sku' => 'KCC_PIPE_APIDO_999',
            'warehouse' => 'Warehouse B',
            'status' => 'Active',
            'is_general' => '0',
            'stock_quantity' => 5,
        ]);

        $response->assertSessionHasErrors('motorcycle_models');
    }

    public function test_create_and_edit_views_provide_warehouses(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $responseCreate = $this->actingAs($user)->get(route('product-catalog.create'));
        $responseCreate->assertOk()
            ->assertViewHas('warehouses');

        $product = ProductCatalog::create([
            'product_name' => 'Motul 3000',
            'brand' => 'Motul',
            'product_description' => 'Engine Oil',
            'sku' => 'KCC_ENGINE_OIL_MOTUL_800ML_998',
            'warehouse' => 'Warehouse C',
            'size' => '800mL',
            'status' => 'Active',
        ]);

        $responseEdit = $this->actingAs($user)->get(route('product-catalog.edit', $product));
        $responseEdit->assertOk()
            ->assertViewHas('warehouses');
    }

    public function test_warehouse_is_required_when_storing_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('product-catalog.store'), [
            'product_name' => 'Motul 7100 4T 10W-40',
            'brand' => 'Motul',
            'product_description' => 'Engine Oil',
            'sku' => 'KCC_ENGINE_OIL_MOTUL_1L_098',
            'warehouse' => '',
            'size' => '1L',
            'status' => 'Active',
            'is_general' => '1',
        ]);

        $response->assertSessionHasErrors('warehouse');
    }
}
