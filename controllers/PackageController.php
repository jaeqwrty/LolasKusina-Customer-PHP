<?php
/**
 * Package Controller — Handles package listing and detail views
 * 
 * DIP: Package model is injected via constructor, not created internally.
 */
require_once __DIR__ . '/../models/Package.php';

class PackageController {
    private $packageModel;
    
    public function __construct(Package $packageModel) {
        $this->packageModel = $packageModel;
    }
    
    /** Display all packages. */
    public function index() {
        $packages = $this->packageModel->getAllPackages();
        include __DIR__ . '/../views/home.php';
    }
    
    /** Display a single package's details. */
    public function show($id) {
        $package = $this->packageModel->getPackageById($id);
        
        if (!$package) {
            header("Location: /");
            exit();
        }
        
        $items = $this->packageModel->getPackageItems($id);
        include __DIR__ . '/../views/order_details.php';
    }
    
    /** Display packages filtered by category. */
    public function category($category) {
        $packages = $this->packageModel->getPackagesByCategory($category);
        include __DIR__ . '/../views/home.php';
    }
    
    /** Display best-selling packages. */
    public function bestSellers() {
        $packages = $this->packageModel->getBestSellers();
        include __DIR__ . '/../views/home.php';
    }
}
?>
