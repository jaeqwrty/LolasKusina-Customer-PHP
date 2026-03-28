<?php
// Auth Gate Page — shown when an unauthenticated user tries to access a protected page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure BASE_PATH is available even if accessed without going through the router
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
}
require_once __DIR__ . '/helpers/view_helpers.php';

// If already logged in, go straight to the app
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . getHomePath());
    exit;
}

$pageTitle = "Welcome - Lola's Kusina";
$redirect = sanitizeRedirectPath($_GET['redirect'] ?? null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a3a36">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B35',
                    },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior-y: contain;
        }
        button, a { touch-action: manipulation; }
        .touch-feedback:active { transform: scale(0.97); opacity: 0.9; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center px-4 py-6 md:py-12">

    <!-- ── Main Container ── -->
    <div class="w-full max-w-6xl">
        <!-- Desktop: Grid Layout -->
        <div class="hidden md:grid md:grid-cols-2 md:gap-8 md:items-center">
            
            <!-- Hero Section Left -->
            <div class="relative rounded-2xl overflow-hidden h-96 bg-[#1a3a36] shadow-xl">
                <!-- Hero food image -->
                <img
                    src="<?php echo BASE_PATH; ?>/images/hero-food.jpg"
                    alt="Filipino food spread"
                    class="w-full h-full object-cover"
                    onerror="this.style.display='none'"
                >
                <!-- Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a3a36]/60 to-transparent"></div>
                <!-- Desktop Hero Content -->
                <div class="absolute inset-0 flex flex-col justify-end p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-6 h-6 text-white flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0010 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0015 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/>
                        </svg>
                        <span class="text-lg font-bold text-white">Lola's Kusina</span>
                    </div>
                    <p class="text-white/90 font-semibold text-sm">Authentic Filipino Catering</p>
                    <p class="text-white/70 text-xs">For every celebration and occasion</p>
                </div>
            </div>

            <!-- Content Section Right -->
            <div class="space-y-6">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0010 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0015 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Welcome to</p>
                        <p class="text-lg font-bold text-gray-900">Lola's Kusina</p>
                    </div>
                </div>

                <!-- Heading -->
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-1">
                        Ready to <span class="text-primary">order</span>?
                    </h1>
                    <p class="text-gray-600 text-sm">Sign in to explore our menu, book catering, and manage your orders.</p>
                </div>

                <!-- Features Grid (Compact) -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Review -->
                    <div class="bg-white rounded-lg p-3 border border-orange-100 hover:border-orange-200 hover:shadow-sm transition">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mb-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-xs">Reviews</p>
                        <p class="text-gray-500 text-xs">Share feedback</p>
                    </div>

                    <!-- Booking -->
                    <div class="bg-white rounded-lg p-3 border border-blue-100 hover:border-blue-200 hover:shadow-sm transition">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mb-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-xs">Booking</p>
                        <p class="text-gray-500 text-xs">Plan in minutes</p>
                    </div>

                    <!-- Track Order History -->
                    <div class="bg-white rounded-lg p-3 border border-purple-100 hover:border-purple-200 hover:shadow-sm transition">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mb-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-xs">Order History</p>
                        <p class="text-gray-500 text-xs">Track orders</p>
                    </div>

                    <!-- Support -->
                    <div class="bg-white rounded-lg p-3 border border-green-100 hover:border-green-200 hover:shadow-sm transition">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mb-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-xs">Support</p>
                        <p class="text-gray-500 text-xs">24/7 help</p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="space-y-2.5 pt-4">
                    <!-- Primary Button: Create Account -->
                    <a href="<?php echo BASE_PATH; ?>/register.php?redirect=<?php echo urlencode($redirect); ?>"
                       class="w-full bg-primary text-white py-3 rounded-lg font-bold text-sm text-center flex items-center justify-center space-x-2 touch-feedback shadow-md hover:bg-orange-600 active:bg-orange-700 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Create New Account</span>
                    </a>

                    <!-- Secondary Button: Log In -->
                    <a href="<?php echo BASE_PATH; ?>/login.php?redirect=<?php echo urlencode($redirect); ?>"
                       class="w-full bg-white border-2 border-primary text-primary py-3 rounded-lg font-bold text-sm text-center flex items-center justify-center space-x-2 touch-feedback hover:bg-orange-50 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign In</span>
                    </a>

                    <!-- Tertiary Button: Continue as Guest -->
                    <a href="<?php echo BASE_PATH; ?>/"
                       class="w-full text-gray-700 py-2.5 rounded-lg font-medium text-sm text-center touch-feedback hover:text-primary transition duration-200">
                        Continue browsing as guest
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile: Single Column Layout -->
        <div class="md:hidden flex flex-col space-y-6">
            <!-- Hero Section -->
            <div class="relative rounded-2xl overflow-hidden h-48 bg-[#1a3a36] shadow-lg">
                <!-- Hero food image -->
                <img
                    src="<?php echo BASE_PATH; ?>/images/hero-food.jpg"
                    alt="Filipino food spread"
                    class="w-full h-full object-cover"
                    onerror="this.style.display='none'"
                >
                <!-- Overlay gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a3a36]/70 via-[#1a3a36]/40 to-transparent"></div>
                <!-- Mobile Hero Content -->
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <svg class="w-6 h-6 text-white flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0010 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0015 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/>
                        </svg>
                        <span class="text-xl font-bold text-white">Lola's Kusina</span>
                    </div>
                    <p class="text-white/90 font-semibold text-sm">Authentic Filipino Catering</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="bg-white rounded-2xl p-6 shadow-lg space-y-5">
                <!-- Heading -->
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-1">
                        Ready to <span class="text-primary">order</span>?
                    </h1>
                    <p class="text-gray-600 text-sm">Sign in to explore our menu and book catering.</p>
                </div>

                <!-- Features List (Mobile) -->
                <div class="space-y-2.5">
                    <!-- Review -->
                    <div class="flex items-center space-x-3 p-3 bg-orange-50 rounded-lg border border-orange-100">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Leave Reviews</p>
                            <p class="text-gray-500 text-xs">Share your experience</p>
                        </div>
                    </div>

                    <!-- Booking -->
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Easy Booking</p>
                            <p class="text-gray-500 text-xs">Plan in minutes</p>
                        </div>
                    </div>

                    <!-- Track Order History -->
                    <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Track Orders</p>
                            <p class="text-gray-500 text-xs">View order history</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="space-y-2.5 pt-2">
                    <!-- Primary Button: Create Account -->
                    <a href="<?php echo BASE_PATH; ?>/register.php?redirect=<?php echo urlencode($redirect); ?>"
                       class="w-full bg-primary text-white py-3 rounded-lg font-bold text-sm text-center flex items-center justify-center space-x-2 touch-feedback shadow-md hover:bg-orange-600 active:bg-orange-700 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Create New Account</span>
                    </a>

                    <!-- Secondary Button: Log In -->
                    <a href="<?php echo BASE_PATH; ?>/login.php?redirect=<?php echo urlencode($redirect); ?>"
                       class="w-full bg-white border-2 border-primary text-primary py-3 rounded-lg font-bold text-sm text-center flex items-center justify-center space-x-2 touch-feedback hover:bg-orange-50 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign In</span>
                    </a>

                    <!-- Tertiary Button: Continue as Guest -->
                    <a href="<?php echo BASE_PATH; ?>/"
                       class="w-full text-gray-700 py-2.5 rounded-lg font-medium text-sm text-center touch-feedback hover:text-primary transition duration-200">
                        Continue browsing as guest
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
