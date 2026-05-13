<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== AMBIENTES BREAKDOWN IN ENSAYOS ===\n";
$counts = DB::table('ensayos')
    ->select('amb_seleccion', DB::raw('count(*) as total'))
    ->groupBy('amb_seleccion')
    ->get();

foreach($counts as $c) {
    echo "Ambiente: " . ($c->amb_seleccion ?? 'NULL') . " | Total Ensayos: " . $c->total . "\n";
}
