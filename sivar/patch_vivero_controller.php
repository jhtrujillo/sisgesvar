<?php
$file = '../api_sivar/app/Http/Controllers/ViveroController.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
    public function getCaracteresPorProyecto(\$id)
    {
        \$caracteres = DB::table('proyecto_caracteres')->where('proyecto_id', \$id)->get();
        return response()->json(\$caracteres);
    }
EOD;

$newFunc = <<<EOD
    public function getCaracteresPorProyecto(\$id)
    {
        \$fechaf = \Carbon\Carbon::today()->format('Y-m-d');
        \$fechai = \Carbon\Carbon::yesterday()->format('Y-m-d');

        \$caracteres = DB::table('proyecto_caracteres')
            ->where('proyecto_id', \$id)
            ->orWhereIn('id', function(\$query) use (\$id, \$fechai, \$fechaf) {
                \$query->select('id_crcter')
                      ->from('floracion')
                      ->where('id_pr', \$id)
                      ->where('estado', 0)
                      ->where('bolsa_comun', 0)
                      ->whereBetween('fcha', [\$fechai, \$fechaf]);
            })
            ->distinct()
            ->get();
            
        return response()->json(\$caracteres);
    }
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
