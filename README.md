PHP Amor – Lightweight PHP Framework

PHP Amor is a minimal, lightweight PHP framework designed for rapid development of modular backend apps with built-in template rendering, routing, and security features. Inspired by Laravel and Django, it provides a safe default environment for developing PHP applications with CSRF, XSS prevention, and modular templates.

🌟 Features

Modular backend structure: Easily separate logic into core, admin, or custom modules.

Automatic template rendering: Backend PHP automatically renders templates in /templates/{module}/{file}.html.

Routing system: Map URLs to backend PHP files with central routers.php.

Development & Production servers: Includes server.php for dev and amor.sh for production setup.

Security-first:

CSRF token enforcement for forms

Automatic output escaping (e() helper)

Module whitelist

Template security contract via $SECURE array

Debug-friendly: Toggle debug in config/settings.php to display helpful error messages.

Lightweight & portable: No heavy dependencies required; runs on PHP 8+.

📂 Directory Structure
php-amor/
│
├── admin/                  # Admin module backend logic
├── core/                   # Core module backend logic
│   └── index.php           # Example backend entry point
├── config/
│   ├── config.php          # DB connection and configuration
│   ├── settings.php        # Debug, security flags, and general settings
│   └── models.php          # Auto-create tables (like Django models)
├── gallery/                # Example additional module
├── nginx_template.conf     # Sample Nginx configuration for production
├── routers/
│   ├── controllers.php     # Central controller, routing logic
│   ├── routers.php         # URL → backend mapping
│   ├── render.php          # Central template renderer
│   └── security.php        # Enforces security contract for templates
├── templates/
│   ├── core/               # Templates for core module
│   ├── admin/              # Templates for admin module
│   └── ...                 # Other modules
├── server.php              # Development server entry point
├── amor.sh                 # Production server start helper
└── README.md

⚡ Installation
1. Clone from GitHub
git clone https://github.com/barackblue/php-amor.git
cd php-amor

2. Setup Database

Create a MySQL database:

CREATE DATABASE amor_db;


Configure database credentials in .env (or config/config.php):

DB_HOST=localhost
DB_USER=root
DB_PASS=secret
DB_NAME=amor_db


CREATING AND RUNING MIGRATIONS.
Just edit the models.php to define ur db tables like bellow the comment here
<?php
return [
    //add ur tables definations here 
    'posts' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'filename' => 'VARCHAR(255) NOT NULL',
        'title' => 'VARCHAR(255) NOT NULL',
        'uploaded_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ],

    'users' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'username' => 'VARCHAR(100) NOT NULL',
        'password' => 'VARCHAR(255) NOT NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ]
];


then run 
php config/migrate.php


in case u delete a column or a table in ur db u might wana check the models.php also to dlete it and viserversa is true.

example usage :
┌──(alpha㉿kali-alpha)-[~/…/progress/Dummy projects/my post php site/config]
└─$ php migrate.php
Processing table: posts
 - Adding column: title
Processing table: users
 - Table does not exist. Creating...
✅ Migrations completed successfully!

COMMAND TO CHECK IF DB IS CONNECTED SUCCESSFULY.
┌──(alpha㉿kali-alpha)-[~/…/Projects/progress/Dummy projects/my post php site]
└─$ php status.php       
⚔ PHP AMOR STATUS CHECK
-------------------------
PHP Version: 8.4.11
Debug Mode: ENABLED
Database: CONNECTED
Environment: .env loaded
Router: OK
-------------------------
✔ Status check completed


🖥 Running Development Server
cd /path/to/php-amor
php -S localhost:8000 server.php


Open your browser at http://localhost:8000

Debug errors will display if debug = true in config/settings.php

All routes are controlled via routers/routers.php

🚀 Running Production Server

Ensure PHP-FPM and Nginx (or Apache) are installed.

Edit nginx_template.conf:

server {
    listen 80;
    server_name mysite.local;
    root /var/www/mypostsite;

    index index.php;

    location / {
        try_files $uri /routers/controllers.php;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}


Start server helper:

chmod +x amor.sh
./amor.sh


amor.sh sets up environment and ensures production-ready routing.

Debug messages are suppressed; only generic error messages are shown.

📌 Routing

routers/routers.php maps URLs to backend PHP files:

<?php
$BASE_PATH = realpath(__DIR__ . '/..');

return [
    '/' => $BASE_PATH . '/core/index.php',
    '/admin' => $BASE_PATH . '/admin/dashboard.php',
];


Always point to backend PHP, never directly to .html.

Templates are auto-resolved via render().

🛡 Security

XSS / HTML injection prevention: Use <?= e($var) ?> in templates

CSRF protection: All forms must include hidden CSRF token:

<input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">


Template manifest enforcement:

<?php
$SECURE = [
    'csrf' => true,
    'escaped_variables' => true
];


Security rules enforced via routers/security.php

Violations in debug mode will display descriptive messages

🛠 Commands & Helpers
Command	Description
php -S localhost:8000 server.php	Start dev server
./amor.sh	Start production-ready server
php config/models.php	Auto-create DB tables (like Django models)
git pull	Update framework from GitHub
📝 Recommended Usage

Add new backend module:

/newmodule/index.php
/templates/newmodule/index.html


Register module in routers/routers.php:

'
  // List of allowed backend modules
    $allowedModules = [ // <-- add your backend folders here
        'core',
        'admin',
        'newmodule'
    ]; 



Add $SECURE to template and use e() for all variables.

Done — the framework handles routing, rendering, and security 
automatically.

URLS.
any url won't be live unless its adedd in the routers.php just like

<?php
$BASE_PATH = realpath(__DIR__ . '/..');

return [
    '/' => $BASE_PATH . '/core/index.php',
    //add any url here
];



💡 Philosophy

Lightweight: no heavy dependencies

Secure by default: built-in CSRF & XSS prevention

Modular: backend logic and templates separated

Debug-friendly during development, production-safe out-of-the-box



IT IS USABLE BUT  STILL ON DEVELOPMENT.
