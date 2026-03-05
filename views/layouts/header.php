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
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icon-192.png">
    
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
        
        /* Desktop enhancement - center content */
        @media (min-width: 768px) {
            .mobile-container {
                max-width: 480px;
                margin-left: auto;
                margin-right: auto;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                min-height: 100vh;
                background: white;
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
</head>
<body class="bg-gray-50 md:bg-gray-200">
    <!-- Mobile Container Wrapper for Desktop View -->
    <div class="mobile-container bg-gray-50 relative">
    
    <!-- Header Navigation -->
    <header class="bg-white shadow-md sticky top-0 z-50 safe-top">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2 touch-feedback">
                    <img src="/images/logo.png" alt="Lola's Kusina Logo" class="h-10 w-10 rounded-full" onerror="this.style.display='none'">
                    <span class="text-xl font-bold text-primary">Lola's Kusina</span>
                </a>
                
                <!-- Login Button -->
                <button class="bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-orange-600 active:bg-orange-700 transition touch-feedback">
                    Login
                </button>
            </div>
        </div>
    </header>
