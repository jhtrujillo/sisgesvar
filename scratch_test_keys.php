<?php
require 'vendor/autoload.php';
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnsayoImport;
use Illuminate\Support\Facades\Facade;

// Setup Laravel context
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filePath = 'ejemplos/Base Datos Ensayos EZP (1).xlsx';
$import = new EnsayoImport();

// We can't directly access the internal structure easily, 
// let's create a test class that just outputs the row.

class TestImport implements \Maatwebsite\Excel\Concerns\ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow {
    public function headingRow(): int { return 2; }
    public function model(array $row) {
        print_r(array_keys($row));
        exit; // Just print first row keys and exit
    }
}

\Maatwebsite\Excel\Facades\Excel::import(new TestImport, $filePath);
