<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'ejemplos/Base Datos Ensayos EZP (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

// Row 0 is usually the top headers, or Row 1? 
// In EnsayoImport it says "Row 1 is meta, Row 2 are technical headers".
// Let's print the first 3 rows.
print_r($rows[0] ?? []);
print_r($rows[1] ?? []);
print_r($rows[2] ?? []);
