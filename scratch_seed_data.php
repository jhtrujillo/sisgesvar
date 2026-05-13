<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ensayo;
use App\Models\User;

$user = User::where('email', 'like', '%@%')->first();
if (!$user) {
    $user = User::create([
        'name' => 'Test Driver',
        'email' => 'driver' . rand(10,99) . '@example.com',
        'password' => bcrypt('password'),
    ]);
}

Ensayo::create([
    'user_id' => $user->id,
    'nombre_ensayo' => 'MUESTRA PRUEBA SISTEMA',
    'proyecto' => 'PROYECTO INICIAL',
    'amb_seleccion' => 'HUMEDO',
    'corte' => '1'
]);

echo "Created a row successfully.\n";
