<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Admin can access all analytics routes ──────────────────────────────────

test('admin can access sales analytics (not blocked by middleware)', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // The sales controller uses MySQL DATE_FORMAT which errors in SQLite test DB,
    // so we verify middleware does NOT redirect admin (i.e. admin is authorized).
    // A redirect would mean middleware blocked access.
    $response = $this->actingAs($admin)->get(route('sales.analytics'));
    $this->assertNotEquals(302, $response->status(), 'Admin should not be redirected from sales analytics');
});

test('admin can access pricing module', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('pricing.module'))
        ->assertStatus(200);
});

test('admin can access overstocking report', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('overstocking.report'))
        ->assertStatus(200);
});

test('admin can access out of stock report', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('out.of.stock'))
        ->assertStatus(200);
});

test('admin sidebar shows Data Analytics with all five items', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Data Analytics');
    $response->assertSee('Sales Analytics');
    $response->assertSee('Pricing Module');
    $response->assertSee('Overstocking Report');
    $response->assertSee('Out of Stock Report');
    $response->assertSee('Dead Stock Analysis');
});

// ── Inventory Clerk is blocked from admin-only analytics routes ────────────

test('inventory clerk is redirected from sales analytics', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('sales.analytics'))
        ->assertRedirect(route('dashboard'));
});

test('inventory clerk is redirected from pricing module', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('pricing.module'))
        ->assertRedirect(route('dashboard'));
});

test('inventory clerk is redirected from sales analytics api', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('api.analytics.sales_widgets'))
        ->assertRedirect(route('dashboard'));
});

test('inventory clerk is redirected from sales export', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('analytics.sales.export'))
        ->assertRedirect(route('dashboard'));
});

// ── Inventory Clerk CAN access inventory-focused analytics ─────────────────

test('inventory clerk can access overstocking report', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('overstocking.report'))
        ->assertStatus(200)
        ->assertSee('Overstocking');
});

test('inventory clerk can access out of stock report', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('out.of.stock'))
        ->assertStatus(200);
});

test('inventory clerk can access dead stock analysis', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $this->actingAs($clerk)
        ->get(route('dss.dead-stock.index'))
        ->assertStatus(200);
});

// ── Inventory Clerk sidebar shows Inventory Analytics (not Data Analytics) ──

test('inventory clerk sidebar shows Inventory Analytics with correct items', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $response = $this->actingAs($clerk)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Inventory Analytics');
    $response->assertSee('Overstocking Report');
    $response->assertSee('Out of Stock Report');
    $response->assertSee('Dead Stock Analysis');
    // Should NOT see admin-only analytics items in sidebar
    $response->assertDontSee('Sales Analytics');
    $response->assertDontSee('Pricing Module');
});
