<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ensayo;
$all = Ensayo::all();
foreach($all as $e) {
    echo "ID: " . $e->id . " | Ensayo: " . $e->nombre_ensayo . " | Proyecto: " . $e->proyecto . "\n";
}
