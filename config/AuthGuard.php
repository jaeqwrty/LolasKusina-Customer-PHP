<?php
/**
 * Auth Guard — DIP: Injectable authentication guard
 * 
 * Replaces the global requireAuth() function from auth.php.
 * Can be injected into controllers and mocked in tests.
 * SRP: Only handles authentication gating.
 */
class AuthGuard {
    /**
     * Redirect unauthenticated users to the auth gate page.
     *
     * @param string $returnPath Path to redirect back to after login
     */
    public function requireAuth(string $returnPath = '/'): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $basePath = defined('BASE_PATH') ? BASE_PATH : '';
            header('Location: ' . $basePath . '/auth_gate.php?redirect=' . urlencode($returnPath));
            exit;
        }
    }
}
?>
