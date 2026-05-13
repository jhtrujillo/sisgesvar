<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/ejemplos/Base Datos Ensayos EZP (1).xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

echo "Row 1 (Explanations): \n";
print_r(array_slice($rows[0], 0, 10));

echo "\nRow 2 (Technical Headers): \n";
print_r(array_slice($rows[1], 0, 10));

echo "\nRow 3 (First Data Row): \n";
print_r(array_slice($rows[2], 0, 10));

echo "\nRow 22: \n";
print_r(array_slice($rows[21], 0, 10));

echo "\nRow 31: \n";
print_r(array_slice($rows[30], 0, 10));
