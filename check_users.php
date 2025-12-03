<?php
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$db = $app->make('db');

echo "=== Users in users_user Table ===\n";
$users = $db->table('users_user')->get();

if ($users->isEmpty()) {
    echo "ERROR: No users found in users_user table!\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id}, Email: {$user->email}\n";
    }
}

echo "\n=== Users in users Table ===\n";
try {
    $authUsers = $db->table('users')->get(['id', 'email']);
    foreach ($authUsers as $user) {
        echo "ID: {$user->id}, Email: {$user->email}\n";
    }
} catch (Exception $e) {
    echo "Error querying users table: {$e->getMessage()}\n";
}

echo "\n=== Check Auth User ===\n";
try {
    $auth = $app->make('auth');
    if ($auth->check()) {
        echo "Authenticated user email: {$auth->user()->email}\n";
    } else {
        echo "No authenticated user\n";
    }
} catch (Exception $e) {
    echo "Error checking auth: {$e->getMessage()}\n";
}
