<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$newFunc = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \Log::info("VETO: \$caracteristica caused viabilidad=false for {\$florA_eval->variedad} x {\$florB_eval->variedad} (Niveles A=" . \$this->obtenerNivelEvaluacion(\$florA_eval, \$caracteristica, \$testigoVal, 'viabilidad') . " B=" . \$this->obtenerNivelEvaluacion(\$florB_eval, \$caracteristica, \$testigoVal, 'viabilidad') . " Max=" . \$ponderado->nivel . ")");
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
