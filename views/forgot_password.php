<?php
/**
 * Forgot Password Page
 * TODO: Implement password reset functionality
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/helpers/view_helpers.php';

$pageTitle = "Forgot Password - Lola's Kusina";

// If already logged in, redirect home
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . getHomePath());
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#FF6B35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#FF6B35' },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%); }
        button, a { touch-action: manipulation; }
        .touch-feedback:active { transform: scale(0.97); opacity: 0.9; }
        .input-field { font-size: 16px; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen lg:h-screen flex items-center justify-center px-4 py-8 lg:py-0 lg:overflow-hidden">

    <div class="w-full max-w-sm lg:max-w-3xl">

        <!-- ══ DESKTOP (lg+): landscape two-column card ══ -->
        <div class="hidden lg:flex bg-white rounded-3xl shadow-xl overflow-hidden fade-in" style="min-height:0">

            <!-- Left: brand panel -->
            <div class="w-2/5 bg-gradient-to-br from-orange-50 to-orange-100 flex flex-col items-center justify-center p-10 space-y-6">
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-md">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Account Recovery</p>
                    <p class="text-xl font-bold text-gray-900">Lola's Kusina</p>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-sm text-gray-600 leading-relaxed">We'll help you get back into your account and continue enjoying authentic Filipino meals.</p>
                </div>
                <!-- Decorative dots -->
                <div class="flex space-x-2 pt-2">
                    <div class="w-2 h-2 bg-primary rounded-full opacity-80"></div>
                    <div class="w-2 h-2 bg-orange-300 rounded-full opacity-60"></div>
                    <div class="w-2 h-2 bg-orange-200 rounded-full opacity-40"></div>
                </div>
            </div>

            <!-- Right: form panel -->
            <div class="flex-1 flex flex-col justify-center p-10 space-y-5">
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/login.php', ENT_QUOTES, 'UTF-8'); ?>"
                   class="inline-flex items-center text-gray-500 hover:text-primary text-sm font-medium transition self-start">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Login
                </a>

                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-gray-900">Forgot Password?</h1>
                    <p class="text-gray-500 text-sm">Enter your registered mobile number and we'll send you a reset link.</p>
                </div>

                <!-- Notice banner -->
                <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl flex items-start space-x-3">
                    <svg class="w-4 h-4 text-primary flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-orange-700 text-sm">Password reset via SMS is coming soon. Please contact us directly for account assistance.</p>
                </div>

                <!-- Form (disabled) -->
                <form class="space-y-4" onsubmit="return false;">
                    <div class="space-y-1">
                        <label for="phone_d" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Mobile Number</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" id="phone_d" name="phone" placeholder="+63 912 345 6789" disabled
                                class="input-field w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-400 placeholder-gray-300 cursor-not-allowed">
                        </div>
                    </div>
                    <button type="submit" disabled
                        class="w-full bg-gray-200 text-gray-400 py-3 rounded-full font-bold text-sm tracking-widest cursor-not-allowed uppercase">
                        Send Reset Link
                    </button>
                </form>

                <p class="text-gray-500 text-sm">
                    Remembered it?
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/login.php', ENT_QUOTES, 'UTF-8'); ?>"
                       class="font-bold text-primary hover:text-orange-600 transition">Login</a>
                </p>
            </div>
        </div>

        <!-- ══ MOBILE (< lg): single column card ══ -->
        <div class="lg:hidden">
            <div class="bg-white rounded-3xl shadow-xl px-6 py-8 sm:px-8 space-y-6 fade-in">

                <a href="<?php echo htmlspecialchars(BASE_PATH . '/login.php', ENT_QUOTES, 'UTF-8'); ?>"
                   class="inline-flex items-center text-gray-600 hover:text-primary text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Login
                </a>

                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Forgot Password?</h1>
                        <p class="text-gray-500 text-sm mt-1">Enter your mobile number to receive a reset link.</p>
                    </div>
                </div>

                <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl flex items-start space-x-3">
                    <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-orange-700 text-sm">Password reset via SMS is coming soon. Please contact us directly for account assistance.</p>
                </div>

                <form class="space-y-4" onsubmit="return false;">
                    <div class="space-y-2">
                        <label for="phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Mobile Number</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" id="phone" name="phone" placeholder="+63 912 345 6789" disabled
                                class="input-field w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-400 placeholder-gray-300 cursor-not-allowed">
                        </div>
                    </div>
                    <button type="submit" disabled
                        class="w-full bg-gray-200 text-gray-400 py-3 rounded-full font-bold text-sm tracking-widest cursor-not-allowed uppercase">
                        Send Reset Link
                    </button>
                </form>

                <p class="text-center text-gray-500 text-sm">
                    Remembered it?
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/login.php', ENT_QUOTES, 'UTF-8'); ?>"
                       class="font-bold text-primary hover:text-orange-600 transition">Login</a>
                </p>
            </div>

            <!-- Brand footer -->
            <div class="flex justify-center mt-6">
                <div class="text-center space-y-1">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0010 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0015 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/>
                        </svg>
                        <span class="font-bold text-gray-900 text-sm">Lola's Kusina</span>
                    </div>
                    <p class="text-gray-400 text-xs">Authentic Filipino Catering</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
