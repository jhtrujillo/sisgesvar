<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ensayo;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\EnsayoController;

// Get or Create a test record
$user = User::first();
if (!$user) {
    echo "No user found for testing.\n";
    exit;
}

$ensayo = Ensayo::first();
if (!$ensayo) {
    $ensayo = Ensayo::create([
        'nombre_ensayo' => 'TESTING_INLINE',
        'user_id' => $user->id,
        'proyecto' => 'PROYECTO PREVIO'
    ]);
}

echo "Updating Ensayo ID: " . $ensayo->id . "\n";
echo "Original Proyecto: " . $ensayo->proyecto . "\n";

// Authenticate
auth()->login($user);

// Build request
$request = Request::create('/fake', 'PATCH', [
    'field' => 'proyecto',
    'value' => 'NUEVO PROYECTO DINAMICO ' . rand(100, 999)
]);

$controller = new EnsayoController();
try {
    $response = $controller->update($request, $ensayo);
    echo "Response Code: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
    // Verify reload
    $ensayo->refresh();
    echo "Post-Update Proyecto: " . $ensayo->proyecto . "\n";
    
    // Check Catalog
    $catalogEntry = \App\Models\Catalogo::where('valor', $ensayo->proyecto)->first();
    echo "Catalog Entry Found: " . ($catalogEntry ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
}
