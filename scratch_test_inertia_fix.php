<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
// Properly bootstrap console/http kernel to set up bindings
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ensayo;
use Illuminate\Http\Request;

$user = User::orderBy('id', 'asc')->first();
$ensayo = Ensayo::orderBy('id', 'desc')->first();

echo "Testing simulated Inertia call through App Http Kernel flow...\n";

$httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$request = Request::create('/ensayos/' . $ensayo->id, 'PATCH', [
    'field' => 'proyecto',
    'value' => 'SIM_HIT_' . rand(10,99)
]);
$request->headers->set('X-Inertia', 'true');

// Simulate acting as user
auth()->login($user);

$response = $httpKernel->handle($request);

echo "Response Code: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Target: " . $response->headers->get('Location') . "\n";
} else {
    echo "Content starts with: " . substr($response->getContent(), 0, 200) . "\n";
}
