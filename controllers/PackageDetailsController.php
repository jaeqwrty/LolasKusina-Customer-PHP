<?php
/**
 * Package Details Controller — SRP: handles package detail page data loading
 * 
 * Extracted from package_details.php view so the view contains only presentation.
 * DIP: Depends on Package model (which depends on DatabaseInterface).
 */
require_once __DIR__ . '/../models/Package.php';

class PackageDetailsController {
    private $packageModel;
    
    public function __construct(Package $packageModel) {
        $this->packageModel = $packageModel;
    }
    
    /**
     * Load package data and display the details view.
     *
     * @param int $packageId Package ID from the URL
     */
    public function show(int $packageId) {
        $package = null;
        $items = [];
        
        if ($packageId > 0) {
            try {
                $package = $this->packageModel->getPackageById($packageId);
                if ($package) {
                    $items = $this->packageModel->getPackageItems($packageId);
                }
            } catch (Exception $e) {
                // DB not available — fall through to static data
            }
        }
        
        // Static fallback packages (mirrors what index.php shows)
        $staticPackages = [
            1 => [
                'id'          => 1,
                'name'        => 'Paborito Package',
                'description' => 'Good for 6-7 pax',
                'details'     => 'Includes Lechon Kawali, Pata, Pancit Canton, Steamed Rice, and Buko Pandan.',
                'price'       => 2500,
                'image'       => 'paborito-package.jpg',
                'rating'      => 4.8,
                'reviews'     => 20,
                'badge'       => 'Best Seller',
                'category'    => 'fiesta',
            ],
            2 => [
                'id'          => 2,
                'name'        => 'Family Fiesta',
                'description' => 'Good for 10-12 pax',
                'details'     => 'The ultimate gathering set! Chicken Inasal, Kare-Kare, Lumpia Shanghai, Garlic Rice, and Halo-halo.',
                'price'       => 4200,
                'image'       => 'family-fiesta.jpg',
                'rating'      => 4.9,
                'reviews'     => 35,
                'badge'       => 'Popular',
                'category'    => 'fiesta',
            ],
            3 => [
                'id'          => 3,
                'name'        => 'Salo-Salo Special',
                'description' => 'Good for 15-20 pax',
                'details'     => 'Perfect for big celebrations! Crispy Pata, Beef Caldereta, Seafood Pancit, Java Rice, Fruit Salad.',
                'price'       => 6500,
                'image'       => 'salo-salo.jpg',
                'rating'      => 4.7,
                'reviews'     => 18,
                'badge'       => 'New',
                'category'    => 'fiesta',
            ],
        ];
        
        $staticItems = [
            1 => [
                ['name' => 'Lechon Kawali',     'description' => 'Crispy Fried Pork Belly',  'image' => 'lechon-kawali.jpg'],
                ['name' => 'Crispy Pata',       'description' => 'Deep Fried Pork Leg',       'image' => 'pata.jpg'],
                ['name' => 'Pancit Canton',     'description' => 'Stir-Fried Yellow Noodles', 'image' => 'pancit-canton.jpg'],
                ['name' => 'Steamed Rice',      'description' => 'Good for 6-7 pax',          'image' => 'rice.jpg'],
                ['name' => 'Buko Pandan',       'description' => 'Creamy Coconut Dessert',    'image' => 'buko-pandan.jpg'],
            ],
            2 => [
                ['name' => 'Chicken Inasal',    'description' => 'Grilled Marinated Chicken', 'image' => 'chicken-inasal.jpg'],
                ['name' => 'Kare-Kare',         'description' => 'Oxtail in Peanut Sauce',    'image' => 'kare-kare.jpg'],
                ['name' => 'Lumpiang Shanghai', 'description' => 'Mini Spring Rolls',          'image' => 'lumpia.jpg'],
                ['name' => 'Garlic Rice',       'description' => 'Good for 10-12 pax',         'image' => 'rice.jpg'],
                ['name' => 'Halo-Halo',         'description' => 'Classic Filipino Dessert',   'image' => 'halo-halo.jpg'],
            ],
            3 => [
                ['name' => 'Crispy Pata',       'description' => 'Deep Fried Pork Leg',        'image' => 'pata.jpg'],
                ['name' => 'Beef Caldereta',    'description' => 'Beef in Tomato Sauce',        'image' => 'caldereta.jpg'],
                ['name' => 'Seafood Pancit',    'description' => 'Noodles with Mixed Seafood',  'image' => 'pancit-canton.jpg'],
                ['name' => 'Java Rice',         'description' => 'Good for 15-20 pax',          'image' => 'rice.jpg'],
                ['name' => 'Fruit Salad',       'description' => 'Mixed Fruits in Cream',       'image' => 'buko-pandan.jpg'],
            ],
        ];
        
        // Use static data if DB returned nothing
        if (!$package) {
            $package = $staticPackages[$packageId] ?? $staticPackages[1];
            $items   = $staticItems[$package['id']] ?? $staticItems[1];
        }
        
        // If DB items are empty but package exists, use static items
        if (empty($items) && isset($staticItems[$package['id'] ?? 0])) {
            $items = $staticItems[$package['id']];
        }
        
        $pageTitle   = htmlspecialchars($package['name']) . " - Lola's Kusina";
        $currentPage = "packages";
        
        include __DIR__ . '/../views/package_details.php';
    }
}
?>
