<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// Add parameter
$content = str_replace(
    'public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente)',
    'public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente, $caracter = null)',
    $content
);

// We need to inject the when() condition right after the last where() for each of these queries.
// Query 1: $flores
$content = str_replace(
    "->where('floracion.bolsa_comun', '=', 0)\n            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))",
    "->where('floracion.bolsa_comun', '=', 0)\n            ->when(\$caracter, function (\$q) use (\$caracter) { return \$q->where('floracion.id_crcter', \$caracter); })\n            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))",
    $content
);

// Query 2: $flores_BG
$content = str_replace(
    "->where('remote_pg_sipro.id_prycto', \$proy)\n            ->groupBy('floracion.vrdad', \"floracion.sxo\", \"floracion.id_pr\", \"caracteres.nmbre_crcter\", \"caracteres.id_crcter\", \"floracion.polen\", \"remote_pg_sipro.nm_prycto\")\n            ->select",
    "->where('remote_pg_sipro.id_prycto', \$proy)\n            ->when(\$caracter, function (\$q) use (\$caracter) { return \$q->where('floracion.id_crcter', \$caracter); })\n            ->groupBy('floracion.vrdad', \"floracion.sxo\", \"floracion.id_pr\", \"caracteres.nmbre_crcter\", \"caracteres.id_crcter\", \"floracion.polen\", \"remote_pg_sipro.nm_prycto\")\n            ->select",
    $content
);

// Query 3: $flores_PR (same signature as BG)
// Query 4: $flores_EIII (same signature as BG)

file_put_contents($file, $content);
