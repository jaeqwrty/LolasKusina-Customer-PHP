<?php
/**
 * Router for PHP built-in development server
 * Usage: php -S localhost:8000 index.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files from public/ directory
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && is_file($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
    ];
    header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
    readfile($publicFile);
    return true;
}

// Route all other requests through the public app router
require __DIR__ . '/public/app_router.php';
