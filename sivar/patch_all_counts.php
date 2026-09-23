<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$oldStr1 = '->select(DB::raw("\"floracion\".\"vrdad\", ';
$newStr1 = '->select(DB::raw("\"floracion\".\"vrdad\", count(*) as cantidad_flores, ';
$content = str_replace($oldStr1, $newStr1, $content);

$oldStr2 = '->select(DB::raw("\'\' as \"estado\", \"floracion\".\"vrdad\", ';
$newStr2 = '->select(DB::raw("\'\' as \"estado\", \"floracion\".\"vrdad\", count(*) as cantidad_flores, ';
$content = str_replace($oldStr2, $newStr2, $content);

$oldStr3 = '->select(DB::raw(\'"floracion"."vrdad", ';
$newStr3 = '->select(DB::raw(\'"floracion"."vrdad", count(*) as cantidad_flores, ';
$content = str_replace($oldStr3, $newStr3, $content);

file_put_contents($file, $content);
