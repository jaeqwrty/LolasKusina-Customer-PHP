<?php
// Menu Controller
require_once __DIR__ . '/../models/MenuItem.php';

class MenuController {
    private $menuItemModel;
    
    public function __construct() {
        $this->menuItemModel = new MenuItem();
    }
    
    // Display build package page
    public function buildPackage() {
        // Get menu items by category for custom package building
        $mainDishes = $this->menuItemModel->getItemsByCategory('main_dish');
        $sideDishes = $this->menuItemModel->getItemsByCategory('side_dish');
        $desserts = $this->menuItemModel->getItemsByCategory('dessert');
        
        include __DIR__ . '/../views/build_package.php';
    }
    
    // Get items by category (for AJAX requests)
    public function getItemsByCategory() {
        if (!isset($_GET['category'])) {
            echo json_encode(['error' => 'Category not specified']);
            return;
        }
        
        $category = $_GET['category'];
        $items = $this->menuItemModel->getItemsByCategory($category);
        
        header('Content-Type: application/json');
        echo json_encode($items);
    }
    
    // Calculate custom package price
    public function calculatePrice() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $itemIds = $data['items'] ?? [];
        
        if (empty($itemIds)) {
            echo json_encode(['total' => 0]);
            return;
        }
        
        $items = $this->menuItemModel->getItemsByIds($itemIds);
        $total = array_sum(array_column($items, 'price'));
        
        header('Content-Type: application/json');
        echo json_encode([
            'total' => $total,
            'items' => $items
        ]);
    }
}
?>
