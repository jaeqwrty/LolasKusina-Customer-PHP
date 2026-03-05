<?php
// Cart / Checkout View
$pageTitle = "Your Order";
$currentPage = "order";
$cartCount = count($_SESSION['cart'] ?? []);
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-3xl mb-20 md:mb-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <a href="/" class="bg-white rounded-full p-2 shadow-md hover:shadow-lg transition">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Your Order</h1>
        <button onclick="clearAllCart()" class="text-red-500 font-semibold text-sm hover:text-red-600 transition">Clear All</button>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h2>

        <?php
        $cartItems = [
            [
                'id' => 1,
                'name' => 'Paborito Package',
                'description' => 'Good for 6-7 pax',
                'price' => 2500,
                'quantity' => 1,
                'image' => 'paborito-package.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Halo-Halo Special',
                'description' => 'Classic Filipino Dessert',
                'price' => 85,
                'quantity' => 2,
                'image' => 'halo-halo.jpg'
            ]
        ];

        foreach ($cartItems as $index => $item):
        ?>
        <div class="cart-item flex items-center space-x-3 py-3 border-b border-gray-100 last:border-0" data-index="<?php echo $index; ?>" data-price="<?php echo $item['price']; ?>">
            <div class="w-20 h-20 rounded-lg overflow-hidden shrink-0 bg-[#FFF3EE]">
                <img src="/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='/images/placeholder.svg'">
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800"><?php echo $item['name']; ?></h3>
                <p class="text-xs text-gray-500 mb-2"><?php echo $item['description']; ?></p>
                <div class="flex items-center justify-between">
                    <span class="item-price text-lg font-bold text-primary">₱<?php echo number_format($item['price'], 2); ?></span>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(<?php echo $index; ?>, -1)" class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="item-quantity text-sm font-bold text-gray-800 w-6 text-center"><?php echo $item['quantity']; ?></span>
                        <button onclick="updateQuantity(<?php echo $index; ?>, 1)" class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <button onclick="removeItem(<?php echo $index; ?>)" class="text-red-500 ml-2 hover:text-red-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Delivery Details -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Delivery Details</h2>
        
        <div class="space-y-3">
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Full Name</label>
                <input type="text" placeholder="Juan Dela Cruz" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Phone Number</label>
                <input type="tel" placeholder="09XX XXX XXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Delivery Address</label>
                <textarea placeholder="House No., Street, Barangay, City" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            </div>
            
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Delivery Date & Time</label>
                <input type="datetime-local" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
        </div>
    </div>

    <!-- Payment Method -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h2>
        
        <div class="space-y-3">
            <label class="flex items-center p-3 border-2 border-primary bg-orange-50 rounded-lg cursor-pointer">
                <input type="radio" name="payment" value="cod" checked class="w-5 h-5 text-primary">
                <div class="ml-3 flex-1">
                    <div class="font-semibold text-gray-800">Cash on Delivery</div>
                    <div class="text-xs text-gray-500">Pay when you receive</div>
                </div>
                <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                </svg>
            </label>
            
            <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary">
                <input type="radio" name="payment" value="gcash" class="w-5 h-5 text-primary">
                <div class="ml-3 flex-1">
                    <div class="font-semibold text-gray-800">GCash</div>
                    <div class="text-xs text-gray-500">Pay via GCash</div>
                </div>
                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                </svg>
            </label>
            
            <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary">
                <input type="radio" name="payment" value="bank" class="w-5 h-5 text-primary">
                <div class="ml-3 flex-1">
                    <div class="font-semibold text-gray-800">Bank Transfer</div>
                    <div class="text-xs text-gray-500">Pay via bank</div>
                </div>
                <svg class="w-8 h-8 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
            </label>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
        
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span id="subtotal" class="font-semibold">₱2,670.00</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Delivery Fee</span>
                <span id="deliveryFee" class="font-semibold">₱50.00</span>
            </div>
            <div class="flex justify-between text-green-600">
                <span>Discount</span>
                <span id="discount" class="font-semibold">-₱100.00</span>
            </div>
            <div class="border-t border-gray-200 pt-2 mt-2"></div>
            <div class="flex justify-between text-lg">
                <span class="font-bold text-gray-800">Total</span>
                <span id="total" class="font-bold text-primary">₱2,620.00</span>
            </div>
        </div>
    </div>

    <!-- Place Order Button -->
    <a href="/order_confirmation.php?order_id=1" id="placeOrderBtn" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-orange-600 transition flex items-center justify-center space-x-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>PLACE ORDER - ₱2,620.00</span>
    </a>
</div>

<script>
// Cart management functions
const deliveryFee = 50;
const discount = 100;

function calculateTotal() {
    let subtotal = 0;
    const items = document.querySelectorAll('.cart-item');
    
    items.forEach(item => {
        const price = parseFloat(item.dataset.price);
        const quantity = parseInt(item.querySelector('.item-quantity').textContent);
        subtotal += price * quantity;
    });
    
    const total = subtotal + deliveryFee - discount;
    
    // Update display
    document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('deliveryFee').textContent = '₱' + deliveryFee.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('discount').textContent = '-₱' + discount.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('total').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
    
    // Update button
    document.querySelector('#placeOrderBtn span').textContent = 'PLACE ORDER - ₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
    
    return total;
}

function updateQuantity(index, change) {
    const item = document.querySelector(`.cart-item[data-index="${index}"]`);
    if (!item) return;
    
    const quantitySpan = item.querySelector('.item-quantity');
    let quantity = parseInt(quantitySpan.textContent);
    
    quantity += change;
    
    // Ensure quantity stays within bounds
    if (quantity < 1) quantity = 1;
    if (quantity > 99) quantity = 99;
    
    quantitySpan.textContent = quantity;
    
    // Update item price display
    const price = parseFloat(item.dataset.price);
    const itemPrice = item.querySelector('.item-price');
    itemPrice.textContent = '₱' + (price * quantity).toLocaleString('en-PH', {minimumFractionDigits: 2});
    
    calculateTotal();
    
    // Show feedback
    showToast(`Quantity updated to ${quantity}`);
}

function removeItem(index) {
    if (confirm('Remove this item from cart?')) {
        const item = document.querySelector(`.cart-item[data-index="${index}"]`);
        if (item) {
            item.style.transition = 'opacity 0.3s, transform 0.3s';
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                item.remove();
                calculateTotal();
                
                // Check if cart is empty
                const remainingItems = document.querySelectorAll('.cart-item');
                if (remainingItems.length === 0) {
                    showEmptyCart();
                }
                
                showToast('Item removed from cart');
            }, 300);
        }
    }
}

function clearAllCart() {
    if (confirm('Are you sure you want to clear all items from cart?')) {
        const items = document.querySelectorAll('.cart-item');
        items.forEach(item => {
            item.style.transition = 'opacity 0.3s';
            item.style.opacity = '0';
        });
        
        setTimeout(() => {
            items.forEach(item => item.remove());
            showEmptyCart();
            showToast('Cart cleared');
        }, 300);
    }
}

function showEmptyCart() {
    const orderItemsDiv = document.querySelector('.bg-white.rounded-xl.shadow-md.p-4.mb-4');
    orderItemsDiv.innerHTML = `
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h2>
        <div class="text-center py-8">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-gray-500 mb-4">Your cart is empty</p>
            <a href="/" class="inline-block bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-orange-600 transition">
                Start Shopping
            </a>
        </div>
    `;
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-black text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });
    });
    
    // Initialize total
    calculateTotal();
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
