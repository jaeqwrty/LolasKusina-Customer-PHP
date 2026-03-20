<?php
/**
 * Router — OCP: Registry-based route mapping
 * 
 * Routes are defined in a data structure, not a switch statement.
 * To add a new page, add an entry to $routes — no need to edit routing logic.
 */
session_start();

// Base path for URL generation (supports subdirectory deployments)
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

// Get the requested URI
$request = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$request = str_replace($scriptName, '', $request);
$request = trim($request, '/');

// Remove query string
$request = strtok($request, '?');
if ($request === false) $request = '';

// Serve static files directly (CSS, JS, images, JSON, etc.)
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'json', 'webp'];
$fileExtension = pathinfo($request, PATHINFO_EXTENSION);

if (in_array(strtolower($fileExtension), $staticExtensions)) {
    $filePath = __DIR__ . '/' . $request;
    if (file_exists($filePath)) {
        return false; // Let PHP built-in server handle static files
    }
}

// ===== OCP Route Registry =====
// Add new pages here — no need to touch routing logic below.
$homeRouteHandler = function () {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Package.php';
    require_once __DIR__ . '/../controllers/PackageController.php';

    $controller = new PackageController(new Package(Database::getInstance()));
    $controller->index();
};

$deliveryFeeHandler = function () {
    require_once __DIR__ . '/../controllers/DeliveryFeeController.php';

    $controller = new DeliveryFeeController(new GoogleMatrixService());
    $controller->calculate();
};

$reverseGeocodeHandler = function () {
    require_once __DIR__ . '/../controllers/DeliveryFeeController.php';

    $controller = new DeliveryFeeController(new GoogleMatrixService());
    $controller->reverseGeocode();
};

$routes = [
    ''                            => $homeRouteHandler,
    'index.php'                   => $homeRouteHandler,
    'api/delivery-fee'            => $deliveryFeeHandler,
    'api/reverse-geocode'         => $reverseGeocodeHandler,
    'login.php'                   => '/../views/login.php',
    'views/login.php'             => '/../views/login.php',
    'register.php'                => '/../views/register.php',
    'views/register.php'          => '/../views/register.php',
    'forgot_password.php'         => '/../views/forgot_password.php',
    'views/forgot_password.php'   => '/../views/forgot_password.php',
    'order_details.php'           => '/../views/order_details.php',
    'views/order_details.php'     => '/../views/order_details.php',
    'package_details.php'         => '/../views/package_details.php',
    'views/package_details.php'   => '/../views/package_details.php',
    'build_package.php'           => '/../views/build_package.php',
    'views/build_package.php'     => '/../views/build_package.php',
    'cart.php'                    => '/../views/cart.php',
    'views/cart.php'              => '/../views/cart.php',
    'order_confirmation.php'      => '/../views/order_confirmation.php',
    'views/order_confirmation.php'=> '/../views/order_confirmation.php',
    'reviews.php'                 => '/../views/reviews.php',
    'views/reviews.php'           => '/../views/reviews.php',
    'write_review.php'            => '/../views/write_review.php',
    'views/write_review.php'      => '/../views/write_review.php',
    'review_success.php'          => '/../views/review_success.php',
    'views/review_success.php'    => '/../views/review_success.php',
    'order_history.php'           => '/../views/order_history.php',
    'views/order_history.php'     => '/../views/order_history.php',
    'profile.php'                 => '/../views/profile.php',
    'views/profile.php'           => '/../views/profile.php',
    'logout.php'                  => '/../views/logout.php',
    'views/logout.php'            => '/../views/logout.php',
    'auth_gate.php'               => '/../views/auth_gate.php',
    'views/auth_gate.php'         => '/../views/auth_gate.php',
];

// ===== Route Resolution =====
if (array_key_exists($request, $routes)) {
    $target = $routes[$request];
    if (is_callable($target)) {
        $target();
    } else {
        include __DIR__ . $target;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Page Not Found</h1>";
}
?>
