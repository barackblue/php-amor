<?php
// Load settings
$settings = require __DIR__ . '/settings.php';

// Load .env
$dotenv = [];
$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        if (trim($line) === '' || $line[0] === '#') continue;
        [$key, $val] = explode('=', trim($line), 2);
        $dotenv[$key] = $val;
    }
}

// DB connection 
$mysqli = @new mysqli(
    $dotenv['DB_HOST'] ?? '',
    $dotenv['DB_USER'] ?? '',
    $dotenv['DB_PASS'] ?? '',
    $dotenv['DB_NAME'] ?? ''
);


$dbConnected = !$mysqli->connect_error;

