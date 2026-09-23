<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldStr = '->select(DB::raw("\"floracion\".\"vrdad\", ';
$newStr = '->select(DB::raw("\"floracion\".\"vrdad\", count(*) as cantidad_flores, ';

$content = str_replace($oldStr, $newStr, $content);

file_put_contents($file, $content);
