<?php

// Mock a login request and check the result
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'gg@gmail.com';
$password = 'muba123'; // assuming this is the password for the test user

$request = Request::create('/api/auth/login', 'POST', [
    'email' => $email,
    'password' => $password
]);

$response = $app->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT) . "\n";
