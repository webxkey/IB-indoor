<?php
try {
    $pdo = new PDO("pgsql:host=127.0.0.1;port=5432;dbname=ibsport", "postgres", "muba123", [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "DB Connection Successful\n";
} catch (Exception $e) {
    echo "DB Connection Failed: " . $e->getMessage() . "\n";
}
