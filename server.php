<?php
// If request is for an existing static file, serve it normally
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && file_exists($file)) {
    return false; // let PHP serve the file
}

// Otherwise, EVERYTHING goes through controllers
require __DIR__ . '/routers/controllers.php';
