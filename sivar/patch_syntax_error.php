<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

$content = str_replace('COUNT(DISTINCT "floracion"."id_flrcion")', 'COUNT(DISTINCT \"floracion\".\"id_flrcion\")', $content);

file_put_contents($file, $content);
