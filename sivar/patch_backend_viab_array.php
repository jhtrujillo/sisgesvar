<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldArray = <<<EOD
                \$viabilidad = array(
                    'varA' => \$florA->vrdad,
                    'varB' => \$florB->vrdad,
                    'viabilidad' => true,
                    'vm' => '',
                    'vm2' => '',
                    'polen' => \$florA->polen ?? null,
                    'polen2' => \$florB->polen ?? null,
EOD;

$newArray = <<<EOD
                \$viabilidad = array(
                    'varA' => \$florA->vrdad,
                    'varB' => \$florB->vrdad,
                    'viabilidad' => true,
                    'vm' => '',
                    'vm2' => '',
                    'polen' => \$florA->polen ?? null,
                    'polen2' => \$florB->polen ?? null,
                    'cantidad_flores' => \$florA->cantidad_flores ?? 0,
                    'cantidad_flores2' => \$florB->cantidad_flores ?? 0,
EOD;

$content = str_replace($oldArray, $newArray, $content);
file_put_contents($file, $content);
