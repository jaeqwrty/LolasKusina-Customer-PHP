<?php
/**
 * Router — OCP: Registry-based route mapping
 * 
 * Routes are defined in a data structure, not a switch statement.
 * To add a new page, add an entry to $routes — no need to edit routing logic.
 * 
 * Controller routes use closures to instantiate and invoke controllers.
 * View-only routes use file paths (string) for direct inclusion.
 */
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/InputValidator.php';

// Base path for URL generation (supports subdirectory deployments)
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

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

// ===== Shared Dependencies =====
// Lazy-loaded: only instantiated when a controller route is matched.
$db = null;
$validator = null;

function getDb() {
    global $db;
    if ($db === null) {
        $db = new Database();
    }
    return $db;
}

function getValidator() {
    global $validator;
    if ($validator === null) {
        $validator = new InputValidator();
    }
    return $validator;
}

// ===== OCP Route Registry =====
// Controller routes use closures; view-only routes use file path strings.
// Add new pages here — no need to touch routing logic below.
$routes = [
    // Homepage: wired through PackageController to load packages from DB
    '' => function() {
        require_once __DIR__ . '/../controllers/PackageController.php';
        require_once __DIR__ . '/../models/Package.php';
        $controller = new PackageController(new Package(getDb()));
        $controller->index();
    },
    'index.php' => function() {
        require_once __DIR__ . '/../controllers/PackageController.php';
        require_once __DIR__ . '/../models/Package.php';
        $controller = new PackageController(new Package(getDb()));
        $controller->index();
    },

    // Package details
    'package_details.php'         => '/../views/package_details.php',
    'views/package_details.php'   => '/../views/package_details.php',

    // Build your own package
    'build_package.php'           => '/../views/build_package.php',
    'views/build_package.php'     => '/../views/build_package.php',

    // Cart & checkout
    'cart.php'                    => '/../views/cart.php',
    'views/cart.php'              => '/../views/cart.php',

    // Order confirmation
    'order_confirmation.php'      => '/../views/order_confirmation.php',
    'views/order_confirmation.php'=> '/../views/order_confirmation.php',

    // Order details
    'order_details.php'           => '/../views/order_details.php',
    'views/order_details.php'     => '/../views/order_details.php',

    // Order history
    'order_history.php'           => '/../views/order_history.php',
    'views/order_history.php'     => '/../views/order_history.php',

    // Reviews
    'reviews.php'                 => '/../views/reviews.php',
    'views/reviews.php'           => '/../views/reviews.php',
    'write_review.php'            => '/../views/write_review.php',
    'views/write_review.php'      => '/../views/write_review.php',
    'review_success.php'          => '/../views/review_success.php',
    'views/review_success.php'    => '/../views/review_success.php',

    // Profile & Auth
    'profile.php'                 => '/../views/profile.php',
    'views/profile.php'           => '/../views/profile.php',
    'auth_gate.php'               => '/../views/auth_gate.php',
    'views/auth_gate.php'         => '/../views/auth_gate.php',

    // Order status tracker (FR-C09 prep — route reserved)
    'track_order.php'             => '/../views/track_order.php',
    'views/track_order.php'       => '/../views/track_order.php',
];

// ===== Route Resolution =====
if (array_key_exists($request, $routes)) {
    $route = $routes[$request];
    if (is_callable($route)) {
        // Controller route: invoke the closure
        $route();
    } else {
        // View-only route: include the file directly
        include __DIR__ . $route;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Page Not Found</h1>";
}
?>
