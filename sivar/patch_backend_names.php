<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldVeto = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \$viabilidad['viabilidad'] = false;
                                    \$viabilidad['causa_veto'] = strtoupper(\$caracteristica) . " excede límite (" . \$ponderado->nivel . ")";
                                }
                            }
EOD;

$newVeto = <<<EOD
                            if (\$hasA && \$hasB) {
                                if (!\$this->calcularViabilidadCaracteristica(\$caracteristica, \$florA_eval, \$florB_eval, \$ponderado, \$testigoVal)) {
                                    \$viabilidad['viabilidad'] = false;
                                    
                                    \$nombresLegibles = [
                                        'scrsa' => 'Sacarosa',
                                        'tchm' => 'TCHM (Producción)',
                                        'msco_r' => 'Mosaico',
                                        'rya_cfe_r' => 'Roya',
                                        'roya' => 'Roya',
                                        'roya_naranja' => 'Roya Naranja',
                                        'carbon' => 'Carbón',
                                        'volcamiento' => 'Volcamiento',
                                        'altura_planta' => 'Altura de Planta',
                                        'poblacion' => 'Población',
                                        'dmtro_tllo' => 'Diámetro de Tallo'
                                    ];
                                    \$nombreLegible = \$nombresLegibles[\$caracteristica] ?? strtoupper(\$caracteristica);
                                    
                                    \$viabilidad['causa_veto'] = \$nombreLegible . " excede límite (" . \$ponderado->nivel . ")";
                                }
                            }
EOD;

$content = str_replace($oldVeto, $newVeto, $content);
file_put_contents($file, $content);
