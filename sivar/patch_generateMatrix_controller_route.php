<?php
// Controller
$file = '../api_sivar/app/Http/Controllers/CrossingController.php';
$content = file_get_contents($file);
$content = str_replace(
    'public function generateMatrix(Request $request, $proyectos, $proyecto, $testigo, $ambiente = \'Semiseco\')',
    'public function generateMatrix(Request $request, $proyectos, $proyecto, $testigo, $ambiente = \'Semiseco\', $caracter = null)',
    $content
);
$content = str_replace(
    'return $this->crossingService->generateMatrix($proyectos, $proyecto, $testigo, $ambiente);',
    'return $this->crossingService->generateMatrix($proyectos, $proyecto, $testigo, $ambiente, $caracter);',
    $content
);
file_put_contents($file, $content);

// Route
$file = '../api_sivar/routes/api.php';
$content = file_get_contents($file);
$content = str_replace(
    "Route::get('generateMatrix/{proyectos}/{proyecto}/{testigo}/{ambiente}', [\App\Http\Controllers\CrossingController::class, 'generateMatrix']);",
    "Route::get('generateMatrix/{proyectos}/{proyecto}/{testigo}/{ambiente}/{caracter?}', [\App\Http\Controllers\CrossingController::class, 'generateMatrix']);",
    $content
);
file_put_contents($file, $content);
