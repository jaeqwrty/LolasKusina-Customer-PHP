<?php
// Order Details View
$pageTitle = "Order Details - Paborito Package";
$currentPage = "packages";
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-6 max-w-md mb-20">
    <!-- Back Button and Actions -->
    <div class="flex justify-between items-center mb-4">
        <a href="/" class="bg-white rounded-full p-2 shadow-md">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div class="flex space-x-2">
            <button class="bg-white rounded-full p-2 shadow-md">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>
            <button class="bg-white rounded-full p-2 shadow-md">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Package Image -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 mb-6 flex items-center justify-center">
        <img src="/images/paborito-package.png" alt="Paborito Package" class="w-48 h-48 object-contain" onerror="this.src='/images/placeholder.svg'">
    </div>

    <!-- Package Info -->
    <div class="mb-6">
        <div class="flex justify-between items-start mb-2">
            <h1 class="text-2xl font-bold text-gray-800">Paborito Package</h1>
            <span class="text-2xl font-bold text-primary">₱<?php echo number_format($package['price'] ?? 2500, 2); ?></span>
        </div>
        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="font-semibold">4.8</span>
                <span class="ml-1">(20 Reviews)</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span>30-45 mins</span>
            </div>
        </div>
        <div class="bg-orange-100 text-orange-700 text-sm px-3 py-1 rounded-full inline-block">
            🔥 Good for 10-12 persons
        </div>
    </div>

    <!-- Package Contents -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Mga Kasama sa Package</h2>
        
        <?php
        $items = [
            ['name' => 'Pancit Canton', 'description' => 'Stir-Fried Yellow Noodles', 'image' => 'pancit-canton.jpg', 'checked' => true],
            ['name' => 'Lumpiang Shanghai', 'description' => 'Mini Spring Rolls', 'image' => 'lumpia.jpg', 'checked' => true],
            ['name' => 'Lechon Kawali', 'description' => 'Crispy Fried Pork Belly', 'image' => 'lechon-kawali.jpg', 'checked' => true],
            ['name' => 'Steamed Rice', 'description' => 'Good for 10-12', 'image' => 'rice.jpg', 'checked' => true]
        ];
        
        foreach ($items as $item):
        ?>
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div class="flex items-center space-x-3">
                <img src="/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-12 h-12 rounded-lg object-cover" onerror="this.src='/images/placeholder.svg'">
                <div>
                    <h3 class="font-semibold text-gray-800"><?php echo $item['name']; ?></h3>
                    <p class="text-xs text-gray-500"><?php echo $item['description']; ?></p>
                </div>
            </div>
            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quantity Selector -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex items-center justify-between">
            <span class="text-gray-700 font-semibold">Quantity</span>
            <div class="flex items-center space-x-4">
                <button onclick="decreaseQuantity()" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <span id="quantity" class="text-xl font-bold text-gray-800 min-w-[2rem] text-center">1</span>
                <button onclick="increaseQuantity()" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Order Button -->
    <a href="/cart.php" id="addToCartBtn" class="block w-full bg-primary text-white py-4 rounded-xl font-bold text-lg text-center shadow-lg hover:bg-orange-600 transition">
        ADD TO CART - ₱2,500.00
    </a>
</div>

<script>
let quantity = 1;
const basePrice = 2500;

function updateDisplay() {
    document.getElementById('quantity').textContent = quantity;
    const totalPrice = basePrice * quantity;
    document.getElementById('addToCartBtn').innerHTML = 
        `ADD TO CART - ₱${totalPrice.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

function increaseQuantity() {
    if (quantity < 99) {
        quantity++;
        updateDisplay();
    }
}

function decreaseQuantity() {
    if (quantity > 1) {
        quantity--;
        updateDisplay();
    }
}

// Add to favorites functionality
document.querySelectorAll('.bg-white.rounded-full.p-2').forEach((btn, index) => {
    if (index === 1) { // Favorite button
        btn.addEventListener('click', function() {
            const svg = this.querySelector('svg');
            svg.classList.toggle('fill-red-500');
            svg.classList.toggle('text-red-500');
            
            // Show feedback
            const feedback = document.createElement('div');
            feedback.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-black text-white px-4 py-2 rounded-lg shadow-lg z-50';
            feedback.textContent = svg.classList.contains('fill-red-500') ? '❤️ Added to favorites!' : 'Removed from favorites';
            document.body.appendChild(feedback);
            
            setTimeout(() => feedback.remove(), 2000);
        });
    }
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
