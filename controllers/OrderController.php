<?php
// Order Controller
require_once __DIR__ . '/../models/Order.php';
session_start();

class OrderController {
    private $orderModel;
    
    public function __construct() {
        $this->orderModel = new Order();
    }
    
    // Display cart page
    public function cart() {
        $cartItems = $_SESSION['cart'] ?? [];
        $cartCount = count($cartItems);
        include __DIR__ . '/../views/cart.php';
    }
    
    // Add item to cart
    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/');
            exit();
        }
        
        $itemType = $_POST['item_type'] ?? '';
        $itemId = $_POST['item_id'] ?? 0;
        $itemName = $_POST['item_name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $cartItem = [
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_name' => $itemName,
            'price' => $price,
            'quantity' => $quantity
        ];
        
        // Check if item already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['item_id'] == $itemId && $item['item_type'] == $itemType) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = $cartItem;
        }
        
        header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
        exit();
    }

    // Remove item from cart
    public function removeFromCart() {
        if (!isset($_GET['index'])) {
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }

        $index = (int)$_GET['index'];

        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
        }

        header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
        exit();
    }
    
    // Update cart quantity
    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $index = $data['index'] ?? -1;
        $quantity = $data['quantity'] ?? 1;
        
        if ($index >= 0 && isset($_SESSION['cart'][$index])) {
            $_SESSION['cart'][$index]['quantity'] = max(1, $quantity);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Item not found']);
        }
    }
    
    // Clear cart
    public function clearCart() {
        $_SESSION['cart'] = [];
        header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
        exit();
    }
    
    // Place order
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
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $deliveryFee = 50;
        $discount = $_POST['discount'] ?? 0;
        $total = $subtotal + $deliveryFee - $discount;
        
        // Prepare order data
        $orderData = [
            'customer_name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'delivery_datetime' => $_POST['delivery_datetime'],
            'payment_method' => $_POST['payment_method'],
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount' => $discount,
            'total' => $total
        ];
        
        // Create order
        $orderId = $this->orderModel->createOrder($orderData);
        
        if ($orderId) {
            // Add order items
            $this->orderModel->addOrderItems($orderId, $cartItems);
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Redirect to order confirmation
            header("Location: " . (defined('BASE_PATH') ? BASE_PATH : '') . "/order_confirmation.php?order_id=" . $orderId);
            exit();
        } else {
            $_SESSION['error'] = 'Failed to place order. Please try again.';
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
            exit();
        }
    }
    
    // View order details
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
