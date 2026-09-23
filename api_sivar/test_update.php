<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/api/siembra-campo/viveros/46',
        'PUT',
        [
            'origen_vivero_id' => '',
            'origen_parcela' => 'TEST_MANUAL',
            'ingenio' => 'CN',
            'hacienda' => 'EESA',
            'lote_id' => 62,
            'consecutivo_vivero_ingenio' => 23,
            'fecha_siembra' => '2026-04-25',
            'origen_ingenio' => 'CN',
            'origen_hacienda' => 'EESA',
            'origen_lote_id' => 62,
            'origen_anio' => 2025,
            'proyectos' => [86],
            'caracteres_ids' => [2]
        ]
    )
);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
