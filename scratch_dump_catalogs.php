<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Catalogo;

echo "=== MASTER CATALOGS DUMP ===\n";
$cats = Catalogo::orderBy('categoria')->orderBy('valor')->get();
foreach ($cats as $c) {
    echo "[{$c->categoria}] {$c->valor}\n";
}
