<?php
// Package Controller
require_once __DIR__ . '/../models/Package.php';

class PackageController {
    private $packageModel;
    
    public function __construct() {
        $this->packageModel = new Package();
    }
    
    // Display all packages
    public function index() {
        $packages = $this->packageModel->getAllPackages();
        include __DIR__ . '/../views/index.php';
    }
    
    // Display package details
    public function show($id) {
        $package = $this->packageModel->getPackageById($id);
        
        if (!$package) {
            header("Location: /");
            exit();
        }
        
        $items = $this->packageModel->getPackageItems($id);
        include __DIR__ . '/../views/order_details.php';
    }
    
    // Display packages by category
    public function category($category) {
        $packages = $this->packageModel->getPackagesByCategory($category);
        include __DIR__ . '/../views/index.php';
    }
    
    // Display best sellers
    public function bestSellers() {
        $packages = $this->packageModel->getBestSellers();
        include __DIR__ . '/../views/index.php';
    }
}
?>
