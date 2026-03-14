<?php
/**
 * Application Configuration — SRP
 * 
 * Single source of truth for all configuration constants.
 * Reads from .env file for environment-specific values (NFR-S: no hardcoded secrets).
 * Separated from the Database class so config and infrastructure are independent concerns.
 */

// Load environment variables from .env file
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $envVars = parse_ini_file($envPath);
    if ($envVars !== false) {
        foreach ($envVars as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

// Database credentials
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'lolas_kusina');

// Application settings
define('APP_NAME', $_ENV['APP_NAME'] ?? "Lola's Kusina");
define('APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'Asia/Manila');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('APP_CURRENCY', $_ENV['APP_CURRENCY'] ?? 'PHP');

// Set timezone (NFR-T03)
date_default_timezone_set(APP_TIMEZONE);
?>
