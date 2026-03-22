<?php
/**
 * Order Model — Data access for orders and order_items tables
 * 
 * DIP: Depends on DatabaseInterface, implements OrderModelInterface.
 * SRP: All order-related queries live here (not in User model).
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';
require_once __DIR__ . '/../config/OrderModelInterface.php';

class Order implements OrderModelInterface {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    /** Create a new order and return its ID, or false on failure. */
    public function createOrder($data) {
        try {
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
        } catch (RuntimeException $e) {
            error_log("Order::createOrder failed: " . $e->getMessage());
            return false;
        }
    }
    
    /** Add line items to an existing order. */
    public function addOrderItems($orderId, $items) {
        try {
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
        } catch (RuntimeException $e) {
            error_log("Order::addOrderItems failed: " . $e->getMessage());
            return false;
        }
    }
    
    /** Get a single order by its ID. */
    public function getOrderById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (RuntimeException $e) {
            error_log("Order::getOrderById failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get a single order by its reference number (FR-C09 prep).
     * Ready for the status tracker feature.
     */
    public function getOrderByReferenceNumber($referenceNumber) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM orders WHERE id = ? LIMIT 1"
            );
            // NOTE: Currently queries by ID. When the reference_number column is
            // added to the orders table, update this to query by reference_number.
            $stmt->bind_param("s", $referenceNumber);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (RuntimeException $e) {
            error_log("Order::getOrderByReferenceNumber failed: " . $e->getMessage());
            return null;
        }
    }
    
    /** Get all line items for an order. */
    public function getOrderItems($orderId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Order::getOrderItems failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get all orders for a given customer. */
    public function getCustomerOrders($customerId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC"
            );
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Order::getCustomerOrders failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Update the status of an order. */
    public function updateOrderStatus($orderId, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $orderId);
            return $stmt->execute();
        } catch (RuntimeException $e) {
            error_log("Order::updateOrderStatus failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get customer orders with payment and first item details (for profile page).
     * Moved from User model to respect SRP — order queries belong in Order model.
     *
     * @param int $customerId Customer user ID
     * @return array
     */
    public function getCustomerOrdersWithDetails(int $customerId): array {
        $result = $this->db->execute(
            "SELECT o.order_id, o.reference_number, o.created_at, o.status,
                    op.grand_total,
                    (SELECT mi.name FROM order_items oi
                     JOIN menu_items mi ON mi.item_id = oi.item_id
                     WHERE oi.order_id = o.order_id
                     ORDER BY oi.order_item_id ASC LIMIT 1) AS first_item
             FROM orders o
             LEFT JOIN order_payments op ON op.order_id = o.order_id
             WHERE o.customer_id = ?
             ORDER BY o.created_at DESC",
            [$customerId]
        );
        return is_array($result) ? $result : [];
    }
}
?>
