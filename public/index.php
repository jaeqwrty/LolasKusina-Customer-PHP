<?php
// Simple Router / Index File
session_start();

// Get the requested URI
$request = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$request = str_replace($scriptName, '', $request);
$request = trim($request, '/');

// Remove query string
$request = strtok($request, '?');

// Serve static files directly (CSS, JS, images, JSON, etc.)
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'json', 'webp'];
$fileExtension = pathinfo($request, PATHINFO_EXTENSION);

if (in_array(strtolower($fileExtension), $staticExtensions)) {
    $filePath = __DIR__ . '/' . $request;
    if (file_exists($filePath)) {
        return false; // Let PHP built-in server handle static files
    }
}

// Route handling
switch ($request) {
    case '':
    case 'index.php':
        include __DIR__ . '/../views/index.php';
        break;
        
    case 'order_details.php':
    case 'views/order_details.php':
        include __DIR__ . '/../views/order_details.php';
        break;
        
    case 'build_package.php':
    case 'views/build_package.php':
        include __DIR__ . '/../views/build_package.php';
        break;
        
    case 'cart.php':
    case 'views/cart.php':
        include __DIR__ . '/../views/cart.php';
        break;
        
    case 'order_confirmation.php':
    case 'views/order_confirmation.php':
        include __DIR__ . '/../views/order_confirmation.php';
        break;
    
    case 'reviews.php':
    case 'views/reviews.php':
        include __DIR__ . '/../views/reviews.php';
        break;
        
    case 'write_review.php':
    case 'views/write_review.php':
        include __DIR__ . '/../views/write_review.php';
        break;
        
    case 'review_success.php':
    case 'views/review_success.php':
        include __DIR__ . '/../views/review_success.php';
        break;
        
    case 'order_history.php':
    case 'views/order_history.php':
        include __DIR__ . '/../views/order_history.php';
        break;
        
    case 'profile.php':
    case 'views/profile.php':
        include __DIR__ . '/../views/profile.php';
        break;
    
    default:
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page Not Found</h1>";
        break;
}
?>
