<?php
/**
 * Package Model — Data access for packages and package_items tables
 * 
 * DIP: Depends on DatabaseInterface, implements PackageModelInterface.
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';
require_once __DIR__ . '/../config/PackageModelInterface.php';

class Package implements PackageModelInterface {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    /** Get all active packages ordered by newest first. */
    public function getAllPackages() {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Package::getAllPackages failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get active featured/bestseller packages for the homepage (FR-C01). */
    public function getFeaturedPackages($limit = 6) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM packages WHERE is_active = 1 AND is_bestseller = 1 
                 ORDER BY sales_count DESC LIMIT ?"
            );
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Package::getFeaturedPackages failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get a single package by its ID. */
    public function getPackageById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (RuntimeException $e) {
            error_log("Package::getPackageById failed: " . $e->getMessage());
            return null;
        }
    }
    
    /** Get all items belonging to a package. */
    public function getPackageItems($packageId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM package_items WHERE package_id = ?");
            $stmt->bind_param("i", $packageId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Package::getPackageItems failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get active packages filtered by category. */
    public function getPackagesByCategory($category) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM packages WHERE category = ? AND is_active = 1 ORDER BY created_at DESC"
            );
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Package::getPackagesByCategory failed: " . $e->getMessage());
            return [];
        }
    }
    
    /** Get best-selling packages. */
    public function getBestSellers($limit = 3) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM packages WHERE is_bestseller = 1 AND is_active = 1 
                 ORDER BY sales_count DESC LIMIT ?"
            );
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (RuntimeException $e) {
            error_log("Package::getBestSellers failed: " . $e->getMessage());
            return [];
        }
    }
}
?>
