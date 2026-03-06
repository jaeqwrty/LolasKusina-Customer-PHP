<?php
/**
 * Authentication Utilities — DRY Principle
 * 
 * Centralized auth guard function to ensure consistency across protected pages.
 * Use this instead of repeating auth logic in every protected view.
 */

/**
 * Verify user is authenticated. Redirect to auth gate if not.
 * 
 * @param string $redirectPath The path to redirect back to after login (e.g., '/write_review.php')
 * @param bool $preserveQueryString Whether to include query parameters in the redirect
 * @return void (exits if unauthenticated)
 */
function requireAuth($redirectPath, $preserveQueryString = false) {
    if (!empty($_SESSION['user_id'])) {
        return; // User is authenticated
    }

    // Build redirect URL with optional query string
    $redirect = BASE_PATH . $redirectPath;
    if ($preserveQueryString && !empty($_SERVER['QUERY_STRING'])) {
        $redirect .= '?' . $_SERVER['QUERY_STRING'];
    }

    // Redirect to auth gate
    header('Location: ' . BASE_PATH . '/auth_gate.php?redirect=' . urlencode($redirect));
    exit;
}
?>
