<?php
/**
 * Router — OCP: Registry-based route mapping, DIP: ServiceContainer wiring
 * 
 * Routes are defined in a data structure, not a switch statement.
 * To add a new page, add an entry to $routes — no need to edit routing logic.
 * 
 * Controller routes use closures that receive $container for DI.
 * View-only routes use file paths (string) for direct inclusion.
 */
session_start();

require_once __DIR__ . '/../config/ServiceContainer.php';

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

// ===== DIP: ServiceContainer replaces global helper functions =====
$container = new ServiceContainer();

// ===== OCP Route Registry =====
// Controller routes use closures; view-only routes use file path strings.
// Add new pages here — no need to touch routing logic below.
$routes = [
    // Homepage: wired through PackageController to load packages from DB
    '' => function() use ($container) {
        require_once __DIR__ . '/../controllers/PackageController.php';
        $controller = new PackageController($container->getPackageModel());
        $controller->index();
    },
    'index.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/PackageController.php';
        $controller = new PackageController($container->getPackageModel());
        $controller->index();
    },

    // Auth — SRP: controllers handle business logic, views are pure presentation
    'login.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($container->getUserModel(), $container->getValidator());
        $controller->login();
    },
    'views/login.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($container->getUserModel(), $container->getValidator());
        $controller->login();
    },
    'register.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($container->getUserModel(), $container->getValidator());
        $controller->register();
    },
    'views/register.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($container->getUserModel(), $container->getValidator());
        $controller->register();
    },

    // Profile — SRP: controller handles data + auth guard, view is pure presentation
    'profile.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $controller = new ProfileController($container->getUserModel(), $container->getOrderModel(), $container->getAuthGuard());
        $controller->show();
    },
    'views/profile.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $controller = new ProfileController($container->getUserModel(), $container->getOrderModel(), $container->getAuthGuard());
        $controller->show();
    },

    // Package details — SRP: controller handles data loading + fallbacks
    'package_details.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/PackageDetailsController.php';
        $packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new PackageDetailsController($container->getPackageModel());
        $controller->show($packageId);
    },
    'views/package_details.php' => function() use ($container) {
        require_once __DIR__ . '/../controllers/PackageDetailsController.php';
        $packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new PackageDetailsController($container->getPackageModel());
        $controller->show($packageId);
    },

    // Other view-only routes (no SRP violation — these are simple includes)
    'forgot_password.php'         => '/../views/forgot_password.php',
    'views/forgot_password.php'   => '/../views/forgot_password.php',
    'order_details.php'           => '/../views/order_details.php',
    'views/order_details.php'     => '/../views/order_details.php',

    // Build your own package
    'build_package.php'           => '/../views/build_package.php',
    'views/build_package.php'     => '/../views/build_package.php',

    // Cart & checkout
    'cart.php'                    => '/../views/cart.php',
    'views/cart.php'              => '/../views/cart.php',

    // Order confirmation
    'order_confirmation.php'      => '/../views/order_confirmation.php',
    'views/order_confirmation.php'=> '/../views/order_confirmation.php',

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

    // Auth pages (no business logic in view)
    'logout.php'                  => '/../views/logout.php',
    'views/logout.php'            => '/../views/logout.php',
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
