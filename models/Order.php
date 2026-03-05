<?php
// Order Model
require_once __DIR__ . '/../config/database.php';

class Order {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Create a new order
    public function createOrder($data) {
        $stmt = $this->db->prepare("
            INSERT INTO orders (customer_name, phone, address, delivery_datetime, payment_method, subtotal, delivery_fee, discount, total, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        $stmt->bind_param(
            "sssssdddd",
            $data['customer_name'],
            $data['phone'],
            $data['address'],
            $data['delivery_datetime'],
            $data['payment_method'],
            $data['subtotal'],
            $data['delivery_fee'],
            $data['discount'],
            $data['total']
        );
        
        if ($stmt->execute()) {
            return $this->db->getConnection()->insert_id;
        }
        
        return false;
    }
    
    // Add order items
    public function addOrderItems($orderId, $items) {
        $stmt = $this->db->prepare("
            INSERT INTO order_items (order_id, item_type, item_id, item_name, quantity, price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
            $stmt->bind_param(
                "iisiid",
                $orderId,
                $item['item_type'],
                $item['item_id'],
                $item['item_name'],
                $item['quantity'],
                $item['price']
            );
            $stmt->execute();
        }
        
        return true;
    }
    
    // Get order by ID
    public function getOrderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // Get order items
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Get customer orders
    public function getCustomerOrders($customerId) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        
        return $orders;
    }
    
    // Update order status
    public function updateOrderStatus($orderId, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        
        return $stmt->execute();
    }
}
?>
