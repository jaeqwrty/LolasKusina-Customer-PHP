<?php
/**
 * Order Model — Data access for orders and order_items tables
 * 
 * DIP: Depends on DatabaseInterface, not the concrete Database class.
 * Dependency is injected via constructor.
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';

class Order {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    /** Create a new order and return its ID, or false on failure. */
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
    
    /** Add line items to an existing order. */
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
    
    /** Get a single order by its ID. */
    public function getOrderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /** Get all line items for an order. */
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
    
    /** Get all orders for a given customer. */
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
    
    /** Update the status of an order. */
    public function updateOrderStatus($orderId, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        
        return $stmt->execute();
    }
}
?>
