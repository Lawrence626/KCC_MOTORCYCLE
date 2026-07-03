<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$controller = app(App\Http\Controllers\WarehouseManagementController::class);
$response = $controller->index();
if ($response instanceof Illuminate\View\View) {
    $data = $response->getData();
    file_put_contents(__DIR__ . '/tmp_wh_data.json', json_encode($data['warehouses'], JSON_PRETTY_PRINT));
    echo 'dumped';
} else {
    echo get_class($response);
}
