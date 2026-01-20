<?php
function render(array $data = [])
{
    // Load settings (debug mode)
    $settings = require __DIR__ . '/../config/settings.php';

    // List of allowed backend modules
    $allowedModules = [ // <-- add your backend folders here
        'core',
        'admin',
    ]; 

    // Detect the PHP file that called render()
    $caller = debug_backtrace()[0]['file'];

    $base = realpath(__DIR__ . '/..'); // project root
    $relative = str_replace($base . '/', '', $caller);

    // Extract module and file
    $parts = explode('/', $relative);
    $module = $parts[0];
    $file = basename(end($parts), '.php');

    // Check if module is allowed
    if (!in_array($module, $allowedModules)) {
        http_response_code(403);
        if ($settings['debug']) {
            echo "Module '$module' is not allowed to render templates or not registered.";
        } else {
            echo "Forbidden";
        }
        exit;
    }

    // Build template path
    $template = $base . "/templates/$module/$file.html";

    // Check if template exists
    if (!file_exists($template)) {
        http_response_code(500);
        if ($settings['debug']) {
            echo "Template not found: $template";
        } else {
            echo "Server error";
        }
        exit;
    }
    // Enforce security
    require __DIR__ . '/security.php';
    enforce_security($template, $settings, $allowedModules);
    
    // Make backend variables available in template
    extract($data);

    // Render template
    require $template;
}
