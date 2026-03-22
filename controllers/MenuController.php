<?php
/**
 * Menu Controller — Handles menu browsing and custom package building
 * 
 * DIP: Depends on MenuItemModelInterface, not concrete MenuItem class.
 */
require_once __DIR__ . '/../config/MenuItemModelInterface.php';

class MenuController {
    private $menuItemModel;
    
    public function __construct(MenuItemModelInterface $menuItemModel) {
        $this->menuItemModel = $menuItemModel;
    }
    
    /** Display the build-your-own-package page. */
    public function buildPackage() {
        $mainDishes = $this->menuItemModel->getItemsByCategory('main_dish');
        $sideDishes = $this->menuItemModel->getItemsByCategory('side_dish');
        $desserts = $this->menuItemModel->getItemsByCategory('dessert');
        
        include __DIR__ . '/../views/build_package.php';
    }
    
    /** Return items by category as JSON (for AJAX requests). */
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
    
    /** Calculate custom package price from selected items (AJAX). */
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
