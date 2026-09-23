<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$lines = file($file);

$inMethod = false;
$newLines = [];
foreach ($lines as $i => $line) {
    if (strpos($line, 'public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente)') !== false) {
        $line = str_replace(
            'public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente)',
            'public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente, $caracter = null)',
            $line
        );
        $inMethod = true;
    }
    
    if (strpos($line, 'public function getGenerales(') !== false) {
        $inMethod = false;
    }

    if ($inMethod) {
        if (strpos($line, "->where('floracion.bolsa_comun', '=', 0)") !== false) {
            // Check if next line is select() to only match query 1
            if (isset($lines[$i+1]) && strpos($lines[$i+1], '->select(DB::raw(\'count') !== false) {
                $newLines[] = $line;
                $newLines[] = "            ->when(\$caracter, function (\$q) use (\$caracter) { return \$q->where('floracion.id_crcter', \$caracter); })\n";
                continue;
            }
        }
        
        if (strpos($line, "->where('remote_pg_sipro.id_prycto', \$proy)") !== false) {
            // This is for flores_BG, flores_PR, flores_EIII
            if (isset($lines[$i+1]) && strpos($lines[$i+1], '->groupBy(') !== false) {
                $newLines[] = $line;
                $newLines[] = "            ->when(\$caracter, function (\$q) use (\$caracter) { return \$q->where('floracion.id_crcter', \$caracter); })\n";
                continue;
            }
        }
    }
    
    $newLines[] = $line;
}

file_put_contents($file, implode("", $newLines));
