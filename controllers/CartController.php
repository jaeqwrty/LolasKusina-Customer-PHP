<?php
/**
 * Cart Controller — SRP: manages session-based shopping cart only
 * 
 * Extracted from OrderController so each controller has a single responsibility.
 * DIP: No concrete model dependency needed — cart lives in $_SESSION.
 */

class CartController {
    
    /** Display the cart / checkout page. */
    public function cart() {
        $cartItems = $_SESSION['cart'] ?? [];
        $cartCount = count($cartItems);
        include __DIR__ . '/../views/cart.php';
    }
    
    /** Add an item to the cart. */
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
        
        // Merge with existing item if already in cart
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

    /** Remove an item from the cart by index. */
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
    
    /** Update the quantity of a cart item (AJAX). */
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
    
    /** Clear all items from the cart. */
    public function clearCart() {
        $_SESSION['cart'] = [];
        header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/cart.php');
        exit();
    }
}
?>
