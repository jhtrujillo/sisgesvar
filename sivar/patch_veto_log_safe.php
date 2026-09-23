<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldVeto = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$newVeto = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \Log::info("VETO GENETICO: {\$caracteristica} causó veto para {\$florA_eval->vrdad} x {\$florB_eval->vrdad}. Límite era: {\$ponderado->nivel}");
                                    \$viabilidad['viabilidad'] = false;
                                }
                            }
EOD;

$content = str_replace($oldVeto, $newVeto, $content);
file_put_contents($file, $content);
