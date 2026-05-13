<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'storage/app/private/temp_imports/EvnKoijSVi929yNJe3f0ZCUWER7onaZ0HihLI5oS.xlsx';

$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();

$rows = $sheet->rangeToArray('A1:AM4');
foreach ($rows as $idx => $row) {
    echo "ROW " . ($idx+1) . ":\n";
    print_r($row);
}
