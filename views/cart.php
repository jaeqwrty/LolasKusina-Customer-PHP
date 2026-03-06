<?php
// Cart / Multi-Step Checkout View
$pageTitle = "My Cart";
$currentPage = "cart";
$cartCount = count($_SESSION['cart'] ?? []);

// Sample cart items (replace with session data)
$cartItems = [
    [
        'id' => 1,
        'name' => 'Pinoy Breakfast Special',
        'description' => 'Garlic Rice, Fried Egg, Tapa',
        'price' => 185,
        'quantity' => 1,
        'image' => 'pinoy-breakfast.jpg'
    ],
    [
        'id' => 2,
        'name' => 'Vegetable Salad Bowl',
        'description' => 'Fresh greens, Vinaigrette',
        'price' => 210,
        'quantity' => 2,
        'image' => 'vegetable-salad.jpg'
    ]
];

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

include __DIR__ . '/layouts/header.php';
?>

<style>
    /* Step progress bar */
    .step-progress { height: 4px; border-radius: 2px; }
    .step-progress-fill { height: 100%; border-radius: 2px; transition: width 0.4s ease; }
    
    /* Checkout step transitions */
    .checkout-step { display: none; animation: fadeSlideIn 0.35s ease; }
    .checkout-step.active { display: block; }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    /* Option cards */
    .option-card { transition: all 0.2s ease; cursor: pointer; }
    .option-card.selected { border-color: #FF6B35; background: #FFF7ED; }
    .option-card .radio-dot { width: 22px; height: 22px; border: 2px solid #D1D5DB; border-radius: 50%; transition: all 0.2s; }
    .option-card.selected .radio-dot { border-color: #FF6B35; background: #FF6B35; box-shadow: inset 0 0 0 4px white; }
    
    /* Upload area */
    .upload-area { border: 2px dashed #D1D5DB; transition: all 0.2s; }
    .upload-area:hover, .upload-area.dragover { border-color: #FF6B35; background: #FFF7ED; }
    .upload-area.has-file { border-color: #22C55E; background: #F0FDF4; }
    
    /* Payment account cards */
    .payment-account { background: #F9FAFB; border-radius: 12px; padding: 14px 16px; }
    
    /* Review item rows */
    .review-item { display: flex; align-items: center; gap: 12px; padding: 12px 0; }
    .review-item + .review-item { border-top: 1px solid #F3F4F6; }
    
    /* Schedule selectors */
    .schedule-option { cursor: pointer; transition: all 0.2s; }
    .schedule-option.selected { background: #FF6B35; color: white; }
    .schedule-option.selected .schedule-sub { color: rgba(255,255,255,0.8); }
</style>

<div class="container mx-auto px-4 md:px-8 py-4 max-w-md md:max-w-2xl mb-20 md:mb-8">
    
    <!-- ==================== TOP NAV ==================== -->
    <div class="flex items-center justify-between mb-2">
        <button onclick="goBack()" class="flex items-center text-gray-600 hover:text-gray-800 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="text-sm font-medium">Bumalik</span>
        </button>
        <span id="stepLabel" class="text-sm font-semibold text-gray-500">CART</span>
    </div>

    <!-- Progress Bar -->
    <div class="step-progress bg-gray-200 w-full mb-6">
        <div id="progressFill" class="step-progress-fill bg-primary" style="width: 25%"></div>
    </div>

    <!-- ==================== STEP 0: VIEW CART ==================== -->
    <div id="step0" class="checkout-step active">
        <h2 class="text-xl font-bold text-primary leading-tight mb-1">My Cart</h2>
        <p class="text-sm text-gray-500 mb-6">Review your items before checkout</p>

        <!-- Cart Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Order Items</h3>
            <div id="cartItemsList">
                <?php foreach ($cartItems as $item): ?>
                <div class="review-item">
                    <div class="w-14 h-14 rounded-lg bg-orange-50 overflow-hidden shrink-0">
                        <img src="<?php echo BASE_PATH; ?>/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='<?php echo BASE_PATH; ?>/images/placeholder.svg'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="font-bold text-gray-800 text-sm whitespace-nowrap">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        <div class="flex items-center space-x-2 bg-gray-100 rounded-lg px-2 py-1">
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" class="text-gray-600 hover:text-gray-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span class="text-xs font-semibold text-gray-800 min-w-[1.5rem] text-center">x<?php echo $item['quantity']; ?></span>
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" class="text-gray-600 hover:text-gray-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Subtotal</span>
                <span id="cartSubtotal" class="font-semibold text-gray-800">₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="border-t border-gray-100 pt-2 mt-2 flex justify-between items-center">
                <span class="font-bold text-gray-800">Subtotal</span>
                <span id="cartTotal" class="font-bold text-primary text-lg">₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <button onclick="goToStep(1)" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-orange-600 transition flex items-center justify-center gap-2">
                PROCEED TO CHECKOUT
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button onclick="continueShopping()" class="w-full bg-white text-primary py-3 rounded-xl font-bold border-2 border-primary hover:bg-orange-50 transition">
                CONTINUE SHOPPING
            </button>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">May problema sa order? <a href="#" class="text-primary font-semibold">Tumawag sa amin.</a></p>
    </div>
        <!-- ==================== STEP 1: Delivery or Pickup ==================== -->
    <div id="step1" class="checkout-step">
        <h2 class="text-xl font-bold text-primary leading-tight mb-1">Paano mo gustong makuha ang order?</h2>
        <p class="text-sm text-gray-500 mb-6">Step 1 of 4: Delivery or Pickup</p>

        <!-- Delivery Option -->
        <div class="option-card selected border-2 rounded-2xl p-5 mb-4" data-method="delivery" onclick="selectMethod('delivery')">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">DELIVERY</h3>
                        <p class="text-xs text-gray-500">+ delivery fee applies</p>
                    </div>
                </div>
                <div class="radio-dot"></div>
            </div>
            <button class="w-full mt-4 bg-primary text-white py-3 rounded-xl font-bold text-sm hover:bg-orange-600 transition">
                Piliin Ito +
            </button>
        </div>

        <!-- Pickup Option -->
        <div class="option-card border-2 border-gray-200 rounded-2xl p-5 mb-4" data-method="pickup" onclick="selectMethod('pickup')">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/><path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">PICKUP</h3>
                        <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Walang delivery fee</span>
                    </div>
                </div>
                <div class="radio-dot"></div>
            </div>
            <button class="w-full mt-4 bg-white text-gray-700 py-3 rounded-xl font-bold text-sm border-2 border-gray-200 hover:border-primary hover:text-primary transition">
                Piliin Ito
            </button>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">May problema sa order? <a href="#" class="text-primary font-semibold">Tumawag sa amin.</a></p>
    </div>

    <!-- ==================== STEP 2A: Delivery Details ==================== -->
    <div id="step2-delivery" class="checkout-step">
        <div class="flex items-center justify-between mb-1">
            <div>
                <p class="text-xs text-red-500 font-semibold">Step 2 of 4</p>
                <h2 class="text-xl font-bold text-gray-800">Delivery Details</h2>
            </div>
            <span class="text-xs text-gray-400 font-medium">Address &amp; Schedule</span>
        </div>

        <!-- Delivery Address -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mt-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">Delivery Address</h3>
            <div class="flex items-start gap-2 mb-3">
                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                <input id="deliveryAddress" type="text" placeholder="123 Rizal Ave, Manila" value="" class="flex-1 text-sm text-gray-800 font-medium bg-transparent border-b border-gray-200 pb-1 focus:outline-none focus:border-primary">
            </div>
            
            <!-- Map placeholder -->
            <div class="w-full h-40 bg-gray-100 rounded-xl mb-3 flex items-center justify-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-blue-50"></div>
                <div class="relative text-center">
                    <svg class="w-8 h-8 text-primary mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    <p class="text-xs text-gray-500">Map Preview</p>
                </div>
            </div>
            <button class="text-primary text-sm font-semibold hover:underline">Edit Map</button>
        </div>

        <!-- Distance & Fee (delivery only) -->
        <div id="deliveryFeeSection" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm text-gray-600">Distance</span>
                </div>
                <span id="distanceDisplay" class="font-bold text-gray-800">8.5 km</span>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/></svg>
                    <span class="text-sm text-gray-600">Delivery Fee</span>
                </div>
                <span class="font-bold text-primary text-lg">₱<span id="deliveryFeeAmount">102.00</span></span>
            </div>
        </div>

        <!-- Schedule Delivery -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-4">Schedule Delivery</h3>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <!-- Date -->
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">DATE</label>
                    <div class="schedule-option selected bg-gray-100 rounded-xl p-3 text-center" onclick="selectScheduleDate(this, 'today')">
                        <div class="font-bold text-sm">Today</div>
                        <div class="schedule-sub text-xs text-gray-500"><?php echo date('M d, Y'); ?></div>
                    </div>
                    <input type="date" id="customDate" class="w-full mt-2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <!-- Time -->
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">TIME</label>
                    <div class="schedule-option selected bg-gray-100 rounded-xl p-3 text-center" onclick="selectScheduleTime(this, 'asap')">
                        <div class="font-bold text-sm flex items-center justify-center gap-1">ASAP <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg></div>
                        <div class="schedule-sub text-xs text-gray-500">~ 45 mins</div>
                    </div>
                    <input type="time" id="customTime" class="w-full mt-2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <!-- Note to rider -->
            <div>
                <label class="text-xs text-gray-500 font-medium mb-1 block">Note to rider (optional)</label>
                <input id="riderNote" type="text" placeholder="Example: Gate is color blue, call upon arrival" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
        </div>

        <!-- Next Button -->
        <button onclick="goToStep(3)" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-base shadow-lg hover:bg-orange-600 transition flex items-center justify-center gap-2 mt-2">
            NEXT: Payment Info
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- ==================== STEP 2B: Pickup Schedule ==================== -->
    <div id="step2-pickup" class="checkout-step">
        <div class="flex items-center justify-between mb-1">
            <div>
                <p class="text-xs text-orange-500 font-semibold">Step 2 of 4</p>
                <h2 class="text-xl font-bold text-gray-800">Pickup Schedule</h2>
            </div>
            <span class="text-xs text-gray-400 font-medium">Location &amp; Time</span>
        </div>

        <!-- Store Location -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mt-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">Pickup Location</h3>
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Lola's Kusina Store</p>
                    <p class="text-xs text-gray-500">123 Maligaya St., Quezon City, Metro Manila</p>
                </div>
            </div>
            <!-- Map placeholder -->
            <div class="w-full h-40 bg-gray-100 rounded-xl mb-3 flex items-center justify-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-blue-50"></div>
                <div class="relative text-center">
                    <svg class="w-8 h-8 text-primary mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    <p class="text-xs text-gray-500">Store Location</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg p-3">
                <svg class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="text-xs text-green-700 font-semibold">Walang delivery fee — FREE pickup!</span>
            </div>
        </div>

        <!-- Schedule Pickup -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-4">Schedule Pickup</h3>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <!-- Date -->
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">DATE</label>
                    <div id="pickupDateToday" class="schedule-option selected bg-gray-100 rounded-xl p-3 text-center" onclick="selectPickupDate(this, 'today')">
                        <div class="font-bold text-sm">Today</div>
                        <div class="schedule-sub text-xs text-gray-500"><?php echo date('M d, Y'); ?></div>
                    </div>
                    <input type="date" id="pickupCustomDate" class="w-full mt-2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <!-- Time -->
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">TIME</label>
                    <div id="pickupTimeASAP" class="schedule-option selected bg-gray-100 rounded-xl p-3 text-center" onclick="selectPickupTime(this, 'asap')">
                        <div class="font-bold text-sm flex items-center justify-center gap-1">ASAP <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg></div>
                        <div class="schedule-sub text-xs text-gray-500">~ 30 mins</div>
                    </div>
                    <input type="time" id="pickupCustomTime" class="w-full mt-2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <!-- Note -->
            <div>
                <label class="text-xs text-gray-500 font-medium mb-1 block">Note to store (optional)</label>
                <input id="pickupNote" type="text" placeholder="Example: Please pack separately" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
        </div>

        <!-- Next Button -->
        <button onclick="goToStep(3)" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-base shadow-lg hover:bg-orange-600 transition flex items-center justify-center gap-2 mt-2">
            NEXT: Payment Info
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- ==================== STEP 3: Payment & Contact Info ==================== -->
    <div id="step3" class="checkout-step">
        <div class="flex items-center justify-between mb-1">
            <div>
                <p class="text-xs text-primary font-semibold">Step 3 of 4</p>
                <h2 class="text-xl font-bold text-gray-800">Payment &amp; <span class="text-primary">Contact Info</span></h2>
            </div>
            <span class="text-xs text-gray-400 font-medium">Payment &amp; Contact</span>
        </div>
        <p class="text-xs text-gray-500 mb-5">Please review details carefully</p>

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Order Summary</h3>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Subtotal</span>
                <span class="font-semibold text-gray-800">₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div id="step3FeeRow" class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Delivery Fee</span>
                <span id="step3FeeAmount" class="font-semibold text-gray-800">₱<?php echo number_format(102, 2); ?></span>
            </div>
            <div class="border-t border-gray-100 pt-2 mt-1 flex justify-between items-center mb-2">
                <span class="text-sm font-bold text-gray-700">Total Bill</span>
                <span id="step3TotalBill" class="font-bold text-gray-800 text-lg">₱<?php echo number_format($subtotal + 102, 2); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Downpayment Required (50%)</span>
                <span id="step3Downpayment" class="font-bold text-primary">₱<?php echo number_format(($subtotal + 102) * 0.5, 2); ?></span>
            </div>
            <div class="mt-3 bg-orange-50 border border-orange-200 rounded-lg p-3 flex items-start gap-2">
                <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <p class="text-xs text-gray-600">Please pay the downpayment to confirm your order.</p>
            </div>
        </div>

        <!-- Payment Accounts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Send Payment To</h3>
            
            <div class="payment-account flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">GC</div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-800 text-sm">GCash</div>
                    <div class="text-xs text-gray-500">Juan Dela Cruz</div>
                </div>
                <span class="font-mono text-sm font-bold text-gray-800">0917 123 4567</span>
            </div>
            
            <div class="payment-account flex items-center gap-3">
                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-xs">BDO</div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-800 text-sm">BDO Unibank</div>
                </div>
                <span class="font-mono text-sm font-bold text-gray-800">0612 3456 7898</span>
            </div>
        </div>

        <!-- Upload Receipt -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Upload Downpayment Receipt</h3>
            <div id="uploadArea" class="upload-area rounded-xl p-6 text-center cursor-pointer" onclick="document.getElementById('receiptFile').click()">
                <div id="uploadPlaceholder">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500 font-medium">Tap to upload receipt</p>
                    <p class="text-xs text-gray-400 mt-1">(JPG, PNG or PDF Max 5MB)</p>
                </div>
                <div id="uploadSuccess" class="hidden">
                    <svg class="w-10 h-10 text-green-500 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <p id="uploadFileName" class="text-sm text-green-700 font-medium"></p>
                    <p class="text-xs text-gray-400 mt-1">Tap to change</p>
                </div>
            </div>
            <input type="file" id="receiptFile" accept="image/*,.pdf" class="hidden" onchange="handleFileUpload(this)">
        </div>

        <!-- Contact Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Contact Info</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Full Name</label>
                    <input id="contactName" type="text" placeholder="e.g. Maria Clara" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Mobile Number</label>
                    <input id="contactPhone" type="tel" placeholder="+63 917 000 0000" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
        </div>

        <!-- Next Button -->
        <button onclick="goToStep(4)" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-base shadow-lg hover:bg-orange-600 transition flex items-center justify-center gap-2">
            NEXT: Final Review
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- ==================== STEP 4: Final Review ==================== -->
    <div id="step4" class="checkout-step">
        <div class="flex items-center justify-between mb-1">
            <span class="text-sm text-gray-500">Review Order</span>
            <span class="text-xs text-gray-400 font-semibold">4/4</span>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-1">Step 4: <span class="text-primary">Final Review</span></h2>
        <p class="text-xs text-gray-500 mb-5">Please review your order details before checkout.</p>

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-primary p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Order Summary</h3>
            <div id="reviewItems">
                <?php foreach ($cartItems as $item): ?>
                <div class="review-item">
                    <div class="w-14 h-14 rounded-lg bg-orange-50 overflow-hidden shrink-0">
                        <img src="<?php echo BASE_PATH; ?>/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='<?php echo BASE_PATH; ?>/images/placeholder.svg'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($item['description']); ?></p>
                        <span class="text-xs text-primary font-semibold">x<?php echo $item['quantity']; ?></span>
                    </div>
                    <span class="font-bold text-gray-800 text-sm whitespace-nowrap">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Delivery / Pickup Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 id="reviewDetailsLabel" class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Delivery Details</h3>
            <div class="space-y-2">
                <div class="flex items-start gap-2">
                    <span class="inline-block bg-primary text-white text-xs font-bold px-2 py-0.5 rounded">METHOD</span>
                    <span id="reviewMethod" class="text-sm font-medium text-gray-800">Delivery</span>
                </div>
                <div class="flex items-start gap-2">
                    <span id="reviewAddressBadge" class="inline-block bg-gray-200 text-gray-600 text-xs font-bold px-2 py-0.5 rounded">ADDRESS</span>
                    <span id="reviewAddress" class="text-sm text-gray-700">-</span>
                </div>
                <div class="flex items-start gap-2">
                    <span id="reviewArrivalBadge" class="inline-block bg-gray-200 text-gray-600 text-xs font-bold px-2 py-0.5 rounded">ESTIMATED ARRIVAL</span>
                    <span id="reviewArrival" class="text-sm text-gray-700">-</span>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Payment Summary</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span id="reviewSubtotal" class="font-semibold text-gray-800">₱<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div id="reviewFeeRow" class="flex justify-between">
                    <span class="text-gray-600">Delivery Fee</span>
                    <span id="reviewDeliveryFee" class="font-semibold text-gray-800">₱102.00</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2"></div>
                <div class="flex justify-between text-lg">
                    <span class="font-bold text-gray-800">Total Amount</span>
                    <span id="reviewTotal" class="font-bold text-primary">₱<?php echo number_format($subtotal + 102, 2); ?></span>
                </div>
            </div>

            <!-- 50% Downpayment -->
            <div class="mt-3 bg-orange-50 border border-orange-200 rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">50% Downpayment</span>
                    <span id="reviewDownpayment" class="font-bold text-primary">₱<?php echo number_format(($subtotal + 102) * 0.5, 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Place Order Button -->
        <button onclick="placeOrder()" id="placeOrderBtn" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-base shadow-lg hover:bg-orange-600 transition flex items-center justify-center gap-2">
            PLACE ORDER
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

</div>

<script>
// ===== State =====
let currentStep = 0; // Start at cart view (step 0)
let orderMethod = 'delivery'; // 'delivery' or 'pickup'
let deliveryFee = 102;
const subtotal = <?php echo $subtotal; ?>;

// Map step number to the correct div ID based on current method
function getStepId(step) {
    if (step === 0) return 'step0'; // Cart view
    if (step === 2) return orderMethod === 'pickup' ? 'step2-pickup' : 'step2-delivery';
    if (step === 1) return 'step1';
    if (step === 3) return 'step3';
    if (step === 4) return 'step4';
    return 'step' + step;
}

// ===== Navigation =====
function goToStep(step) {
    if (!validateStep(currentStep)) return;

    document.getElementById(getStepId(currentStep)).classList.remove('active');
    currentStep = step;
    document.getElementById(getStepId(currentStep)).classList.add('active');

    // Update progress bar & label
    const progressPercent = (step + 1) * 20; // 0=20%, 1=40%, 2=60%, 3=80%, 4=100%
    document.getElementById('progressFill').style.width = progressPercent + '%';
    
    if (currentStep === 0) {
        document.getElementById('stepLabel').textContent = 'CART';
    } else {
        document.getElementById('stepLabel').textContent = currentStep + '/4';
    }

    if (step === 3) populateStep3();
    if (step === 4) populateReview();

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goBack() {
    if (currentStep > 0) {
        goToStep(currentStep - 1);
    } else {
        window.location.href = BASE_PATH || '/';
    }
}

function continueShopping() {
    window.location.href = BASE_PATH || '/';
}

function validateStep(step) {
    if (step === 0) return true; // Cart view - no validation
    if (step === 2 && orderMethod === 'delivery') {
        const address = document.getElementById('deliveryAddress').value.trim();
        if (!address) {
            showToast('Please enter a delivery address');
            document.getElementById('deliveryAddress').focus();
            return false;
        }
    }
    if (step === 3) {
        const name = document.getElementById('contactName').value.trim();
        const phone = document.getElementById('contactPhone').value.trim();
        if (!name) { showToast('Please enter your name'); document.getElementById('contactName').focus(); return false; }
        if (!phone) { showToast('Please enter your mobile number'); document.getElementById('contactPhone').focus(); return false; }
    }
    return true;
}

// ===== Step 1: Method Selection =====
function selectMethod(method) {
    orderMethod = method;
    deliveryFee = method === 'pickup' ? 0 : 102;

    document.querySelectorAll('.option-card').forEach(card => {
        card.classList.toggle('selected', card.dataset.method === method);
    });

    // Auto-advance after brief delay
    setTimeout(() => goToStep(2), 300);
}

// ===== Step 0: Update Cart Quantity =====
function updateQuantity(itemId, change) {
    console.log('Update quantity for item ' + itemId + ' by ' + change);
    showToast('Quantity updated. (Session sync coming soon)');
}

// ===== Step 2: Delivery schedule helpers =====
function selectScheduleDate(el, type) {
    el.classList.add('selected');
}

function selectScheduleTime(el, type) {
    el.classList.add('selected');
}

// ===== Step 2: Pickup schedule helpers =====
function selectPickupDate(el, type) {
    document.querySelectorAll('#step2-pickup .schedule-option').forEach(opt => {
        const label = opt.closest('div')?.previousElementSibling?.textContent?.trim();
        if (label === 'DATE') opt.classList.remove('selected');
    });
    el.classList.add('selected');
}

function selectPickupTime(el, type) {
    document.querySelectorAll('#step2-pickup .schedule-option').forEach(opt => {
        const label = opt.closest('div')?.previousElementSibling?.textContent?.trim();
        if (label === 'TIME') opt.classList.remove('selected');
    });
    el.classList.add('selected');
}

// ===== Step 3: Populate dynamic totals =====
function populateStep3() {
    const fee = orderMethod === 'delivery' ? deliveryFee : 0;
    const total = subtotal + fee;

    const feeRow = document.getElementById('step3FeeRow');
    feeRow.style.display = orderMethod === 'pickup' ? 'none' : '';
    document.getElementById('step3FeeAmount').textContent = '₱' + fee.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('step3TotalBill').textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('step3Downpayment').textContent = '₱' + (total * 0.5).toLocaleString('en-PH', { minimumFractionDigits: 2 });
}

// ===== Step 3: File Upload =====
function handleFileUpload(input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        showToast('File too large. Max 5MB.');
        input.value = '';
        return;
    }

    const allowed = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!allowed.includes(file.type)) {
        showToast('Please upload JPG, PNG, or PDF only.');
        input.value = '';
        return;
    }

    document.getElementById('uploadPlaceholder').classList.add('hidden');
    document.getElementById('uploadSuccess').classList.remove('hidden');
    document.getElementById('uploadFileName').textContent = file.name;
    document.getElementById('uploadArea').classList.add('has-file');
}

// ===== Step 4: Populate Review =====
function populateReview() {
    const fee = orderMethod === 'delivery' ? deliveryFee : 0;
    const total = subtotal + fee;
    const fmt = v => '₱' + v.toLocaleString('en-PH', { minimumFractionDigits: 2 });

    // Method label
    document.getElementById('reviewMethod').textContent = orderMethod === 'delivery' ? 'Delivery' : 'Pickup';

    // Section heading and badges
    if (orderMethod === 'delivery') {
        document.getElementById('reviewDetailsLabel').textContent  = 'Delivery Details';
        document.getElementById('reviewAddressBadge').textContent  = 'ADDRESS';
        document.getElementById('reviewArrivalBadge').textContent  = 'ESTIMATED ARRIVAL';
        document.getElementById('reviewAddress').textContent =
            document.getElementById('deliveryAddress').value.trim() || '-';
    } else {
        document.getElementById('reviewDetailsLabel').textContent  = 'Pickup Details';
        document.getElementById('reviewAddressBadge').textContent  = 'LOCATION';
        document.getElementById('reviewArrivalBadge').textContent  = 'SCHEDULED PICKUP';
        document.getElementById('reviewAddress').textContent = "Lola's Kusina Store — 123 Maligaya St., Quezon City";
    }

    // Schedule text
    const isPickup = orderMethod === 'pickup';
    const dateInput = document.getElementById(isPickup ? 'pickupCustomDate' : 'customDate');
    const timeInput = document.getElementById(isPickup ? 'pickupCustomTime' : 'customTime');
    const defaultWait = isPickup ? '~ 30 mins' : '~ 45 mins';
    let schedText = 'Today, ASAP (' + defaultWait + ')';
    if (dateInput.value || timeInput.value) {
        const d = dateInput.value
            ? new Date(dateInput.value + 'T00:00:00').toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'Today';
        const t = timeInput.value || 'ASAP';
        schedText = d + ', ' + t;
    }
    document.getElementById('reviewArrival').textContent = schedText;

    // Totals
    document.getElementById('reviewSubtotal').textContent   = fmt(subtotal);
    document.getElementById('reviewDeliveryFee').textContent = fmt(fee);
    document.getElementById('reviewTotal').textContent      = fmt(total);
    document.getElementById('reviewDownpayment').textContent = fmt(total * 0.5);

    // Show/hide delivery fee row
    document.getElementById('reviewFeeRow').style.display = orderMethod === 'pickup' ? 'none' : '';
}

// ===== Place Order =====
function placeOrder() {
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Processing...';

    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let ref = 'LK-' + new Date().getFullYear() + '-';
    for (let i = 0; i < 5; i++) ref += chars.charAt(Math.floor(Math.random() * chars.length));

    setTimeout(() => {
        const fee   = orderMethod === 'delivery' ? deliveryFee : 0;
        const total = subtotal + fee;
        const address = orderMethod === 'delivery'
            ? document.getElementById('deliveryAddress').value
            : 'Lola\'s Kusina Store — Pickup';

        const params = new URLSearchParams({
            ref:     ref,
            total:   total,
            method:  orderMethod,
            name:    document.getElementById('contactName').value,
            address: address
        });
        window.location.href = BASE_PATH + '/order_confirmation.php?' + params.toString();
    }, 1200);
}

// ===== Utility =====
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-lg z-[100] text-sm font-medium';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
