<?php
$file = '../api_sivar/app/Http/Controllers/CrossingController.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
    public function suggestionCrossingsPerProject(Request \$request, \$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter = null)
    {
        return \$this->crossingService->suggestionCrossingsPerProject(\$proyectos, \$proyecto, \$testigo, \$ambiente);
    }
EOD;

$newFunc = <<<EOD
    public function suggestionCrossingsPerProject(Request \$request, \$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter = null)
    {
        return \$this->crossingService->suggestionCrossingsPerProject(\$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter);
    }
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
