<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnsayoImport;

// Find the most recent temp file from private storage
$dir = __DIR__ . '/storage/app/private/temp_imports/';
$files = glob($dir . '*.xlsx');
if (empty($files)) {
    echo "No files found in temp_imports!\n";
    exit;
}

usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$targetFile = $files[0];
echo "Analyzing file: " . basename($targetFile) . " (Modified: " . date('Y-m-d H:i:s', filemtime($targetFile)) . ")\n";

// Set dummy user to avoid auth()->id() null constraint issues if any
$user = \App\Models\User::first();
auth()->login($user);

try {
    echo "Executing simulated database transaction import...\n";
    \Illuminate\Support\Facades\DB::beginTransaction();
    
    // Simulate some standard mappings for testing
    $mappings = [
        'PROYECTO' => [],
        'INGENIO' => [],
        'AMBIENTE' => []
    ];
    
    Excel::import(new EnsayoImport('PIEDEMONTE', $mappings), $targetFile);
    
    $insertedCount = \App\Models\Ensayo::where('created_at', '>', \Carbon\Carbon::now()->subSeconds(5))->count();
    echo "SUCCESS! Simulated import created {$insertedCount} new records!\n";
    
    \Illuminate\Support\Facades\DB::rollBack();
    echo "Rollback completed safely.\n";
} catch (\Exception $e) {
    echo "EXCEPTION CAUGHT DURING IMPORT!\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
