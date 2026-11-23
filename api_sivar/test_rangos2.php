<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$evaluaciones = \App\Models\Evaluacion::with('rangos')->whereHas('tipoEvaluacion', function($q) { $q->where('keyname', 'viabilidad'); })->get();
foreach ($evaluaciones as $eval) {
    echo "Traits: " . implode(', ', $eval->arrayCharacters ?? []) . "\n";
    foreach ($eval->rangos as $rango) {
        echo "  [{$rango->valor_minimo} - {$rango->valor_maximo}) -> {$rango->calificacion}\n";
    }
}
