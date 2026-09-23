<?php
$file = '../api_sivar/app/Http/Controllers/FloracionImportController.php';
$content = file_get_contents($file);

$oldInserts = <<<EOD
            \$inserts[] = [
                'ingnio' => \$vivero->ingenio,
                'hcnda' => \$vivero->hacienda,
                'fcha' => \$fechaParsed,
                'lte' => \$vivero->suerte,
                'prcla' => \$excelParcela,
                'vrdad' => \$excelVariedad,
                'flrcion' => \$floracionTipo ?? 'Natural',
                'sxo' => \$mappedSexo,
                'polen' => is_numeric(\$polen) ? (int)\$polen : null,
                'grpo' => \$plotIdOrigen, // Origen/Plot Origen
                'vivero' => \$vivero->identificador_unico,
                'id_smbra_cmpo' => \$vivero->id, // Store reference
                'estado' => '0',
                'id_pr' => \$vivero->proyecto_id,
                'id_crcter' => \$caracterId, // Inherited automatically
                'usrio_edto' => auth()->id() ?? 1,
                'fcha_edto' => \$now
            ];
EOD;

$newInserts = <<<EOD
            \$floresCantStr = \$colIndex['flores'] !== false ? trim(\$row[\$colIndex['flores']]) : '1';
            \$floresCant = is_numeric(\$floresCantStr) && (int)\$floresCantStr > 0 ? (int)\$floresCantStr : 1;
            
            for (\$k = 0; \$k < \$floresCant; \$k++) {
                \$inserts[] = [
                    'ingnio' => \$vivero->ingenio,
                    'hcnda' => \$vivero->hacienda,
                    'fcha' => \$fechaParsed,
                    'lte' => \$vivero->suerte,
                    'prcla' => \$excelParcela,
                    'vrdad' => \$excelVariedad,
                    'flrcion' => \$floracionTipo ?? 'Natural',
                    'sxo' => \$mappedSexo,
                    'polen' => is_numeric(\$polen) ? (int)\$polen : null,
                    'grpo' => \$plotIdOrigen, // Origen/Plot Origen
                    'vivero' => \$vivero->identificador_unico,
                    'id_smbra_cmpo' => \$vivero->id, // Store reference
                    'estado' => '0',
                    'id_pr' => \$vivero->proyecto_id,
                    'id_crcter' => \$caracterId, // Inherited automatically
                    'usrio_edto' => auth()->id() ?? 1,
                    'fcha_edto' => \$now
                ];
            }
EOD;

$content = str_replace($oldInserts, $newInserts, $content);
file_put_contents($file, $content);
