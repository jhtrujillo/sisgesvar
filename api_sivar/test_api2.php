<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = $app->make(App\Http\Controllers\CrossingController::class);
$request = new \Illuminate\Http\Request();
try {
    $res = $controller->suggestionCrossingsPerProject($request, '230', '010126', 'CC 01-1940', 'Semiseco');
    echo "Success! Length: " . strlen($res->content()) . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\nIn " . $e->getFile() . " line " . $e->getLine() . "\n";
}
