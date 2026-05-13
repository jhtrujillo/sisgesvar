<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Ensayo;

echo "=== DATABASE DIAGNOSTIC ===\n";
$ensayosCount = Ensayo::count();
echo "Total Ensayos in database: $ensayosCount\n";

if ($ensayosCount > 0) {
    $first = Ensayo::first();
    echo "Sample Ensayo ID: " . $first->id . "\n";
    echo "Sample Ensayo User ID: " . ($first->user_id ?? 'NULL') . "\n";
    echo "Sample Ensayo Amb Seleccion: " . ($first->amb_seleccion ?? 'NULL') . "\n";
}

echo "\n=== USERS LIST ===\n";
$users = User::all();
foreach($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | Ambientes: " . json_encode($u->ambiente) . "\n";
}
