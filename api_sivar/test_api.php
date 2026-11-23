<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/suggestionCrossingsPerProject/230/010126/CC%2001-1940/Semiseco', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->status() . "\n";
if ($response->status() == 500) {
    echo "Content: " . $response->content() . "\n";
} else {
    echo "Content: " . substr($response->content(), 0, 500) . "\n";
}
