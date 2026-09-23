<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldWhen = <<<EOD
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->where('remote_pg_sipro.id_prycto', \$proy)
            //->where('caracterizacion_banco_germoplasma.sitio_seleccion', '=', \$ambiente_sitio)
EOD;

$newWhen = <<<EOD
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->where('remote_pg_sipro.id_prycto', \$proy)
            ->when(\$caracter, function (\$q) use (\$caracter) {
                if (strpos(\$caracter, ',') !== false) {
                    \$ids = explode(',', \$caracter);
                    return \$q->whereIn('floracion.id_crcter', \$ids);
                }
                return \$q->where('floracion.id_crcter', \$caracter);
            })
            //->where('caracterizacion_banco_germoplasma.sitio_seleccion', '=', \$ambiente_sitio)
EOD;

$content = str_replace($oldWhen, $newWhen, $content);
file_put_contents($file, $content);
