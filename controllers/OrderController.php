<?php
/**
 * Order Controller — SRP: handles order placement and viewing only
 * 
 * Cart management has been extracted to CartController.
 * Pricing math has been extracted to OrderTotalCalculator.
 * DIP: All dependencies are injected as interfaces, not concrete classes.
 */
require_once __DIR__ . '/../config/OrderModelInterface.php';
require_once __DIR__ . '/../config/CalculatorInterface.php';
require_once __DIR__ . '/../config/ValidatorInterface.php';
require_once __DIR__ . '/../config/ReferenceGeneratorInterface.php';
require_once __DIR__ . '/../config/DeliveryFeeCalculatorInterface.php';

class OrderController {
    private $orderModel;
    private $calculator;
    private $validator;
    private $referenceGenerator;
    private $deliveryFeeCalculator;
    
    public function __construct(
        OrderModelInterface $orderModel,
        CalculatorInterface $calculator,
        ValidatorInterface $validator,
        ReferenceGeneratorInterface $referenceGenerator,
        DeliveryFeeCalculatorInterface $deliveryFeeCalculator
    ) {
        $this->orderModel = $orderModel;
        $this->calculator = $calculator;
        $this->validator = $validator;
        $this->referenceGenerator = $referenceGenerator;
        $this->deliveryFeeCalculator = $deliveryFeeCalculator;
    }
    
    /** Place a new order from the session cart. */
    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }
        
        $cartItems = $_SESSION['cart'] ?? [];
        
        if (empty($cartItems)) {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }
        
        // Validate required fields
        $errors = $this->validator->validateRequired(
            ['name', 'phone', 'address', 'delivery_datetime', 'payment_method'],
            $_POST
        );
        
        // Validate phone format
        if (empty($errors['phone']) && !$this->validator->validatePhone($_POST['phone'])) {
            $errors['phone'] = 'Please enter a valid Philippine phone number.';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }
        
        // Determine fulfillment type (FR-C05 prep)
        $fulfillmentType = $this->validator->sanitizeString($_POST['fulfillment_type'] ?? 'delivery');
        
        // Calculate delivery fee based on fulfillment type
        $deliveryFee = ($fulfillmentType === 'pickup')
            ? $this->deliveryFeeCalculator->getPickupFee()
            : $this->deliveryFeeCalculator->calculate(null);
        
        $discount = floatval($_POST['discount'] ?? 0);
        
        // SRP: delegate pricing math to the calculator service
        $totals = $this->calculator->calculate($cartItems, $deliveryFee, $discount, $fulfillmentType);
        
        // Generate reference number (FR-C08 prep)
        $referenceNumber = $this->referenceGenerator->generate();
        
        // Prepare order data with sanitized inputs
        $orderData = [
            'customer_name'     => $this->validator->sanitizeString($_POST['name']),
            'phone'             => $this->validator->sanitizeString($_POST['phone']),
            'address'           => $this->validator->sanitizeString($_POST['address']),
            'delivery_datetime' => $this->validator->sanitizeString($_POST['delivery_datetime']),
            'payment_method'    => $this->validator->sanitizeString($_POST['payment_method']),
            'subtotal'          => $totals['subtotal'],
            'delivery_fee'      => $totals['delivery_fee'],
            'discount'          => $totals['discount'],
            'total'             => $totals['total'],
        ];
        
        // Create order
        $orderId = $this->orderModel->createOrder($orderData);
        
        if ($orderId) {
            // Add order items
            $this->orderModel->addOrderItems($orderId, $cartItems);
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Store reference number in session for confirmation page
            $_SESSION['last_reference_number'] = $referenceNumber;
            
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
    public function viewOrder($orderId) {
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order) {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/');
            exit();
        }
        
        $orderItems = $this->orderModel->getOrderItems($orderId);
        include __DIR__ . '/../views/order_confirmation.php';
    }
}
?>
