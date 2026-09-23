<?php
// Patch Controller
$file = '../api_sivar/app/Http/Controllers/CrossingController.php';
$content = file_get_contents($file);
$content = str_replace(
    'public function suggestionCrossingsPerProject(Request $request, $proyectos, $proyecto, $testigo, $ambiente)',
    'public function suggestionCrossingsPerProject(Request $request, $proyectos, $proyecto, $testigo, $ambiente, $caracter = null)',
    $content
);
$content = str_replace(
    '$response = $this->crossingService->suggestionCrossingsPerProject($proyectos, $proyecto, $testigo, $ambiente);',
    '$response = $this->crossingService->suggestionCrossingsPerProject($proyectos, $proyecto, $testigo, $ambiente, $caracter);',
    $content
);
file_put_contents($file, $content);

// Patch Route
$file = '../api_sivar/routes/api.php';
$content = file_get_contents($file);
$content = str_replace(
    "Route::get('suggestionCrossingsPerProject/{proyectos}/{proyecto}/{testigo}/{ambiente}', [\App\Http\Controllers\CrossingController::class, 'suggestionCrossingsPerProject']);",
    "Route::get('suggestionCrossingsPerProject/{proyectos}/{proyecto}/{testigo}/{ambiente}/{caracter?}', [\App\Http\Controllers\CrossingController::class, 'suggestionCrossingsPerProject']);",
    $content
);
file_put_contents($file, $content);
