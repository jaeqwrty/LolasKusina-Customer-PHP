<?php
/**
 * Reverse Geocoding Service
 *
 * Converts latitude/longitude coordinates into human-readable addresses.
 */

class ReverseGeocodingService {
    public function reverseGeocode(float $latitude, float $longitude): array {
        $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query([
            'format' => 'jsonv2',
            'lat' => $latitude,
            'lon' => $longitude,
            'zoom' => 18,
            'addressdetails' => 1,
        ]);

        $responseBody = $this->request($url, 10);
        if ($responseBody === false) {
            return [
                'ok' => false,
                'error' => 'Reverse geocoding request failed.',
            ];
        }

        $payload = json_decode($responseBody, true);
        if (!is_array($payload)) {
            return [
                'ok' => false,
                'error' => 'Invalid reverse geocoding response.',
            ];
        }

        $displayName = trim((string) ($payload['display_name'] ?? ''));
        if ($displayName === '') {
            return [
                'ok' => false,
                'error' => 'Address not found for selected location.',
            ];
        }

        return [
            'ok' => true,
            'display_name' => $displayName,
            'latitude' => $latitude,
            'longitude' => $longitude,
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
                CURLOPT_HTTPHEADER => [
                    'User-Agent: LolasKusina/1.0 (reverse-geocoding)',
                    'Accept: application/json',
                ],
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
                'header' => "User-Agent: LolasKusina/1.0 (reverse-geocoding)\r\nAccept: application/json\r\n",
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
