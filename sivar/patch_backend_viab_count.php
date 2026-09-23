<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldInit = <<<EOD
                \$viabilidad = [
                    'varA' => \$florA->vrdad,
                    'polen' => \$florA->polen,
                    'id_caracter' => \$florA->id_caracter,
                    'proyecto' => \$florA->id_pr,
                    'varB' => \$florB->vrdad,
                    'polen2' => \$florB->polen,
                    'id_caracter2' => \$florB->id_caracter,
                    'proyecto2' => \$florB->id_pr,
                    'viabilidad' => true,
                    'causa_veto' => ''
                ];
EOD;

$newInit = <<<EOD
                \$viabilidad = [
                    'varA' => \$florA->vrdad,
                    'polen' => \$florA->polen,
                    'cantidad_flores' => \$florA->cantidad_flores ?? 0,
                    'id_caracter' => \$florA->id_caracter,
                    'proyecto' => \$florA->id_pr,
                    'varB' => \$florB->vrdad,
                    'polen2' => \$florB->polen,
                    'cantidad_flores2' => \$florB->cantidad_flores ?? 0,
                    'id_caracter2' => \$florB->id_caracter,
                    'proyecto2' => \$florB->id_pr,
                    'viabilidad' => true,
                    'causa_veto' => ''
                ];
EOD;

$content = str_replace($oldInit, $newInit, $content);
file_put_contents($file, $content);
