<?php
/**
 * User Model — SRP: Data access for the users table
 * 
 * DIP: Depends on DatabaseInterface, not the concrete Database class.
 * Extracted from login.php, register.php, profile.php views so they
 * contain only presentation logic.
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';

class User {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    /**
     * Find an active user by phone number.
     *
     * @param string $phone Phone number to search
     * @return array|null   User record or null if not found
     */
    public function findByPhone(string $phone): ?array {
        $result = $this->db->execute(
            "SELECT user_id, first_name, last_name, password_hash FROM users WHERE phone_number = ? AND is_active = 1",
            [$phone]
        );
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Find an active user by ID.
     *
     * @param int $userId User ID
     * @return array|null User record or null
     */
    public function findById(int $userId): ?array {
        $result = $this->db->execute(
            "SELECT user_id, first_name, last_name, email, phone_number FROM users WHERE user_id = ? AND is_active = 1",
            [$userId]
        );
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Create a new user account.
     *
     * @param string $firstName    First name
     * @param string $lastName     Last name
     * @param string $phone        Phone number
     * @param string $passwordHash Bcrypt-hashed password
     * @return array|null          Created user record or null on failure
     */
    public function create(string $firstName, string $lastName, string $phone, string $passwordHash): ?array {
        $this->db->execute(
            "INSERT INTO users (role, first_name, last_name, phone_number, password_hash, is_active, created_at) 
             VALUES (?, ?, ?, ?, ?, 1, NOW())",
            ['customer', $firstName, $lastName, $phone, $passwordHash]
        );
        
        // Fetch the newly created user
        return $this->findByPhone($phone);
    }
    
    /**
     * Update a user's profile information.
     *
     * @param int    $userId   User ID
     * @param string $firstName First name
     * @param string $lastName  Last name
     * @param string $phone     Phone number
     * @param string|null $email Email address (nullable)
     * @return bool
     */
    public function updateProfile(int $userId, string $firstName, string $lastName, string $phone, ?string $email): bool {
        return (bool) $this->db->execute(
            "UPDATE users SET first_name = ?, last_name = ?, phone_number = ?, email = ?, updated_at = NOW() WHERE user_id = ?",
            [$firstName, $lastName, $phone, $email, $userId]
        );
    }
    
    /**
     * Update the last login timestamp.
     *
     * @param int $userId User ID
     * @return bool
     */
    public function updateLastLogin(int $userId): bool {
        return (bool) $this->db->execute(
            "UPDATE users SET last_login_at = NOW() WHERE user_id = ?",
            [$userId]
        );
    }
    
    /**
     * Check if a phone number is already registered (optionally excluding a user).
     *
     * @param string   $phone         Phone number to check
     * @param int|null $excludeUserId User ID to exclude from the check
     * @return bool                   True if phone is already taken
     */
    public function isPhoneTaken(string $phone, ?int $excludeUserId = null): bool {
        if ($excludeUserId !== null) {
            $result = $this->db->execute(
                "SELECT user_id FROM users WHERE phone_number = ? AND user_id != ?",
                [$phone, $excludeUserId]
            );
        } else {
            $result = $this->db->execute(
                "SELECT user_id FROM users WHERE phone_number = ?",
                [$phone]
            );
        }
        return !empty($result);
    }
    
    /**
     * Get all orders for a customer with payment and item details.
     *
     * @param int $userId Customer user ID
     * @return array      List of order records
     */
    public function getCustomerOrders(int $userId): array {
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
            [$userId]
        );
        return is_array($result) ? $result : [];
    }
}
?>
