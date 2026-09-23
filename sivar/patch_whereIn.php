<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldWhen = "->when(\$caracter, function (\$q) use (\$caracter) { return \$q->where('floracion.id_crcter', \$caracter); })";
$newWhen = <<<EOD
            ->when(\$caracter, function (\$q) use (\$caracter) {
                if (strpos(\$caracter, ',') !== false) {
                    \$ids = explode(',', \$caracter);
                    return \$q->whereIn('floracion.id_crcter', \$ids);
                }
                return \$q->where('floracion.id_crcter', \$caracter);
            })
EOD;

$content = str_replace($oldWhen, $newWhen, $content);
file_put_contents($file, $content);
