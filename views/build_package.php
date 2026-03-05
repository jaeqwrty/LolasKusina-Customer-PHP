<?php
// Build Your Own Package View
$pageTitle = "Build Your Own Package";
$currentPage = "packages";
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-4xl mb-20 md:mb-8">
    <!-- Back Button -->
    <div class="flex items-center justify-between mb-6">
        <a href="/" class="bg-white rounded-full p-2 shadow-md">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Build Your Package</h1>
        <div class="w-10"></div>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold">1</div>
                <span class="text-xs text-gray-600 mt-1">Main Dish</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">2</div>
                <span class="text-xs text-gray-600 mt-1">Side Dish</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">3</div>
                <span class="text-xs text-gray-600 mt-1">Dessert</span>
            </div>
        </div>
    </div>

    <!-- Package Size Selection -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Select Package Size</h2>
        <div class="grid grid-cols-3 gap-3">
            <button class="border-2 border-primary bg-orange-50 text-primary py-3 rounded-lg font-semibold">
                6-8 pax
            </button>
            <button class="border-2 border-gray-300 text-gray-600 py-3 rounded-lg font-semibold hover:border-primary">
                10-12 pax
            </button>
            <button class="border-2 border-gray-300 text-gray-600 py-3 rounded-lg font-semibold hover:border-primary">
                15-20 pax
            </button>
        </div>
    </div>

    <!-- Main Dishes Selection -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Choose Main Dishes</h2>
            <span class="text-sm text-gray-500">Select up to 3</span>
        </div>

        <?php
        $mainDishes = [
            ['name' => 'Lechon Kawali', 'description' => 'Crispy Fried Pork Belly', 'price' => 450, 'image' => 'lechon-kawali.jpg', 'selected' => true],
            ['name' => 'Chicken Inasal', 'description' => 'Grilled Marinated Chicken', 'price' => 380, 'image' => 'chicken-inasal.jpg', 'selected' => true],
            ['name' => 'Kare-Kare', 'description' => 'Oxtail in Peanut Sauce', 'price' => 520, 'image' => 'kare-kare.jpg', 'selected' => false],
            ['name' => 'Pata (Crispy)', 'description' => 'Deep Fried Pork Leg', 'price' => 680, 'image' => 'pata.jpg', 'selected' => false],
            ['name' => 'Beef Caldereta', 'description' => 'Beef in Tomato Sauce', 'price' => 480, 'image' => 'caldereta.jpg', 'selected' => false]
        ];

        foreach ($mainDishes as $index => $dish):
        ?>
        <div class="menu-item flex items-center justify-between py-3 border-b border-gray-100 last:border-0" data-price="<?php echo $dish['price']; ?>" data-name="<?php echo $dish['name']; ?>">
            <div class="flex items-center space-x-3 flex-1">
                <div class="w-16 h-16 rounded-lg overflow-hidden shrink-0 bg-[#FFF3EE]">
                    <img src="<?php echo BASE_PATH; ?>/images/<?php echo $dish['image']; ?>" alt="<?php echo $dish['name']; ?>" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='<?php echo BASE_PATH; ?>/images/placeholder.svg'">
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800"><?php echo $dish['name']; ?></h3>
                    <p class="text-xs text-gray-500"><?php echo $dish['description']; ?></p>
                    <p class="text-sm font-bold text-primary mt-1">+₱<?php echo number_format($dish['price'], 2); ?></p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="dish-checkbox sr-only peer" <?php echo $dish['selected'] ? 'checked' : ''; ?> onchange="updateSelection()">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl shadow-md p-4 mb-6 border-2 border-orange-200">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-700 font-semibold">Selected Items:</span>
            <span id="itemCount" class="text-primary font-bold">2 items</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-700 font-semibold">Current Total:</span>
            <span id="totalPrice" class="text-2xl font-bold text-primary">₱830.00</span>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex space-x-3">
        <a href="<?php echo BASE_PATH ?: '/'; ?>" class="flex-1 bg-gray-200 text-gray-700 py-4 rounded-xl font-bold text-lg text-center hover:bg-gray-300 transition">
            BACK
        </a>
        <a href="<?php echo BASE_PATH; ?>/cart.php" id="addToCartBtn" class="flex-1 bg-primary text-white py-4 rounded-xl font-bold text-lg text-center shadow-lg hover:bg-orange-600 transition">
            ADD TO CART
        </a>
    </div>
</div>

<script>
// Track selected items
let selectedItems = [];
const maxItems = 3;

function updateSelection() {
    const checkboxes = document.querySelectorAll('.dish-checkbox');
    selectedItems = [];
    let total = 0;
    
    checkboxes.forEach((checkbox, index) => {
        if (checkbox.checked) {
            const menuItem = checkbox.closest('.menu-item');
            const price = parseFloat(menuItem.dataset.price);
            const name = menuItem.dataset.name;
            
            selectedItems.push({ name, price });
            total += price;
        }
    });
    
    // Update display
    document.getElementById('itemCount').textContent = selectedItems.length + (selectedItems.length === 1 ? ' item' : ' items');
    document.getElementById('totalPrice').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
    
    // Disable/enable checkboxes based on max selection
    if (selectedItems.length >= maxItems) {
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.disabled = true;
                checkbox.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    } else {
        checkboxes.forEach(checkbox => {
            checkbox.disabled = false;
            checkbox.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
        });
    }
    
    // Validate button state
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (selectedItems.length === 0) {
        addToCartBtn.classList.add('opacity-50', 'pointer-events-none');
    } else {
        addToCartBtn.classList.remove('opacity-50', 'pointer-events-none');
    }
}

// Package size selection
const sizeButtons = document.querySelectorAll('.border-2');
sizeButtons.forEach(button => {
    button.addEventListener('click', function() {
        // Remove active state from all
        sizeButtons.forEach(btn => {
            btn.classList.remove('border-primary', 'bg-orange-50', 'text-primary');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        // Add active state to clicked
        this.classList.remove('border-gray-300', 'text-gray-600');
        this.classList.add('border-primary', 'bg-orange-50', 'text-primary');
        
        // Show feedback
        const size = this.textContent.trim();
        showToast(`Package size: ${size}`);
    });
});

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-black text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelection();
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
