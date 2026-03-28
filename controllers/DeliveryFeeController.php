<?php
/**
 * Delivery Fee Controller
 *
 * Provides JSON endpoint for distance-based delivery fee.
 */

require_once __DIR__ . '/../services/GoogleMatrixService.php';
require_once __DIR__ . '/../services/ReverseGeocodingService.php';

class DeliveryFeeController {
    private $matrixService;
    private $reverseGeocodingService;

    public function __construct(GoogleMatrixService $matrixService, ?ReverseGeocodingService $reverseGeocodingService = null) {
        $this->matrixService = $matrixService;
        $this->reverseGeocodingService = $reverseGeocodingService ?? new ReverseGeocodingService();
    }

    public function calculate(): void {
        header('Content-Type: application/json; charset=utf-8');

        $destination = trim((string) ($_POST['destination'] ?? $_GET['destination'] ?? ''));
        if ($destination === '') {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Destination address is required.',
            ]);
            return;
        }

        $result = $this->matrixService->calculateDeliveryFee($destination);
        if (!($result['ok'] ?? false)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Unable to calculate delivery fee.',
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'distance_km' => $result['distance_km'],
                'distance_text' => $result['distance_text'],
                'duration_text' => $result['duration_text'],
                'delivery_fee' => $result['delivery_fee'],
                'rate_per_km' => $result['rate_per_km'],
            ],
        ]);
    }

    public function reverseGeocode(): void {
        header('Content-Type: application/json; charset=utf-8');

        $latitude = isset($_POST['lat']) ? (float) $_POST['lat'] : (isset($_GET['lat']) ? (float) $_GET['lat'] : null);
        $longitude = isset($_POST['lng']) ? (float) $_POST['lng'] : (isset($_GET['lng']) ? (float) $_GET['lng'] : null);

        if ($latitude === null || $longitude === null || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Valid latitude and longitude are required.',
            ]);
            return;
        }

        $result = $this->reverseGeocodingService->reverseGeocode($latitude, $longitude);
        if (!($result['ok'] ?? false)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Unable to reverse geocode location.',
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'address' => $result['display_name'],
                'lat' => $result['latitude'],
                'lng' => $result['longitude'],
            ],
        ]);
    }
}
