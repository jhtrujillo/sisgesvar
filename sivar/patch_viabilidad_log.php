<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
                // Otras condiciones 
                if ((\$florB->sxo == "Hembra" || \$florB->sxo == "HD" || \$florB->sxo == "HF")) {
                    \$viabilidad['viabilidad'] = false;
                }
                if ((\$florA->sxo == "Macho" || \$florA->sxo == "MD" || \$florA->sxo == "MF")) {
                    \$viabilidad['viabilidad'] = false;
                }

                \$viabilidad['vm'] = round(\$vm, 2);
EOD;

$newFunc = <<<EOD
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

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
