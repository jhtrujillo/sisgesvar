<?php
$file = '../api_sivar/app/Http/Controllers/CrossingController.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
    public function suggestionCrossingsPerProject(Request \$request, \$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter = null)
    {
        \$response = \$this->crossingService->suggestionCrossingsPerProject(\$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter);
        return response()->json(\$response);
    }
EOD;

$newFunc = <<<EOD
    public function suggestionCrossingsPerProject(Request \$request, \$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter = null)
    {
        \Log::info("Called suggestionCrossingsPerProject: proyectos=\$proyectos, proyecto=\$proyecto, ambiente=\$ambiente, caracter=" . (\$caracter ?: 'NULL'));
        \$response = \$this->crossingService->suggestionCrossingsPerProject(\$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter);
        return response()->json(\$response);
    }
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
