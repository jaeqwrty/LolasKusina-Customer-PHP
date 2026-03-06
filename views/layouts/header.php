<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#FF6B35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <?php if (!isset($cartCount)) { $cartCount = count($_SESSION['cart'] ?? []); } ?>
    <title><?php echo $pageTitle ?? "Lola's Kusina"; ?></title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo BASE_PATH; ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?php echo BASE_PATH; ?>/images/icon-192.png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B35',
                        secondary: '#F7931E',
                        accent: '#FFB84D',
                        dark: '#2D2D2D',
                        light: '#F5F5F5'
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            overscroll-behavior-y: contain;
        }
        
        /* Safe Area Insets for notched devices */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        .safe-left { padding-left: env(safe-area-inset-left); }
        .safe-right { padding-right: env(safe-area-inset-right); }
        
        /* Touch-friendly interactions */
        button, a, input, select, textarea {
            touch-action: manipulation;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Prevent text selection on buttons */
        button, .btn {
            -webkit-user-select: none;
            user-select: none;
        }
        
        /* Active state feedback for touch */
        .touch-feedback:active {
            transform: scale(0.97);
            opacity: 0.9;
        }
        
        /* Desktop sidebar nav link active state */
        .sidebar-link.active {
            background-color: #FF6B35;
            color: white;
        }
        .sidebar-link.active svg {
            color: white;
        }
        /* Desktop: smooth scrollable main area */
        @media (min-width: 768px) {
            html, body {
                height: 100%;
                overflow: hidden;
            }
            .desktop-main {
                height: 100vh;
                overflow-y: auto;
            }
        }
        
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
    <!-- Base path for JS navigation -->
    <script>const BASE_PATH = '<?php echo BASE_PATH; ?>';</script>
</head>
<body class="bg-gray-50">

    <!-- ===================== DESKTOP SIDEBAR (hidden on mobile) ===================== -->
    <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-100 fixed top-0 left-0 h-full z-50 shadow-lg">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-100">
            <a href="<?php echo BASE_PATH; ?>/index.php" class="flex items-center space-x-3">
                <img src="<?php echo BASE_PATH; ?>/images/logo.png" alt="Logo" class="h-12 w-12 rounded-full" onerror="this.style.display='none'">
                <div>
                    <span class="text-lg font-bold text-primary block leading-tight">Lola's Kusina</span>
                    <span class="text-xs text-gray-400">Lutong Probinsya</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="<?php echo BASE_PATH; ?>/index.php" class="sidebar-link <?php echo ($currentPage ?? '') === 'packages' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-primary transition font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                <span>Packages</span>
            </a>
            <a href="<?php echo BASE_PATH; ?>/reviews.php" class="sidebar-link <?php echo ($currentPage ?? '') === 'reviews' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-primary transition font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <span>Reviews</span>
            </a>
            <a href="<?php echo BASE_PATH; ?>/cart.php" class="sidebar-link <?php echo ($currentPage ?? '') === 'order' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-primary transition font-medium relative">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path></svg>
                <span>My Order</span>
                <?php if (isset($cartCount) && $cartCount > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo BASE_PATH; ?>/order_history.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-primary transition font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                <span>Order History</span>
            </a>
            <a href="<?php echo BASE_PATH; ?>/profile.php" class="sidebar-link <?php echo ($currentPage ?? '') === 'account' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-primary transition font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                <span>Account</span>
            </a>
        </nav>

        <!-- Login at bottom -->
        <div class="p-4 border-t border-gray-100">
            <button class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-orange-600 active:bg-orange-700 transition shadow-md">
                Login
            </button>
        </div>
    </aside>
    <!-- ============================================================================ -->

    <!-- Main content wrapper: offset by sidebar on desktop -->
    <div class="md:ml-64 desktop-main">

        <!-- ===== MOBILE HEADER (hidden on desktop) ===== -->
        <header class="md:hidden bg-white shadow-md sticky top-0 z-40 safe-top">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <a href="<?php echo BASE_PATH; ?>/index.php" class="flex items-center space-x-2 touch-feedback">
                        <img src="<?php echo BASE_PATH; ?>/images/logo.png" alt="Lola's Kusina Logo" class="h-10 w-10 rounded-full" onerror="this.style.display='none'">
                        <span class="text-xl font-bold text-primary">Lola's Kusina</span>
                    </a>
                    <button class="bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-orange-600 active:bg-orange-700 transition touch-feedback">
                        Login
                    </button>
                </div>
            </div>
        </header>

        <!-- ===== DESKTOP TOP BAR (hidden on mobile) ===== -->
        <header class="hidden md:flex bg-white border-b border-gray-200 sticky top-0 z-40 px-8 py-4 items-center justify-between shadow-sm">
            <h1 class="text-2xl font-bold text-gray-800"><?php echo $pageTitle ?? "Lola's Kusina"; ?></h1>
            <div class="flex items-center space-x-4">
                <!-- Cart icon -->
                <a href="<?php echo BASE_PATH; ?>/cart.php" class="relative p-2 text-gray-600 hover:text-primary transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path></svg>
                    <?php if (isset($cartCount) && $cartCount > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
                <button class="bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-orange-600 transition shadow-md">
                    Login
                </button>
            </div>
        </header>
