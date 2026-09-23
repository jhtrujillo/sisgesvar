<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$controller = app(App\Http\Controllers\ViveroController::class);

$request = Illuminate\Http\Request::create('/api/siembra-campo/viveros/25', 'PUT', [
    'origen_parcela' => '42',
    'origen_vivero_id' => null,
    'origen_ingenio' => 'MY',
    'origen_hacienda' => '620',
    'origen_lote_id' => 57,
    'origen_anio' => 2024,
    'ingenio' => 'MY',
    'hacienda' => '620',
    'lote_id' => 57,
    'consecutivo_vivero_ingenio' => 8,
    'fecha_siembra' => '2026-01-06T00:00:00.000000Z',
    'proyecto_id' => 230,
    'proyectos' => [230],
]);
$request->setUserResolver(function() {
    return App\Models\User::first();
});

$response = $controller->update(App\Http\Requests\UpdateViveroRequest::createFrom($request), 25);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";

$v = App\Models\Vivero::find(25);
echo "New origen_parcela: " . $v->origen_parcela . "\n";

