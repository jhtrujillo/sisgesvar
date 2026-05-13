<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'ejemplos/Plantilla Base Datos Programacion Ensayos Area Mejoramiento Genetico - Humedo (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

print_r($rows[0] ?? []);
print_r($rows[1] ?? []);
