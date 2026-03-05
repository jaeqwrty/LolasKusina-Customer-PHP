<?php
// Package Model
require_once __DIR__ . '/../config/database.php';

class Package {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all packages
    public function getAllPackages() {
        $sql = "SELECT * FROM packages ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        
        $packages = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $packages[] = $row;
            }
        }
        
        return $packages;
    }
    
    // Get package by ID
    public function getPackageById($id) {
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // Get package items
    public function getPackageItems($packageId) {
        $stmt = $this->db->prepare("SELECT * FROM package_items WHERE package_id = ?");
        $stmt->bind_param("i", $packageId);
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
    
    // Get packages by category
    public function getPackagesByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE category = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $packages = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $packages[] = $row;
            }
        }
        
        return $packages;
    }
    
    // Get best sellers
    public function getBestSellers($limit = 3) {
        $sql = "SELECT * FROM packages WHERE is_bestseller = 1 ORDER BY sales_count DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $packages = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $packages[] = $row;
            }
        }
        
        return $packages;
    }
}
?>
