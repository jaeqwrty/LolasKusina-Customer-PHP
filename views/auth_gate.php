<?php
// Auth Gate Page — shown when an unauthenticated user tries to access a protected page
$pageTitle = "Welcome - Lola's Kusina";
$redirect = $_GET['redirect'] ?? '/';

// Validate redirect to only allow relative paths (prevent open redirect)
if (!preg_match('/^\/[a-zA-Z0-9\-_.~!$&\'()*+,;=:@\/?%]*$/', $redirect)) {
    $redirect = '/';
}
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
<body class="bg-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm mx-auto min-h-screen flex flex-col">

        <!-- ── Hero Section ── -->
        <div class="relative bg-[#1a3a36] overflow-hidden" style="border-radius: 0 0 2.5rem 2.5rem; min-height: 260px;">

            <!-- Pill logo -->
            <div class="absolute top-5 left-1/2 -translate-x-1/2 z-10">
                <div class="flex items-center space-x-2 bg-white rounded-full px-4 py-2 shadow-md">
                    <!-- Fork & knife icon -->
                    <svg class="w-4 h-4 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0010 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0015 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/>
                    </svg>
                    <span class="text-sm font-bold text-gray-800">Lola's Kusina</span>
                </div>
            </div>

            <!-- Hero food image -->
            <img
                src="/images/hero-food.jpg"
                alt="Filipino food spread"
                class="w-full h-64 object-cover opacity-75"
                onerror="this.style.display='none'"
            >
        </div>

        <!-- ── Content ── -->
        <div class="flex-1 flex flex-col px-6 pt-8 pb-8">

            <!-- Heading -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
                    Welcome to <span class="text-primary">Lola's</span>
                </h1>
                <p class="text-gray-500 text-sm mt-2">Authentic Filipino catering for every occasion.</p>
            </div>

            <!-- Feature list -->
            <div class="space-y-3 mb-8">

                <!-- Review -->
                <div class="flex items-center space-x-4 bg-gray-50 rounded-2xl px-4 py-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Mag-leave ng review</p>
                        <p class="text-gray-400 text-xs">Share your dining experience</p>
                    </div>
                </div>

                <!-- Booking -->
                <div class="flex items-center space-x-4 bg-gray-50 rounded-2xl px-4 py-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Easy catering booking</p>
                        <p class="text-gray-400 text-xs">Plan your fiesta in minutes</p>
                    </div>
                </div>

                <!-- Promos -->
                <div class="flex items-center space-x-4 bg-gray-50 rounded-2xl px-4 py-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Exclusive promos</p>
                        <p class="text-gray-400 text-xs">Get deals on bulk orders</p>
                    </div>
                </div>

            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col space-y-3 mt-auto">

                <a href="/register.php?redirect=<?php echo urlencode($redirect); ?>"
                   class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-base text-center flex items-center justify-center space-x-2 touch-feedback shadow-md hover:bg-orange-600 active:bg-orange-700 transition">
                    <span>Create Account</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="/login.php?redirect=<?php echo urlencode($redirect); ?>"
                   class="w-full bg-white border border-gray-200 text-gray-800 py-4 rounded-2xl font-semibold text-base text-center touch-feedback hover:bg-gray-50 transition">
                    Log In
                </a>

                <a href="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>"
                   class="text-center text-sm text-gray-400 py-2 touch-feedback hover:text-gray-600 transition">
                    Skip for now
                </a>

            </div>

        </div>
    </div>

</body>
</html>
