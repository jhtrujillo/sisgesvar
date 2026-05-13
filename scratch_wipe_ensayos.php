<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Ensayo;
use App\Models\Adjunto;

echo "=== INITIATING SAFE DATABASE PURGE ===\n";

try {
    DB::beginTransaction();

    // 1. Count before purge
    $adjuntosCount = Adjunto::count();
    $ensayosCount = Ensayo::count();
    
    echo "Found $ensayosCount Ensayos and $adjuntosCount Adjuntos to delete.\n";

    // 2. Perform deletion (this triggers DB cascade safely)
    // Using DB statement to bypass strict single-query limits and cascade purge
    DB::statement('TRUNCATE TABLE adjuntos RESTART IDENTITY CASCADE;');
    DB::statement('TRUNCATE TABLE ensayos RESTART IDENTITY CASCADE;');
    
    DB::commit();
    echo "SUCCESS: Both tables have been completely emptied and ID sequences reset!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "CRITICAL ERROR DURING PURGE: " . $e->getMessage() . "\n";
}
