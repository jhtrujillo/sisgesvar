<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// 1. Signature
$content = str_replace(
    'public function generateMatrix($proy, $proyecto, $testigo, $ambiente = \'Semiseco\')',
    'public function generateMatrix($proy, $proyecto, $testigo, $ambiente = \'Semiseco\', $caracter = null)',
    $content
);

// 2. Closure uses $caracter
$content = str_replace(
    '$queryFloresBG = function ($useProjectFilter = true) use ($proyectos, $fechai, $fechaf, $hasSpecificProject) {',
    '$queryFloresBG = function ($useProjectFilter = true) use ($proyectos, $fechai, $fechaf, $hasSpecificProject, $caracter) {',
    $content
);
$content = str_replace(
    '$queryFloresPR = function ($useProjectFilter = true) use ($proyectos, $fechai, $fechaf, $hasSpecificProject) {',
    '$queryFloresPR = function ($useProjectFilter = true) use ($proyectos, $fechai, $fechaf, $hasSpecificProject, $caracter) {',
    $content
);
$content = str_replace(
    '$queryFloresEIII = function ($useProjectFilter = true) use ($proyectos, $fechai, $fechaf, $hasSpecificProject) {',
    '$queryFloresEIII = function ($useProjectFilter = true) use ($proyectos, $fechai, $fechaf, $hasSpecificProject, $caracter) {',
    $content
);

// 3. Inject filter into closures
$filterSnippet = <<<EOD
            if (\$caracter) {
                if (strpos(\$caracter, ',') !== false) {
                    \$ids = explode(',', \$caracter);
                    \$q->whereIn('floracion.id_crcter', \$ids);
                } else {
                    \$q->where('floracion.id_crcter', \$caracter);
                }
            }
EOD;

$oldBG = "return \$q->whereBetween('floracion.fcha', array(\$fechai, \$fechaf))";
$newBG = $filterSnippet . "\n            return \$q->whereBetween('floracion.fcha', array(\$fechai, \$fechaf))";

// Apply to generateMatrix queries
$content = str_replace($oldBG, $newBG, $content);

file_put_contents($file, $content);
