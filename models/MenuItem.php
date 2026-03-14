<?php
/**
 * MenuItem Model — Data access for menu_items table
 * 
 * DIP: Depends on DatabaseInterface, not the concrete Database class.
 * Dependency is injected via constructor.
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';

class MenuItem {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    /** Get all available menu items ordered by category and name. */
    public function getAllItems() {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("MenuItem::getAllItems failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get available items filtered by category. */
    public function getItemsByCategory($category) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM menu_items WHERE category = ? AND is_available = 1 ORDER BY name"
            );
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("MenuItem::getItemsByCategory failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get a single item by its ID. */
    public function getItemById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (RuntimeException $e) {
            error_log("MenuItem::getItemById failed: " . $e->getMessage());
            return null;
        }
    }
    
    /** Get multiple items by an array of IDs. */
    public function getItemsByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT * FROM menu_items WHERE id IN ($placeholders) AND is_available = 1";
            $stmt = $this->db->prepare($sql);
            
            $types = str_repeat('i', count($ids));
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("MenuItem::getItemsByIds failed: " . $e->getMessage());
            return [];
        }
    }
}
?>
