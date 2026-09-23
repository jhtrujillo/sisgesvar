<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldPond1 = <<<EOD
        \$ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin(DB::raw('(SELECT ponderados_valor_merito.* FROM ponderados_valor_merito JOIN remote_pg_sipro ON ponderados_valor_merito.id_proyecto = remote_pg_sipro.cd_cntble ) ponderados_valor_merito'), function (\$join) use (\$proyecto, \$ambiente) {
                \$join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', \$proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', \$ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();
EOD;

$newPond1 = <<<EOD
        \$ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin('ponderados_valor_merito', function (\$join) use (\$proyecto, \$ambiente) {
                \$join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', \$proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', \$ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();
EOD;

// There are multiple occurrences of this in CrossingService.php!
$content = str_replace($oldPond1, $newPond1, $content);

// Another slight variation with double semicolon at the end:
$oldPond2 = <<<EOD
        \$ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin(DB::raw('(SELECT ponderados_valor_merito.* FROM ponderados_valor_merito JOIN remote_pg_sipro ON ponderados_valor_merito.id_proyecto = remote_pg_sipro.cd_cntble ) ponderados_valor_merito'), function (\$join) use (\$proyecto, \$ambiente) {
                \$join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', \$proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', \$ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();;
EOD;

$content = str_replace($oldPond2, $newPond1, $content);
file_put_contents($file, $content);
