<?php
$content = file_get_contents('routes/api.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'dashboard') !== false || strpos($line, 'Dashboard') !== false || strpos($line, 'booking-report') !== false || strpos($line, 'sports') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
