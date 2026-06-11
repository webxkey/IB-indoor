<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sports = \App\Models\BookingSport::where('venue_id', 2)->get();
foreach($sports as $sport) {
    echo $sport->name . ' - Status: "' . $sport->status . "\"\n";
}
