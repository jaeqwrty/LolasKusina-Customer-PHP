<?php
/**
 * Package Model — Data access for packages and package_items tables
 * 
 * DIP: Depends on DatabaseInterface, not the concrete Database class.
 * Dependency is injected via constructor.
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';

class Package {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    /** Get all packages ordered by newest first. */
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
    
    /** Get a single package by its ID. */
    public function getPackageById($id) {
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /** Get all items belonging to a package. */
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
    
    /** Get packages filtered by category. */
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
    
    /** Get best-selling packages. */
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
