<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = $app->make(App\Http\Controllers\CrossingController::class);
$request = new \Illuminate\Http\Request();
$res = $controller->suggestionCrossingsPerProject($request, '230', '010126', 'CC 01-1940', 'Semiseco');
$data = json_decode($res->content(), true);
print_r($data['flores'][0]);
