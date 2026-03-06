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
    
    /** Get all menu items ordered by category and name. */
    public function getAllItems() {
        $sql = "SELECT * FROM menu_items ORDER BY category, name";
        $result = $this->db->query($sql);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    /** Get items filtered by category. */
    public function getItemsByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE category = ? ORDER BY name");
        $stmt->bind_param("s", $category);
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
    
    /** Get a single item by its ID. */
    public function getItemById($id) {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /** Get multiple items by an array of IDs. */
    public function getItemsByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM menu_items WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
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
}
?>
