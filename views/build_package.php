<?php
// Build Your Own Package View
$pageTitle = "Build Your Own Package";
$currentPage = "packages";
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-4xl mb-20 md:mb-8">
    <!-- Back Button -->
    <div class="flex items-center justify-between mb-6">
        <button onclick="goBack()" class="bg-white rounded-full p-2 shadow-md">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <h1 class="text-xl font-bold text-gray-800">Build Your Package</h1>
        <div class="w-10"></div>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex items-center justify-between">
            <button onclick="goToStep(1)" class="flex flex-col items-center cursor-pointer transition" id="stepBtn1">
                <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold step-indicator" data-step="1">1</div>
                <span class="text-xs text-gray-600 mt-1">Main Dish</span>
            </button>
            <div class="flex-1 h-1 bg-gray-300 mx-2 step-line" id="line1"></div>
            <button onclick="goToStep(2)" class="flex flex-col items-center cursor-pointer transition" id="stepBtn2">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold step-indicator" data-step="2">2</div>
                <span class="text-xs text-gray-600 mt-1">Side Dish</span>
            </button>
            <div class="flex-1 h-1 bg-gray-300 mx-2 step-line" id="line2"></div>
            <button onclick="goToStep(3)" class="flex flex-col items-center cursor-pointer transition" id="stepBtn3">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold step-indicator" data-step="3">3</div>
                <span class="text-xs text-gray-600 mt-1">Dessert</span>
            </button>
            <div class="flex-1 h-1 bg-gray-300 mx-2 step-line" id="line3"></div>
            <button onclick="goToStep(4)" class="flex flex-col items-center cursor-pointer transition" id="stepBtn4">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold step-indicator" data-step="4">4</div>
                <span class="text-xs text-gray-600 mt-1">Review</span>
            </button>
        </div>
    </div>

    <!-- Step 1: Main Dishes -->
    <div id="step1" class="step-content">
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Choose Main Dishes</h2>
                <span class="text-sm text-gray-500">Select as many as you want</span>
            </div>

            <?php
            $mainDishes = [
                ['name' => 'Lechon Kawali', 'description' => 'Crispy Fried Pork Belly', 'price' => 450, 'image' => 'lechon-kawali.jpg'],
                ['name' => 'Chicken Inasal', 'description' => 'Grilled Marinated Chicken', 'price' => 380, 'image' => 'chicken-inasal.jpg'],
                ['name' => 'Kare-Kare', 'description' => 'Oxtail in Peanut Sauce', 'price' => 520, 'image' => 'kare-kare.jpg'],
                ['name' => 'Pata (Crispy)', 'description' => 'Deep Fried Pork Leg', 'price' => 680, 'image' => 'pata.jpg'],
                ['name' => 'Beef Caldereta', 'description' => 'Beef in Tomato Sauce', 'price' => 480, 'image' => 'caldereta.jpg']
            ];

            foreach ($mainDishes as $index => $dish):
            ?>
            <div class="menu-item flex items-center justify-between py-3 border-b border-gray-100 last:border-0" data-category="main" data-price="<?php echo $dish['price']; ?>" data-name="<?php echo $dish['name']; ?>">
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
                    <input type="checkbox" class="category-checkbox sr-only peer" onchange="updateSelection()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Step 2: Side Dishes -->
    <div id="step2" class="step-content hidden">
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Choose Side Dishes</h2>
                <span class="text-sm text-gray-500">Select as many as you want</span>
            </div>

            <?php
            $sideDishes = [
                ['name' => 'Pancit Canton', 'description' => 'Stir-Fried Yellow Noodles', 'price' => 180, 'image' => 'pancit-canton.jpg'],
                ['name' => 'Fried Rice', 'description' => 'Garlic or Plain Rice', 'price' => 150, 'image' => 'rice.jpg'],
                ['name' => 'Lumpia Shanghai', 'description' => 'Mini Spring Rolls', 'price' => 200, 'image' => 'lumpia.jpg'],
                ['name' => 'Laing', 'description' => 'Taro Leaves in Coconut', 'price' => 140, 'image' => 'laing.jpg'],
                ['name' => 'Pinakbet', 'description' => 'Mixed Vegetable Stew', 'price' => 160, 'image' => 'pinakbet.jpg']
            ];

            foreach ($sideDishes as $index => $dish):
            ?>
            <div class="menu-item flex items-center justify-between py-3 border-b border-gray-100 last:border-0" data-category="side" data-price="<?php echo $dish['price']; ?>" data-name="<?php echo $dish['name']; ?>">
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
                    <input type="checkbox" class="category-checkbox sr-only peer" onchange="updateSelection()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Step 3: Desserts -->
    <div id="step3" class="step-content hidden">
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Choose Desserts</h2>
                <span class="text-sm text-gray-500">Select as many as you want</span>
            </div>

            <?php
            $desserts = [
                ['name' => 'Buko Pandan', 'description' => 'Creamy Coconut Dessert', 'price' => 120, 'image' => 'buko-pandan.jpg'],
                ['name' => 'Halo-Halo', 'description' => 'Classic Filipino Dessert', 'price' => 100, 'image' => 'halo-halo.jpg'],
                ['name' => 'Leche Flan', 'description' => 'Caramel Custard', 'price' => 110, 'image' => 'leche-flan.jpg'],
                ['name' => 'Ube Cake', 'description' => 'Purple Yam Cake Slice', 'price' => 95, 'image' => 'ube-cake.jpg'],
                ['name' => 'Fruit Salad', 'description' => 'Seasonal Mixed Fruits', 'price' => 150, 'image' => 'fruit-salad.jpg']
            ];

            foreach ($desserts as $index => $dish):
            ?>
            <div class="menu-item flex items-center justify-between py-3 border-b border-gray-100 last:border-0" data-category="dessert" data-price="<?php echo $dish['price']; ?>" data-name="<?php echo $dish['name']; ?>">
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
                    <input type="checkbox" class="category-checkbox sr-only peer" onchange="updateSelection()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Step 4: Review Order -->
    <div id="step4" class="step-content hidden">
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Review Your Package</h2>
            
            <!-- Main Dishes Section -->
            <div id="reviewMain" class="mb-6 hidden">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 flex items-center">
                    <span class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-xs mr-2">🍖</span>
                    Main Dishes
                </h3>
                <div id="reviewMainItems" class="space-y-2 bg-gray-50 p-3 rounded-lg"></div>
            </div>
            
            <!-- Side Dishes Section -->
            <div id="reviewSide" class="mb-6 hidden">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 flex items-center">
                    <span class="w-6 h-6 bg-secondary text-white rounded-full flex items-center justify-center text-xs mr-2">🍜</span>
                    Side Dishes
                </h3>
                <div id="reviewSideItems" class="space-y-2 bg-gray-50 p-3 rounded-lg"></div>
            </div>
            
            <!-- Desserts Section -->
            <div id="reviewDessert" class="mb-6 hidden">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 flex items-center">
                    <span class="w-6 h-6 bg-accent text-white rounded-full flex items-center justify-center text-xs mr-2">🍰</span>
                    Desserts
                </h3>
                <div id="reviewDessertItems" class="space-y-2 bg-gray-50 p-3 rounded-lg"></div>
            </div>
            
            <!-- Total -->
            <div class="border-t-2 border-gray-200 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">Total Price:</span>
                    <span id="reviewTotal" class="text-3xl font-bold text-primary">₱0.00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl shadow-md p-4 mb-6 border-2 border-orange-200">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-700 font-semibold">Selected Items:</span>
            <span id="itemCount" class="text-primary font-bold">0 items</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-700 font-semibold">Current Total:</span>
            <span id="totalPrice" class="text-2xl font-bold text-primary">₱0.00</span>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex space-x-3">
        <button onclick="previousStep()" id="prevBtn" class="flex-1 bg-gray-200 text-gray-700 py-4 rounded-xl font-bold text-lg text-center hover:bg-gray-300 transition hidden">
            ← BACK
        </button>
        <button onclick="nextStep()" id="nextBtn" class="flex-1 bg-primary text-white py-4 rounded-xl font-bold text-lg text-center shadow-lg hover:bg-orange-600 transition">
            NEXT →
        </button>
        <button onclick="addToCart()" id="addToCartBtn" class="flex-1 bg-primary text-white py-4 rounded-xl font-bold text-lg text-center shadow-lg hover:bg-orange-600 transition hidden">
            ADD TO CART ✓
        </button>
    </div>
</div>

<script>
let currentStep = 1;
let selectedItems = {};

// ── Navigation ────────────────────────────────────────
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = BASE_PATH || '/';
    }
}

function goToStep(step) {
    if (step >= 1 && step <= 4) {
        currentStep = step;
        updateStepDisplay();
    }
}

function nextStep() {
    if (currentStep < 3) {
        currentStep++;
        updateStepDisplay();
    } else if (currentStep === 3) {
        // Move to review step
        currentStep = 4;
        updateStepDisplay();
        populateReview();
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}

function updateStepDisplay() {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    
    // Show current step
    document.getElementById('step' + currentStep).classList.remove('hidden');
    
    // Update progress indicators
    for (let i = 1; i <= 4; i++) {
        const btn = document.getElementById('stepBtn' + i);
        const indicator = btn.querySelector('.step-indicator');
        const line = document.getElementById('line' + (i-1));
        
        if (i < currentStep) {
            // Completed steps
            indicator.classList.remove('bg-gray-300', 'text-gray-600');
            indicator.classList.add('bg-primary', 'text-white');
            if (line) line.classList.remove('bg-gray-300');
            if (line) line.classList.add('bg-primary');
        } else if (i === currentStep) {
            // Current step
            indicator.classList.remove('bg-gray-300', 'text-gray-600');
            indicator.classList.add('bg-primary', 'text-white');
        } else {
            // Future steps
            indicator.classList.add('bg-gray-300', 'text-gray-600');
            indicator.classList.remove('bg-primary', 'text-white');
        }
    }
    
    // Update buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (currentStep === 1) {
        prevBtn.classList.add('hidden');
        nextBtn.textContent = 'NEXT →';
        nextBtn.classList.remove('hidden');
        addToCartBtn.classList.add('hidden');
    } else if (currentStep === 2 || currentStep === 3) {
        prevBtn.classList.remove('hidden');
        nextBtn.textContent = 'NEXT →';
        nextBtn.classList.remove('hidden');
        addToCartBtn.classList.add('hidden');
    } else if (currentStep === 4) {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.add('hidden');
        addToCartBtn.classList.remove('hidden');
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    selectedItems = {};
    let total = 0;
    
    checkboxes.forEach((checkbox) => {
        if (checkbox.checked) {
            const menuItem = checkbox.closest('.menu-item');
            const category = menuItem.dataset.category;
            const price = parseFloat(menuItem.dataset.price);
            const name = menuItem.dataset.name;
            
            if (!selectedItems[category]) {
                selectedItems[category] = [];
            }
            selectedItems[category].push({ name, price });
            total += price;
        }
    });
    
    // Update display
    let itemCount = 0;
    for (let cat in selectedItems) {
        itemCount += selectedItems[cat].length;
    }
    
    document.getElementById('itemCount').textContent = itemCount + (itemCount === 1 ? ' item' : ' items');
    document.getElementById('totalPrice').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function populateReview() {
    let total = 0;
    
    // Helper function to create item row
    const createItemRow = (name, price) => {
        total += price;
        return `<div class="flex justify-between items-center py-2"><span class="text-gray-700">${name}</span><span class="font-semibold text-gray-800">₱${price.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span></div>`;
    };
    
    // Populate Main Dishes
    if (selectedItems['main'] && selectedItems['main'].length > 0) {
        document.getElementById('reviewMain').classList.remove('hidden');
        document.getElementById('reviewMainItems').innerHTML = selectedItems['main']
            .map(item => createItemRow(item.name, item.price))
            .join('');
    } else {
        document.getElementById('reviewMain').classList.add('hidden');
    }
    
    // Populate Side Dishes
    if (selectedItems['side'] && selectedItems['side'].length > 0) {
        document.getElementById('reviewSide').classList.remove('hidden');
        document.getElementById('reviewSideItems').innerHTML = selectedItems['side']
            .map(item => createItemRow(item.name, item.price))
            .join('');
    } else {
        document.getElementById('reviewSide').classList.add('hidden');
    }
    
    // Populate Desserts
    if (selectedItems['dessert'] && selectedItems['dessert'].length > 0) {
        document.getElementById('reviewDessert').classList.remove('hidden');
        document.getElementById('reviewDessertItems').innerHTML = selectedItems['dessert']
            .map(item => createItemRow(item.name, item.price))
            .join('');
    } else {
        document.getElementById('reviewDessert').classList.add('hidden');
    }
    
    // Update total
    document.getElementById('reviewTotal').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function addToCart() {
    // Collect all selected items
    let cartItems = [];
    for (let category in selectedItems) {
        selectedItems[category].forEach(item => {
            cartItems.push({
                name: item.name,
                price: item.price,
                category: category,
                quantity: 1
            });
        });
    }
    
    if (cartItems.length === 0) {
        alert('Please select at least one item!');
        return;
    }
    
    // Save to session storage
    let cart = [];
    try { cart = JSON.parse(sessionStorage.getItem('cart') || '[]'); } catch(e) {}
    
    // Add custom package
    const customPackage = {
        id: 'custom-' + Date.now(),
        name: 'Custom Built Package',
        items: cartItems,
        total: cartItems.reduce((sum, item) => sum + item.price, 0),
        type: 'custom'
    };
    
    cart.push(customPackage);
    sessionStorage.setItem('cart', JSON.stringify(cart));
    
    // Redirect to cart
    window.location.href = BASE_PATH + '/cart.php';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStepDisplay();
    updateSelection();
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
