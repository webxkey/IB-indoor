<?php
// Fix the missing user in users_user table
try {
    $mysqli = new mysqli('localhost', 'root', '', 'indoor_booking_test');
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "=== Syncing Staff User to users_user Table ===\n";
    
    // Check if minhaj@gmail.com exists in users_user
    $result = $mysqli->query("SELECT id FROM users_user WHERE email = 'minhaj@gmail.com'");
    
    if ($result->num_rows > 0) {
        echo "✓ minhaj@gmail.com already exists in users_user table\n";
    } else {
        echo "Adding minhaj@gmail.com to users_user table...\n";
        
        // Insert the user
        $sql = "INSERT INTO users_user (email) VALUES ('minhaj@gmail.com')";
        if ($mysqli->query($sql)) {
            echo "✓ Successfully added minhaj@gmail.com\n";
            echo "  New ID: " . $mysqli->insert_id . "\n";
        } else {
            echo "✗ Error: " . $mysqli->error . "\n";
        }
    }
    
    // Verify all staff users from 'users' table exist in 'users_user'
    echo "\n=== Checking All Staff Users ===\n";
    $usersResult = $mysqli->query("SELECT id, email FROM users");
    
    $missing = 0;
    while ($user = $usersResult->fetch_assoc()) {
        $checkResult = $mysqli->query("SELECT id FROM users_user WHERE email = '" . $mysqli->real_escape_string($user['email']) . "'");
        
        if ($checkResult->num_rows === 0) {
            echo "✗ Missing in users_user: {$user['email']}\n";
            $insertSql = "INSERT INTO users_user (email) VALUES ('" . $mysqli->real_escape_string($user['email']) . "')";
            if ($mysqli->query($insertSql)) {
                echo "  ✓ Added\n";
            }
            $missing++;
        } else {
            echo "✓ Exists: {$user['email']}\n";
        }
    }
    
    echo "\n=== Final Check ===\n";
    $finalCheck = $mysqli->query("SELECT COUNT(*) as count FROM users_user WHERE email = 'minhaj@gmail.com'");
    $row = $finalCheck->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo "✓ SUCCESS: minhaj@gmail.com is now in users_user table\n";
        echo "✓ Booking system should now work properly\n";
    } else {
        echo "✗ FAILED: User still not found\n";
    }
    
    $mysqli->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
