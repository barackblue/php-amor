#!/usr/bin/env php
<?php

// CLI only
if (php_sapi_name() !== 'cli') {
    exit("Forbidden\n");
}

require __DIR__ . '/config/config.php';

// Colors
function green($m)  { return "\033[32m$m\033[0m"; }
function red($m)    { return "\033[31m$m\033[0m"; }
function yellow($m) { return "\033[33m$m\033[0m"; }

echo green("⚔ PHP AMOR STATUS CHECK\n");
echo "-------------------------\n";

echo "PHP Version: " . PHP_VERSION . "\n";

echo "Debug Mode: ";
echo $settings['debug'] ? yellow("ENABLED\n") : green("DISABLED\n");

echo "Database: ";
if ($dbConnected) {
    echo green("CONNECTED\n");
} else {
    echo red("FAILED\n");
    if ($settings['debug']) {
        echo red("Reason: " . $mysqli->connect_error . "\n");
    }
}

echo "Environment: ";
echo file_exists(__DIR__ . '/config/.env')
    ? green(".env loaded\n")
    : red(".env missing\n");

echo "Router: ";
echo file_exists(__DIR__ . '/routers/controllers.php')
    ? green("OK\n")
    : red("MISSING\n");

echo "-------------------------\n";
echo green("✔ Status check completed\n");
