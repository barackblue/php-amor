<?php
function enforce_security(string $template, array $settings, array $allowedModules)
{
    // Detect module
    $base = realpath(__DIR__ . '/..'); // project root
    $relative = str_replace($base . '/', '', $template);
    $parts = explode('/', $relative);
    $module = $parts[1]; // templates/module/file.html

    // Module whitelist
    if (!in_array($module, $allowedModules)) {
        http_response_code(403);
        die($settings['debug'] ? "Forbidden module: $module" : "Forbidden");
    }

    // Template must define $SECURE variable
    $SECURE = [];
    include $template; // temporarily include to read $SECURE
    if (!isset($SECURE) || !is_array($SECURE)) {
        http_response_code(500);
        die($settings['debug'] ? 
            "Security manifest missing in template: $template. Please implement \$SECURE array." 
            : "Server error");
    }

    // CSRF enforcement
    if (!($SECURE['csrf'] ?? false)) {
        http_response_code(500);
        die($settings['debug'] ? 
            "Template $template does not implement CSRF token protection. Please read security manual." 
            : "Server error");
    }

    // Escaping enforcement
    if (!($SECURE['escaped_variables'] ?? false)) {
        http_response_code(500);
        die($settings['debug'] ? 
            "Template $template does not escape user input variables. Potential XSS risk." 
            : "Server error");
    }

    // Passed all checks
    return true;
}
