<?php

/**
 * Order Controller — SRP: handles order placement and viewing only
 * 
 * Cart management has been extracted to CartController.
 * Pricing math has been extracted to OrderTotalCalculator.
 * DIP: Order model and OrderTotalCalculator are injected via constructor.
 */
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../services/OrderTotalCalculator.php';
require_once __DIR__ . '/../services/GoogleMatrixService.php';

class OrderController
{
    private $orderModel;
    private $calculator;

    public function __construct(Order $orderModel, OrderTotalCalculator $calculator)
    {
        $this->orderModel = $orderModel;
        $this->calculator = $calculator;
    }

    /** Place a new order from the session cart. */
    public function placeOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }

        $cartItems = $_SESSION['cart'] ?? [];

        if (empty($cartItems)) {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }

        // SRP: delegate pricing math to services
        $orderMethod = trim((string) ($_POST['order_method'] ?? 'delivery'));
        $deliveryFee = 0;
        if ($orderMethod === 'delivery') {
            $destination = trim((string) ($_POST['address'] ?? ''));
            $matrixService = new GoogleMatrixService();
            $deliveryResult = $matrixService->calculateDeliveryFee($destination);

            if (!($deliveryResult['ok'] ?? false)) {
                $_SESSION['error'] = 'Unable to calculate delivery fee. Please check your address and try again.';
                header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
                exit();
            }

            $deliveryFee = (float) $deliveryResult['delivery_fee'];
        }

        $discount = (float) ($_POST['discount'] ?? 0);
        $totals = $this->calculator->calculate($cartItems, $deliveryFee, $discount);

        // Prepare order data
        $orderData = [
            'customer_name'    => $_POST['name'],
            'phone'            => $_POST['phone'],
            'address'          => $_POST['address'],
            'delivery_datetime' => $_POST['delivery_datetime'],
            'payment_method'   => $_POST['payment_method'],
            'subtotal'         => $totals['subtotal'],
            'delivery_fee'     => $totals['delivery_fee'],
            'discount'         => $totals['discount'],
            'total'            => $totals['total']
        ];

        // Create order
        $orderId = $this->orderModel->createOrder($orderData);

        if ($orderId) {
            // Add order items
            $this->orderModel->addOrderItems($orderId, $cartItems);

            // Clear cart
            $_SESSION['cart'] = [];

            require_once __DIR__ . '/../services/SmsNotificationService.php';

            $sms = new SmsNotificationService(
                new SemaphoreSender(SEMAPHORE_API_KEY, SEMAPHORE_SENDER_NAME),
                new SmsLogger(__DIR__ . '/../storage/logs/sms.log')
            );

            $sms->notify('order_received', [
                'customer_name' => $orderData['customer_name'],
                'order_id'      => $orderId,
                'total'         => $orderData['total'],
                'address'       => $orderData['address'],
                'phone'         => $orderData['phone'],
            ]);

            // Redirect to order confirmation
            header("Location: " . (defined('BASE_PATH') ? BASE_PATH : '') . "/order_confirmation.php?order_id=" . $orderId);
            exit();
        } else {
            $_SESSION['error'] = 'Failed to place order. Please try again.';
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }
    }

    /** View order details by ID. */
    public function viewOrder($orderId)
    {
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order) {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/');
            exit();
        }

        $orderItems = $this->orderModel->getOrderItems($orderId);
        include __DIR__ . '/../views/order_confirmation.php';
    }
}
