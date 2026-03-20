<?php
/**
 * Google Matrix Service
 *
 * Encapsulates Distance Matrix API calls and delivery-fee calculation.
 */

require_once __DIR__ . '/../config/google_matrix.php';

class GoogleMatrixService {
    private $config;

    public function __construct(?array $config = null) {
        $this->config = $config ?? getGoogleMatrixConfig();
    }

    public function calculateDeliveryFee(string $destinationAddress): array {
        $destinationAddress = trim($destinationAddress);
        if ($destinationAddress === '') {
            return [
                'ok' => false,
                'error' => 'Destination address is required.',
            ];
        }

        $origin = (string) ($this->config['store_address'] ?? '');
        $apiKey = (string) ($this->config['google_matrix_api_key'] ?? '');
        $baseUrl = (string) ($this->config['distance_matrix_base_url'] ?? '');
        $ratePerKm = (float) ($this->config['delivery_fee_rate_per_km'] ?? 12);
        $timeout = (int) ($this->config['api_timeout_seconds'] ?? 10);

        if ($origin === '' || $apiKey === '' || $baseUrl === '') {
            return [
                'ok' => false,
                'error' => 'Google Matrix API is not configured.',
            ];
        }

        $query = http_build_query([
            'origins' => $origin,
            'destinations' => $destinationAddress,
            'key' => $apiKey,
            'units' => 'metric',
        ]);

        $url = $baseUrl . '?' . $query;
        $responseBody = $this->request($url, $timeout);
        if ($responseBody === false) {
            return [
                'ok' => false,
                'error' => 'Could not contact Google Matrix API.',
            ];
        }

        $payload = json_decode($responseBody, true);
        if (!is_array($payload) || ($payload['status'] ?? '') !== 'OK') {
            return [
                'ok' => false,
                'error' => 'Google Matrix API returned an invalid response.',
            ];
        }

        $element = $payload['rows'][0]['elements'][0] ?? null;
        if (!is_array($element) || ($element['status'] ?? '') !== 'OK') {
            return [
                'ok' => false,
                'error' => 'No route found for the provided destination.',
            ];
        }

        $distanceMeters = (float) ($element['distance']['value'] ?? 0);
        $distanceKm = $distanceMeters / 1000;
        $fee = round($distanceKm * $ratePerKm, 2);

        return [
            'ok' => true,
            'origin' => $origin,
            'destination' => $destinationAddress,
            'distance_km' => round($distanceKm, 2),
            'distance_text' => (string) ($element['distance']['text'] ?? ''),
            'duration_text' => (string) ($element['duration']['text'] ?? ''),
            'delivery_fee' => $fee,
            'rate_per_km' => $ratePerKm,
        ];
    }

    private function request(string $url, int $timeoutSeconds) {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeoutSeconds,
                CURLOPT_CONNECTTIMEOUT => $timeoutSeconds,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $result = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($result === false || $httpCode >= 400) {
                return false;
            }
            return $result;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);

        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        return $result;
    }
}
