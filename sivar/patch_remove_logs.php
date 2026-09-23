<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// Remove the problematic veto log
$oldVeto = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \Log::info("VETO: \$caracteristica caused viabilidad=false for {\$florA_eval->variedad} x {\$florB_eval->variedad} (Niveles A=" . \$this->obtenerNivelEvaluacion(\$florA_eval, \$caracteristica, \$testigoVal, 'viabilidad') . " B=" . \$this->obtenerNivelEvaluacion(\$florB_eval, \$caracteristica, \$testigoVal, 'viabilidad') . " Max=" . \$ponderado->nivel . ")");
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$newVeto = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$content = str_replace($oldVeto, $newVeto, $content);

// Remove the other viabilidad logs
$oldViab = <<<EOD
                // Otras condiciones 
                if ((\$florB->sxo == "Hembra" || \$florB->sxo == "HD" || \$florB->sxo == "HF")) {
                    \Log::info("Viabilidad false because florB {\$florB->vrdad} is Hembra");
                    \$viabilidad['viabilidad'] = false;
                }
                if ((\$florA->sxo == "Macho" || \$florA->sxo == "MD" || \$florA->sxo == "MF")) {
                    \Log::info("Viabilidad false because florA {\$florA->vrdad} is Macho");
                    \$viabilidad['viabilidad'] = false;
                }

                if (\$florA->vrdad == 'CC 90-1160' && \$florB->vrdad == 'Cayana') {
                    \Log::info("CC 90-1160 x Cayana: viabilidad is " . (\$viabilidad['viabilidad'] ? 'true' : 'false'));
                }

                \$viabilidad['vm'] = round(\$vm, 2);
EOD;

$newViab = <<<EOD
                // Otras condiciones 
                if ((\$florB->sxo == "Hembra" || \$florB->sxo == "HD" || \$florB->sxo == "HF")) {
                    \$viabilidad['viabilidad'] = false;
                }
                if ((\$florA->sxo == "Macho" || \$florA->sxo == "MD" || \$florA->sxo == "MF")) {
                    \$viabilidad['viabilidad'] = false;
                }

                \$viabilidad['vm'] = round(\$vm, 2);
EOD;

$content = str_replace($oldViab, $newViab, $content);
file_put_contents($file, $content);
