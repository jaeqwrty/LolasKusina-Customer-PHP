<?php
/**
 * T012 - SMS Notification Service
 * Lola's Kusina - Customer Web Module
 * Author: Jullian Anjelo C. Vidal
 *
 * Integrates with Semaphore API to send SMS notifications for:
 *  - Order received/confirmed
 *  - Order out for delivery
 *  - Order delivered
 *  - Order cancelled
 *
 * SOLID Principles Applied:
 *  S - Single Responsibility : Each class does ONE thing only
 *  O - Open/Closed           : Add new message templates without modifying existing ones
 *  L - Liskov Substitution   : Any IMessageTemplate can replace another
 *  I - Interface Segregation : Small focused classes (sender, template, logger)
 *  D - Dependency Inversion  : SmsNotificationService depends on abstractions
 */

// =============================================================================
// SemaphoreSender
// Responsibility: ONLY sends HTTP requests to the Semaphore API
// =============================================================================
class SemaphoreSender {
    private string $apiKey;
    private string $senderName;
    private string $apiUrl = 'https://api.semaphore.co/api/v4/messages';

    public function __construct(string $apiKey, string $senderName = 'LolasKusina') {
        $this->apiKey     = $apiKey;
        $this->senderName = $senderName;
    }

    /**
     * Send an SMS message.
     *
     * @param  string $phone    Recipient phone number (e.g. 09171234567)
     * @param  string $message  SMS message body
     * @return array            ['success' => bool, 'response' => array, 'error' => string|null]
     */
    public function send(string $phone, string $message): array {
        $phone = $this->_formatPhone($phone);

        $payload = [
            'apikey'      => $this->apiKey,
            'number'      => $phone,
            'message'     => $message,
            'sendername'  => $this->senderName,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->apiUrl,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'response' => [], 'error' => $error];
        }

        $decoded = json_decode($response, true) ?? [];
        $success = $httpCode === 200 && !isset($decoded['error']);

        return [
            'success'  => $success,
            'response' => $decoded,
            'error'    => $success ? null : ($decoded['error'] ?? 'Unknown error'),
        ];
    }

    /** Normalize phone number to Semaphore format (e.g. 09171234567 → 639171234567) */
    private function _formatPhone(string $phone): string {
        $phone = preg_replace('/\D/', '', $phone); // strip non-digits
        if (str_starts_with($phone, '0')) {
            $phone = '63' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '63')) {
            $phone = '63' . $phone;
        }
        return $phone;
    }
}


// =============================================================================
// Message Templates
// Responsibility: ONLY define the SMS message for a specific event.
// Open/Closed   : Add new templates (e.g. DownpaymentReceivedTemplate)
//                 without modifying existing ones.
// =============================================================================

class OrderReceivedTemplate {
    public function build(array $order): string {
        return sprintf(
            "Kumusta %s! Natanggap na namin ang inyong order #%s mula sa Lola's Kusina. " .
            "Kabuuang halaga: P%.2f. Abangan ang aming update. Salamat!",
            $order['customer_name'],
            $order['order_id'],
            $order['total']
        );
    }
}

class OrderOutForDeliveryTemplate {
    public function build(array $order): string {
        return sprintf(
            "Lola's Kusina: Ang inyong order #%s ay naglalakbay na papunta sa inyo! " .
            "Delivery address: %s. Mangyaring maging handa. Salamat, %s!",
            $order['order_id'],
            $order['address'],
            $order['customer_name']
        );
    }
}

class OrderDeliveredTemplate {
    public function build(array $order): string {
        return sprintf(
            "Lola's Kusina: Naihatid na ang inyong order #%s. " .
            "Salamat sa inyong tiwala, %s! Enjoy your meal. Ulit-ulitin! :)",
            $order['order_id'],
            $order['customer_name']
        );
    }
}

class OrderCancelledTemplate {
    public function build(array $order): string {
        return sprintf(
            "Lola's Kusina: Paumanhin, %s. Ang inyong order #%s ay na-cancel. " .
            "Para sa katanungan, makipag-ugnayan sa amin. Pasensya na po!",
            $order['customer_name'],
            $order['order_id']
        );
    }
}


// =============================================================================
// SmsLogger
// Responsibility: ONLY logs SMS send attempts to a file
// =============================================================================
class SmsLogger {
    private string $logFile;

    public function __construct(string $logFile) {
        $this->logFile = $logFile;
    }

    public function log(string $phone, string $event, bool $success, ?string $error = null): void {
        $entry = sprintf(
            "[%s] Event: %-25s | Phone: %s | Status: %s%s\n",
            date('Y-m-d H:i:s'),
            $event,
            $phone,
            $success ? 'SENT' : 'FAILED',
            $error ? " | Error: $error" : ''
        );

        // Ensure log directory exists
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}


// =============================================================================
// SmsNotificationService (Orchestrator)
// Responsibility: Coordinates sender, templates, and logger
// Dependency Inversion: All dependencies are injected
// =============================================================================
class SmsNotificationService {
    private SemaphoreSender $sender;
    private SmsLogger       $logger;

    // Template map — O: just add a new entry here for new events
    private array $templates = [];

    public function __construct(SemaphoreSender $sender, SmsLogger $logger) {
        $this->sender = $sender;
        $this->logger = $logger;

        // Register all event templates
        $this->templates = [
            'order_received'       => new OrderReceivedTemplate(),
            'order_out_for_delivery' => new OrderOutForDeliveryTemplate(),
            'order_delivered'      => new OrderDeliveredTemplate(),
            'order_cancelled'      => new OrderCancelledTemplate(),
        ];
    }

    /**
     * Send an SMS notification for a specific order event.
     *
     * @param  string $event  One of: order_received, order_out_for_delivery,
     *                                order_delivered, order_cancelled
     * @param  array  $order  Order data: customer_name, order_id, total, address, phone
     * @return array          ['success' => bool, 'error' => string|null]
     */
    public function notify(string $event, array $order): array {
        // Check if template exists for this event
        if (!isset($this->templates[$event])) {
            return ['success' => false, 'error' => "Unknown event: $event"];
        }

        // Build message from template
        $message = $this->templates[$event]->build($order);

        // Send SMS
        $result = $this->sender->send($order['phone'], $message);

        // Log the attempt
        $this->logger->log($order['phone'], $event, $result['success'], $result['error']);

        return [
            'success' => $result['success'],
            'error'   => $result['error'],
        ];
    }
}


// =============================================================================
// ENTRY POINT — How to use in your controllers
//
// 1. In config/app.php, define:
//    define('SEMAPHORE_API_KEY',    'your_api_key_here');
//    define('SEMAPHORE_SENDER_NAME','LolasKusina');
//
// 2. require_once __DIR__ . '/../services/SmsNotificationService.php';
//
// 3. Wire up dependencies:
//    $sms = new SmsNotificationService(
//        new SemaphoreSender(SEMAPHORE_API_KEY, SEMAPHORE_SENDER_NAME),
//        new SmsLogger(__DIR__ . '/../storage/logs/sms.log')
//    );
//
// 4. Fire notifications:
//
//    // When order is placed (in OrderController::placeOrder)
//    $sms->notify('order_received', [
//        'customer_name' => $orderData['customer_name'],
//        'order_id'      => $orderId,
//        'total'         => $orderData['total'],
//        'address'       => $orderData['address'],
//        'phone'         => $orderData['phone'],
//    ]);
//
//    // When admin marks order as out for delivery
//    $sms->notify('order_out_for_delivery', [...]);
//
//    // When order is delivered
//    $sms->notify('order_delivered', [...]);
//
//    // When order is cancelled
//    $sms->notify('order_cancelled', [...]);
// =============================================================================