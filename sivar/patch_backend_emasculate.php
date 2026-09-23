<?php
$file = '../api_sivar/app/Http/Controllers/CrossingController.php';
$content = file_get_contents($file);

// Replace the observation block inside guardarCruzamiento
$oldCode = <<<EOD
                    \$madreVal = \$cData['madre'] ?? '';
                    \$padresVal = \$cData['padres'] ?? '';
                    \$obsVal = \$cData['observaciones'] ?? 'Programacion de Cruzamientos';
EOD;

$newCode = <<<EOD
                    \$madreVal = \$cData['madre'] ?? '';
                    \$padresVal = \$cData['padres'] ?? '';
                    \$obsVal = \$cData['observaciones'] ?? 'Programacion de Cruzamientos';
                    if (!empty(\$cData['emasculado']) && \$cData['emasculado'] == true) {
                        \$obsVal = "[EMASCULAR MADRE] " . \$obsVal;
                    }
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
