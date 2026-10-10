<?php

// TEMPORARY: show PHP errors in Vercel logs and the browser. Remove once the site works.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');

// Vercel's filesystem is read-only except /tmp, so move Laravel's writable paths there.
$tmp = '/tmp/laravel';

foreach ([
    'storage/framework/views',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/logs',
    'bootstrap/cache',
] as $dir) {
    if (!is_dir("$tmp/$dir")) {
        mkdir("$tmp/$dir", 0777, true);
    }
}

$paths = [
    'LARAVEL_STORAGE_PATH'  => "$tmp/storage",
    'VIEW_COMPILED_PATH'    => "$tmp/storage/framework/views",
    'APP_PACKAGES_CACHE'    => "$tmp/bootstrap/cache/packages.php",
    'APP_SERVICES_CACHE'    => "$tmp/bootstrap/cache/services.php",
    'APP_CONFIG_CACHE'      => "$tmp/bootstrap/cache/config.php",
    'APP_ROUTES_CACHE'      => "$tmp/bootstrap/cache/routes.php",
    'APP_EVENTS_CACHE'      => "$tmp/bootstrap/cache/events.php",
];

foreach ($paths as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__ . '/../public/index.php';