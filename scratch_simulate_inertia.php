<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Ensayo;
use Illuminate\Http\Request;

// Initialize application
$app->boot();

$user = User::orderBy('id', 'asc')->first();
$ensayo = Ensayo::orderBy('id', 'desc')->first();

echo "Simulating Inertia Request: PATCH /ensayos/" . $ensayo->id . "\n";

$request = Request::create('/ensayos/' . $ensayo->id, 'PATCH', [
    'field' => 'proyecto',
    'value' => 'INERTIA_TEST_RUN_' . rand(10,99)
]);

// Manually add Inertia headers so Laravel acts exactly like the frontend!
$request->headers->set('X-Inertia', 'true');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Act as user
$request->setUserResolver(function () use ($user) {
    return $user;
});
auth()->login($user);

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    
    if ($response->isRedirection()) {
        echo "REDIRECTING TO: " . $response->headers->get('Location') . "\n";
    } else {
        echo "RESPONSE CONTENT: \n" . substr($response->getContent(), 0, 300) . "\n";
    }
    
    $kernel->terminate($request, $response);
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
