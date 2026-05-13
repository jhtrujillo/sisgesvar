<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnsayoImport;

$filePath = 'storage/app/private/temp_imports/EvnKoijSVi929yNJe3f0ZCUWER7onaZ0HihLI5oS.xlsx';

try {
    $array = Excel::toArray(new EnsayoImport, $filePath);
    $rows = $array[0] ?? [];
    
    echo "Rows count: " . count($rows) . "\n";
    echo "Sample Keys of first data row: \n";
    print_r(array_keys($rows[0] ?? []));
    
    echo "\nFirst 3 Rows data:\n";
    print_r(array_slice($rows, 0, 3));
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
