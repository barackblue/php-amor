<?php
session_start();

// Load settings
$settings = require __DIR__ . '/../config/settings.php';

// Load routes
$routes = require __DIR__ . '/routers.php';

// Normalize URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
if ($uri === '') $uri = '/';

// 1️Route must exist
if (!array_key_exists($uri, $routes)) {
    http_response_code(404);
    echo $settings['debug']
        ? "Route '$uri' not defined in routers.php"
        : "404 Not Found";
    exit;
}

// 2️Route exists → validate file path
$target = realpath($routes[$uri]);

if ($target === false) {
    http_response_code(500);
    echo $settings['debug']
        ? "Route file missing on disk: {$routes[$uri]}"
        : "Server error";
    exit;
}

// 3️All good → load controller
require $target;
exit;
