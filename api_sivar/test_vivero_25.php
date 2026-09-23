<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$controller = app(App\Http\Controllers\ViveroController::class);
$response = $controller->show(25);
echo $response->getContent();
