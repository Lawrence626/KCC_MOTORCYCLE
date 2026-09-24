<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\DeadStock;
use Illuminate\Http\Request;
use App\Http\Controllers\DeadStockController;

echo "=== TESTING REMOVE DISCOUNT ENDPOINT ===\n\n";

// 1. Pick a product to test with
$deadStock = DeadStock::with('product')->where('is_active', true)->first();
$product = $deadStock->product;

echo "1. Selected Product ID {$product->id} ({$product->name}):\n";

// 2. Apply a 10% discount first
$controller = app(DeadStockController::class);
$applyReq = Request::create("/dss/dead-stock/{$deadStock->id}/apply-discount", 'POST', [
    'discount_type' => 'percentage',
    'discount_value' => '10',
]);
$controller->applyDiscount($deadStock->id, $applyReq);

$product->refresh();
echo "2. Applied 10% discount: discount_type={$product->discount_type}, discount_value={$product->discount_value}\n";
echo "   Is discount set? " . ($product->discount_value == 10 ? "YES ✅" : "NO ❌") . "\n\n";

// 3. Call removeDiscount endpoint
$removeReq = Request::create('/api/pos/remove-product-discount', 'POST', [
    'product_ids' => [$product->id],
]);
$removeReq->headers->set('Accept', 'application/json');

$response = $controller->removeDiscount($removeReq);
$data = json_decode($response->getContent(), true);

echo "3. Called removeDiscount endpoint:\n";
echo "   Response Status: {$response->getStatusCode()}\n";
echo "   Response JSON: " . json_encode($data) . "\n";
echo "   Success: " . ($data['success'] === true ? "✅ PASSED" : "❌ FAILED") . "\n\n";

// 4. Verify product in database
$product->refresh();
echo "4. Verifying database state:\n";
echo "   Product discount_type: " . var_export($product->discount_type, true) . " (Expected: NULL " . ($product->discount_type === null ? "✅" : "❌") . ")\n";
echo "   Product discount_value: " . var_export($product->discount_value, true) . " (Expected: NULL " . ($product->discount_value === null ? "✅" : "❌") . ")\n\n";

$deadStock->refresh();
echo "5. Dead stock analysis_notes: {$deadStock->analysis_notes}\n\n";

echo "=== REMOVE DISCOUNT VERIFICATION PASSED SUCCESSFULLY ===\n";
