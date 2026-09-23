<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// Replace count(*) with COUNT(DISTINCT "floracion"."id_flrcion")
$oldStr = 'count(*) as cantidad_flores';
$newStr = 'COUNT(DISTINCT "floracion"."id_flrcion") as cantidad_flores';
$content = str_replace($oldStr, $newStr, $content);

file_put_contents($file, $content);
