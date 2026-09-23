<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// Replace the genetic veto
$oldGen = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \Log::info("VETO GENETICO: {\$caracteristica} causó veto para {\$florA_eval->vrdad} x {\$florB_eval->vrdad}. Límite era: {\$ponderado->nivel}");
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$newGen = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \$viabilidad['viabilidad'] = false;
                                    \$viabilidad['causa_veto'] = strtoupper(\$caracteristica) . " excede límite (" . \$ponderado->nivel . ")";
                                }
                            }
EOD;

$content = str_replace($oldGen, $newGen, $content);

// Replace the biological veto
$oldBio = <<<EOD
                // Otras condiciones 
                if ((\$florB->sxo == "Hembra" || \$florB->sxo == "HD" || \$florB->sxo == "HF")) {
                    \$viabilidad['viabilidad'] = false;
                }
                if ((\$florA->sxo == "Macho" || \$florA->sxo == "MD" || \$florA->sxo == "MF")) {
                    \$viabilidad['viabilidad'] = false;
                }
EOD;

$newBio = <<<EOD
                // Otras condiciones 
                if ((\$florB->sxo == "Hembra" || \$florB->sxo == "HD" || \$florB->sxo == "HF")) {
                    \$viabilidad['viabilidad'] = false;
                    \$viabilidad['causa_veto'] = "Incompatibilidad de sexo (Ambos son Hembra)";
                }
                if ((\$florA->sxo == "Macho" || \$florA->sxo == "MD" || \$florA->sxo == "MF")) {
                    \$viabilidad['viabilidad'] = false;
                    \$viabilidad['causa_veto'] = "Incompatibilidad de sexo (Ambos son Macho)";
                }
EOD;

$content = str_replace($oldBio, $newBio, $content);

// Replace array initialization
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
                    'viabilidad' => true
                ];
EOD;

$newInit = <<<EOD
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
$content = str_replace($oldInit, $newInit, $content);

file_put_contents($file, $content);
