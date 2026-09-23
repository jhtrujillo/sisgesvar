<?php
$file = '../api_sivar/app/Http/Controllers/CrossingController.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
    public function generateMatrix(Request \$request, \$proyectos, \$proyecto, \$testigo, \$ambiente = 'Semiseco', \$caracter = null)
    {
        return \$this->crossingService->generateMatrix(\$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter);
    }
EOD;

$newFunc = <<<EOD
    public function generateMatrix(Request \$request, \$proyectos, \$proyecto, \$testigo, \$ambiente = 'Semiseco', \$caracter = null)
    {
        \Log::info("Called generateMatrix: proyectos=\$proyectos, ambiente=\$ambiente, caracter=" . (\$caracter ?: 'NULL'));
        return \$this->crossingService->generateMatrix(\$proyectos, \$proyecto, \$testigo, \$ambiente, \$caracter);
    }
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
