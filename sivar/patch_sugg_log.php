<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
    public function suggestionCrossingsPerProject(\$proy, \$proyecto, \$testigo, \$ambiente, \$caracter = null)
    {
        \$fechaf = Carbon::today()->format('Y-m-d');
EOD;

$newFunc = <<<EOD
    public function suggestionCrossingsPerProject(\$proy, \$proyecto, \$testigo, \$ambiente, \$caracter = null)
    {
        \Log::info("Called suggestionCrossingsPerProject: proy=\$proy, proyecto=\$proyecto, ambiente=\$ambiente, caracter=" . (\$caracter ?? 'NULL'));
        \$fechaf = Carbon::today()->format('Y-m-d');
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
