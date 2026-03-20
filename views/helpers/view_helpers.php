<?php
/**
 * Shared view helpers for auth and navigation-related pages.
 */

if (!function_exists('getHomePath')) {
    function getHomePath() {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        if ($basePath === '.') {
            $basePath = '';
        }
        return rtrim($basePath, '/') . '/';
    }
}

if (!function_exists('sanitizeRedirectPath')) {
    function sanitizeRedirectPath($redirect) {
        $default = getHomePath();
        if (!is_string($redirect) || $redirect === '') {
            return $default;
        }

        if (!preg_match('/^\/[a-zA-Z0-9\-_.~!$&\'()*+,;=:@\/?%]*$/', $redirect)) {
            return $default;
        }

        return $redirect;
    }
}
