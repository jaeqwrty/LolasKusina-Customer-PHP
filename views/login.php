<?php
/**
 * Login Page — Minimalist Design
 * Handles user authentication with mobile number and password
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = "Log In - Lola's Kusina";
$redirect = $_GET['redirect'] ?? BASE_PATH . '/index.php';

// Validate redirect URL
if (!preg_match('/^\/[a-zA-Z0-9\-_.~!$&\'()*+,;=:@\/?%]*$/', $redirect)) {
    $redirect = BASE_PATH . '/index.php';
}

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($phone) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $db = Database::getInstance();
            $result = $db->execute(
                "SELECT user_id, first_name, last_name, password_hash FROM users WHERE phone_number = ? AND is_active = 1",
                [$phone]
            );

            if (!empty($result)) {
                $user = $result[0];
                if (password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

                    // Update last login
                    $db->execute(
                        "UPDATE users SET last_login_at = NOW() WHERE user_id = ?",
                        [$user['user_id']]
                    );

                    // Redirect to requested page
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    $error = 'Invalid phone number or password.';
                }
            } else {
                $error = 'Invalid phone number or password.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again later.';
        }
    }
}

// If already logged in, redirect
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $redirect);
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
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-tap-highlight-color: transparent;
            background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
        }
        button, a { touch-action: manipulation; }
        .touch-feedback:active { transform: scale(0.97); opacity: 0.9; }
        .input-field { font-size: 16px; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen lg:h-screen flex items-center justify-center px-4 py-8 lg:py-0 lg:overflow-hidden">

    <!-- ── Page wrapper ── -->
    <div class="w-full max-w-4xl">

        <!-- ══ DESKTOP (lg+): landscape card ══ -->
        <div class="hidden lg:flex bg-white rounded-3xl shadow-xl overflow-hidden fade-in" style="min-height:480px">
            
            <!-- ── Left: brand panel ── -->
            <div class="w-2/5 bg-gradient-to-br from-orange-50 to-orange-100 flex flex-col items-center justify-center p-10 space-y-6">
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-md">
                    <svg class="w-10 h-10 text-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0110 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0115 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/>
                    </svg>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Welcome back to</p>
                    <p class="text-xl font-bold text-gray-900">Lola's Kusina</p>
                </div>
                <div class="space-y-3 w-full">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                        </div>
                        <p class="text-sm text-gray-700 font-medium">Order Your Favorites</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        </div>
                        <p class="text-sm text-gray-700 font-medium">Book Catering Events</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-sm text-gray-700 font-medium">Track Your Orders</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <div class="w-2 h-2 bg-primary rounded-full opacity-80"></div>
                    <div class="w-2 h-2 bg-orange-300 rounded-full opacity-60"></div>
                    <div class="w-2 h-2 bg-orange-200 rounded-full opacity-40"></div>
                </div>
            </div>

            <!-- ── Right: form panel ── -->
            <div class="flex-1 flex flex-col justify-center px-10 py-10 space-y-5">

                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome Back!</h1>
                    <p class="text-gray-500 text-sm">Mag-login para maka-order</p>
                </div>

                <!-- Error -->
                <?php if ($error): ?>
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700 text-sm font-medium"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" action="" class="space-y-4">

                    <div class="space-y-1">
                        <label for="phone_d" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Mobile Number</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="tel" id="phone_d" name="phone" placeholder="+63 912 345 6789"
                                class="input-field w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-orange-100 transition"
                                required autocomplete="tel">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="password_d" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Password</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" id="password_d" name="password" placeholder="••••••••"
                                class="input-field w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-orange-100 transition"
                                required autocomplete="current-password">
                            <button type="button" onclick="togglePwd('password_d','eyeD')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <svg id="eyeD" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="<?php echo htmlspecialchars(BASE_PATH . '/forgot_password.php', ENT_QUOTES, 'UTF-8'); ?>" class="text-primary text-sm font-semibold hover:text-orange-600 transition">Forgot Password?</a>
                    </div>

                    <button type="submit" class="touch-feedback w-full bg-gradient-to-r from-primary to-orange-500 text-white py-3 rounded-full font-bold text-sm tracking-widest shadow-lg hover:from-orange-600 hover:to-orange-600 transition">LOGIN</button>
                </form>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                    <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-400">or</span></div>
                </div>

                <p class="text-gray-600 text-sm">Wala pang account? <a href="<?php echo htmlspecialchars(BASE_PATH . '/register.php?redirect=' . urlencode($redirect), ENT_QUOTES, 'UTF-8'); ?>" class="font-bold text-primary hover:text-orange-600 transition">Register dito</a></p>
                <p class="text-center text-gray-400 text-xs hover:text-gray-600 transition">Continue browsing as guest</p>
            </div>
        </div>

        <!-- ══ MOBILE / TABLET (< lg): single column ══ -->
        <div class="lg:hidden">
            <div class="bg-white rounded-3xl shadow-xl px-6 py-8 sm:px-8 space-y-6 fade-in">

                <!-- Back -->
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/auth_gate.php', ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center text-gray-600 hover:text-primary text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </a>

                <!-- Header -->
                <div class="space-y-1 text-center">
                    <div class="flex justify-center mb-3">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-orange-500 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome Back!</h1>
                    <p class="text-gray-500 text-sm">Mag-login para maka-order</p>
                </div>

                <!-- Error -->
                <?php if ($error): ?>
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700 text-sm font-medium"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" action="" class="space-y-5">

                    <div class="space-y-2">
                        <label for="phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Mobile Number</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="tel" id="phone" name="phone" placeholder="+63 912 345 6789"
                                class="input-field w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-orange-100 transition"
                                required autocomplete="tel">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Password</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" id="password" name="password" placeholder="••••••••"
                                class="input-field w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-orange-100 transition"
                                required autocomplete="current-password">
                            <button type="button" onclick="togglePwd('password','eyeM')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <svg id="eyeM" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="<?php echo htmlspecialchars(BASE_PATH . '/forgot_password.php', ENT_QUOTES, 'UTF-8'); ?>" class="text-primary text-sm font-semibold hover:text-orange-600 transition">Forgot Password?</a>
                    </div>

                    <button type="submit" class="touch-feedback w-full bg-gradient-to-r from-primary to-orange-500 text-white py-3 rounded-full font-bold text-sm tracking-widest shadow-lg hover:from-orange-600 hover:to-orange-600 transition">LOGIN</button>
                </form>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                    <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-400">or</span></div>
                </div>

                <p class="text-center text-gray-600 text-sm">Wala pang account? <a href="<?php echo htmlspecialchars(BASE_PATH . '/register.php?redirect=' . urlencode($redirect), ENT_QUOTES, 'UTF-8'); ?>" class="font-bold text-primary hover:text-orange-600 transition">Register dito</a></p>
                <p class="text-center"><a href="<?php echo htmlspecialchars(BASE_PATH . '/index.php', ENT_QUOTES, 'UTF-8'); ?>" class="text-gray-400 text-xs hover:text-gray-600 transition">Continue browsing as guest</a></p>
            </div><!-- end mobile card -->

            <!-- Brand footer -->
            <div class="flex justify-center mt-6">
                <div class="text-center space-y-1">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a1 1 0 00-1 1v4a3 3 0 002 2.83V17a1 1 0 102 0v-6.17A3 3 0 0010 8V4a1 1 0 00-1-1H5zM7 4.5V8a1 1 0 01-2 0V4.5h2zM14 3a1 1 0 00-1 1v4.17A3 3 0 0115 11v6a1 1 0 102 0v-6a3 3 0 002-2.83V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-2 0v3h-1V4a1 1 0 00-1-1z"/></svg>
                        <span class="font-bold text-gray-900 text-sm">Lola's Kusina</span>
                    </div>
                    <p class="text-gray-400 text-xs">Authentic Filipino Catering</p>
                </div>
            </div>
        </div><!-- end mobile section -->

    </div><!-- end page wrapper -->

    <script>
        const EYE_OPEN  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        const EYE_CLOSE = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-3.368A9.967 9.967 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

        function togglePwd(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            input.type  = input.type === 'password' ? 'text' : 'password';
            icon.innerHTML = input.type === 'text' ? EYE_CLOSE : EYE_OPEN;
        }
    </script>

</body>
</html>
