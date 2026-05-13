<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Ensayo;
use Illuminate\Http\Request;

$user = User::first();
$ensayo = Ensayo::orderBy('id', 'desc')->first();

echo "Testing Real Route PATCH /ensayos/" . $ensayo->id . "\n";

$request = Request::create('/ensayos/' . $ensayo->id, 'PATCH', [
    'field' => 'proyecto',
    'value' => 'TEST_ROUTING_HIT'
]);

// Manually act as user
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Execute through full app pipeline
$response = $kernel->handle($request);

echo "Response Status Code: " . $response->getStatusCode() . "\n";
echo "Response Content Prefix (100 chars): " . substr($response->getContent(), 0, 100) . "\n";

$kernel->terminate($request, $response);
