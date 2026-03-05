<?php
// Menu Item Model
require_once __DIR__ . '/../config/database.php';

class MenuItem {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all menu items
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
    
    // Get items by category
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
    
    // Get item by ID
    public function getItemById($id) {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // Get items by multiple IDs
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
