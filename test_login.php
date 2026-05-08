<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$request = Illuminate\Http\Request::create('/api/auth/login', 'POST', [
    'email' => 'unique_test_register_fix@test.com',
    'password' => 'password123',
]);
$request->headers->set('Accept', 'application/json');
$response = $app->handle($request);
echo "STATUS: " . $response->getStatusCode() . "\n";
echo "BODY: " . substr($response->getContent(), 0, 500) . "\n";
