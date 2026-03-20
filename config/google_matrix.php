<?php
/**
 * Google Matrix config loader.
 *
 * Reads API key from google_matrix.local.php and provides defaults.
 */

if (!function_exists('getGoogleMatrixApiKey')) {
    function getGoogleMatrixApiKey() {
        $localPath = __DIR__ . '/google_matrix.local.php';

        if (!file_exists($localPath)) {
            return '';
        }

        $value = include $localPath;

        if (is_string($value)) {
            return trim($value);
        }

        if (is_array($value) && isset($value['google_matrix_api_key'])) {
            return trim((string) $value['google_matrix_api_key']);
        }

        return '';
    }
}

if (!function_exists('getGoogleMatrixConfig')) {
    function getGoogleMatrixConfig() {
        $defaults = [
            'google_matrix_api_key' => '',
            'distance_matrix_base_url' => 'https://maps.googleapis.com/maps/api/distancematrix/json',
            'store_address' => '7.4471598,125.823198',
            'store_map_url' => 'https://www.google.com/maps?q=7.4471598,125.823198',
            'delivery_fee_rate_per_km' => 12,
            'api_timeout_seconds' => 10,
        ];

        $localPath = __DIR__ . '/google_matrix.local.php';
        if (!file_exists($localPath)) {
            $defaults['google_matrix_api_key'] = getGoogleMatrixApiKey();
            return $defaults;
        }

        $localConfig = include $localPath;
        if (is_array($localConfig)) {
            $merged = array_merge($defaults, $localConfig);
            $merged['google_matrix_api_key'] = trim((string) ($merged['google_matrix_api_key'] ?? ''));
            return $merged;
        }

        $defaults['google_matrix_api_key'] = getGoogleMatrixApiKey();
        return [
            'google_matrix_api_key' => $defaults['google_matrix_api_key'],
            'distance_matrix_base_url' => $defaults['distance_matrix_base_url'],
            'store_address' => $defaults['store_address'],
            'store_map_url' => $defaults['store_map_url'],
            'delivery_fee_rate_per_km' => $defaults['delivery_fee_rate_per_km'],
            'api_timeout_seconds' => $defaults['api_timeout_seconds'],
        ];
    }
}
