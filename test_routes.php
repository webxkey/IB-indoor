<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $routeCollection = Route::getRoutes();
    echo "Total registered routes: " . count($routeCollection) . "\n";
    echo "Routes compiled successfully!\n";
} catch (\Exception $e) {
    echo "Error compiling routes: " . $e->getMessage() . "\n";
}
