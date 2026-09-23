<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// Fix duplicated counts inside double quotes:
$content = preg_replace('/COUNT\(DISTINCT \\\\"floracion\\\\"\\.\\\\"id_flrcion\\\\"\) as cantidad_flores, COUNT\(DISTINCT \\\\"floracion\\\\"\\.\\\\"id_flrcion\\\\"\) as cantidad_flores, /', 'COUNT(DISTINCT \\"floracion\\".\\"id_flrcion\\") as cantidad_flores, ', $content);

// Fix escaped quotes inside single quotes:
$content = preg_replace('/DB::raw\(\'"floracion"\."vrdad", COUNT\(DISTINCT \\\\"floracion\\\\"\\.\\\\"id_flrcion\\\\"\)/', 'DB::raw(\'"floracion"."vrdad", COUNT(DISTINCT "floracion"."id_flrcion")', $content);

file_put_contents($file, $content);
