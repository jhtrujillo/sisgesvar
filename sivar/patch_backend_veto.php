<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldCode = <<<EOD
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

$newCode = <<<EOD
                // Otras condiciones 
                if ((\$florB->sxo == "Hembra" || \$florB->sxo == "HD" || \$florB->sxo == "HF")) {
                    \$prev = isset(\$viabilidad['causa_veto']) && \$viabilidad['viabilidad'] === false ? \$viabilidad['causa_veto'] . " | " : "";
                    \$viabilidad['viabilidad'] = false;
                    \$viabilidad['causa_veto'] = \$prev . "Incompatibilidad de sexo (Ambos son Hembra)";
                }
                if ((\$florA->sxo == "Macho" || \$florA->sxo == "MD" || \$florA->sxo == "MF")) {
                    \$prev = isset(\$viabilidad['causa_veto']) && \$viabilidad['viabilidad'] === false ? \$viabilidad['causa_veto'] . " | " : "";
                    \$viabilidad['viabilidad'] = false;
                    \$viabilidad['causa_veto'] = \$prev . "Incompatibilidad de sexo (Ambos son Macho)";
                }
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
