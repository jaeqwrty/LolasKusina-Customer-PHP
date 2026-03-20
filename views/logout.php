<?php
/**
 * Logout — Destroys the session and redirects to the home page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = [];

// Invalidate the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

$basePath = defined('BASE_PATH') ? BASE_PATH : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath === '.') $basePath = '';
header('Location: ' . $basePath . '/');
exit;
