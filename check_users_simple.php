<?php
// Simple database query script
try {
    $mysqli = new mysqli('localhost', 'root', '', 'indoor_booking_test');

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    echo "=== Users in users_user Table ===\n";
    $result = $mysqli->query("SELECT id, email FROM users_user");

    if ($result->num_rows === 0) {
        echo "ERROR: No users found in users_user table!\n";
    } else {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}, Email: {$row['email']}\n";
        }
    }

    echo "\n=== Users in users Table ===\n";
    $result = $mysqli->query("SELECT id, email FROM users");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}, Email: {$row['email']}\n";
        }
    }

    $mysqli->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
