<?php
// Cart / Multi-Step Checkout View
require_once __DIR__ . '/../config/google_matrix.php';

$pageTitle = "My Cart";
$currentPage = "order";
$cartCount = count($_SESSION['cart'] ?? []);
$matrixConfig = getGoogleMatrixConfig();
$storeAddress = (string) ($matrixConfig['store_address'] ?? '');
$storeMapUrl = (string) ($matrixConfig['store_map_url'] ?? 'https://www.google.com/maps');

$storeLat = 7.4471598;
$storeLng = 125.823198;
if (preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', $storeAddress, $coordMatches)) {
    $storeLat = (float) $coordMatches[1];
    $storeLng = (float) $coordMatches[2];
}

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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

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
    .schedule-pill {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #ffffff;
        padding: 0.6rem 0.7rem;
        text-align: left;
    }
    .schedule-option.selected.schedule-pill {
        background: #FFF7ED;
        border-color: #FF6B35;
        color: #C2410C;
    }
    .schedule-option.selected .schedule-sub { color: #EA580C; }
    .schedule-shell {
        border: 1px solid #F1F5F9;
        background: #F8FAFC;
        border-radius: 0.9rem;
        padding: 0.75rem;
    }

    /* Interactive maps */
    .checkout-map {
        height: 220px;
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    /* Compact layout tuning for the order flow */
    .order-flow-compact .checkout-step .bg-white.rounded-2xl {
        border-radius: 1rem;
    }

    .order-flow-compact .checkout-step .p-4 {
        padding: 0.9rem;
    }

    .order-flow-compact .checkout-step .mb-4 {
        margin-bottom: 0.75rem;
    }

    .order-flow-compact .checkout-step .mb-6 {
        margin-bottom: 1rem;
    }

    .order-flow-compact .checkout-step .py-4 {
        padding-top: 0.8rem;
        padding-bottom: 0.8rem;
    }

    .order-flow-compact .checkout-step .text-xl {
        font-size: 1.125rem;
        line-height: 1.5rem;
    }

    .order-flow-compact .checkout-step .text-lg {
        font-size: 1rem;
        line-height: 1.35rem;
    }

    .order-flow-compact .checkout-step .text-sm {
        line-height: 1.3rem;
    }
</style>

<div class="order-flow-compact container mx-auto px-4 md:px-6 py-3 max-w-md md:max-w-4xl mb-20 md:mb-8">
    
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
        <p class="text-sm text-gray-500 mb-4">Review your items before checkout</p>

        <div class="md:grid md:grid-cols-[1.6fr_1fr] md:gap-4">
            <div>
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
            </div>

            <div class="md:sticky md:top-24 self-start">
                <!-- Cart Summary -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
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
                <div class="space-y-2.5">
                    <button onclick="goToStep(1)" class="w-full bg-primary text-white py-3 rounded-xl font-bold text-base shadow-lg hover:bg-orange-600 transition flex items-center justify-center gap-2">
                        PROCEED TO CHECKOUT
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button onclick="continueShopping()" class="w-full bg-white text-primary py-2.5 rounded-xl font-bold border-2 border-primary hover:bg-orange-50 transition">
                        CONTINUE SHOPPING
                    </button>
                </div>

                <p class="text-center text-xs text-gray-400 mt-4">May problema sa order? <a href="#" class="text-primary font-semibold">Tumawag sa amin.</a></p>
            </div>
        </div>
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
            
            <!-- Interactive delivery map -->
            <div id="deliveryMap" class="checkout-map mb-3"></div>
            <div class="text-xs text-gray-500 mb-2">
                Tap the map to pin the exact delivery location.
            </div>
            <a href="<?php echo htmlspecialchars($storeMapUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-primary text-sm font-semibold hover:underline inline-block">Open in Google Maps</a>
        </div>

        <!-- Distance & Fee (delivery only) -->
        <div id="deliveryFeeSection" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <div class="mb-3">
                <label class="text-sm text-gray-600 font-medium mb-1 block">Distance (km)</label>
                <div class="flex items-center gap-2">
                    <input id="distanceInput" type="text" value="0.00" readonly class="flex-1 px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700 focus:outline-none">
                    <span class="text-sm text-gray-600 font-medium whitespace-nowrap">km</span>
                </div>
                <button type="button" onclick="calculateDeliveryFee()" class="mt-2 text-primary text-sm font-semibold hover:underline">Calculate from address</button>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/></svg>
                    <span class="text-sm text-gray-600">Delivery Fee</span>
                </div>
                <span class="font-bold text-primary text-lg">₱<span id="deliveryFeeAmount">0.00</span></span>
            </div>
            <p id="deliveryFeeMeta" class="text-xs text-gray-400 mt-2">Fee will be computed via Google Maps distance.</p>
        </div>

        <!-- Schedule Delivery -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">Schedule Delivery</h3>
            <div class="schedule-shell mb-3">
                <p class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide mb-2">Quick Date</p>
                <div class="grid grid-cols-2 gap-2" id="deliveryDateQuick">
                    <button type="button" id="deliveryDateToday" class="schedule-option schedule-pill selected" onclick="selectScheduleDate(this, 'today')">
                        <p class="text-sm font-bold">Today</p>
                        <p class="schedule-sub text-[11px] text-gray-500"><?php echo date('M d, Y'); ?></p>
                    </button>
                    <button type="button" id="deliveryDateTomorrow" class="schedule-option schedule-pill" onclick="selectScheduleDate(this, 'tomorrow')">
                        <p class="text-sm font-bold">Tomorrow</p>
                        <p class="schedule-sub text-[11px] text-gray-500"><?php echo date('M d, Y', strtotime('+1 day')); ?></p>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">Delivery Date</label>
                    <input type="date" id="customDate" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">Delivery Time</label>
                    <input type="time" id="customTime" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary" step="300">
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-3">Schedule must be later than the current time.</p>

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
                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($storeAddress, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <!-- Interactive pickup map -->
            <div id="pickupMap" class="checkout-map mb-3"></div>
            <a href="<?php echo htmlspecialchars($storeMapUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-primary text-sm font-semibold hover:underline inline-block mb-2">Open in Google Maps</a>
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg p-3">
                <svg class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="text-xs text-green-700 font-semibold">Walang delivery fee — FREE pickup!</span>
            </div>
        </div>

        <!-- Schedule Pickup -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">Schedule Pickup</h3>
            <div class="schedule-shell mb-3">
                <p class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide mb-2">Quick Date</p>
                <div class="grid grid-cols-2 gap-2" id="pickupDateQuick">
                    <button type="button" id="pickupDateToday" class="schedule-option schedule-pill selected" onclick="selectPickupDate(this, 'today')">
                        <p class="text-sm font-bold">Today</p>
                        <p class="schedule-sub text-[11px] text-gray-500"><?php echo date('M d, Y'); ?></p>
                    </button>
                    <button type="button" id="pickupDateTomorrow" class="schedule-option schedule-pill" onclick="selectPickupDate(this, 'tomorrow')">
                        <p class="text-sm font-bold">Tomorrow</p>
                        <p class="schedule-sub text-[11px] text-gray-500"><?php echo date('M d, Y', strtotime('+1 day')); ?></p>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">Pickup Date</label>
                    <input type="date" id="pickupCustomDate" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-medium mb-1 block">Pickup Time</label>
                    <input type="time" id="pickupCustomTime" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary" step="300">
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-3">Schedule must be later than the current time.</p>
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
                <span id="step3FeeAmount" class="font-semibold text-gray-800">₱0.00</span>
            </div>
            <div class="border-t border-gray-100 pt-2 mt-1 flex justify-between items-center mb-2">
                <span class="text-sm font-bold text-gray-700">Total Bill</span>
                <span id="step3TotalBill" class="font-bold text-gray-800 text-lg">₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Downpayment Required (50%)</span>
                <span id="step3Downpayment" class="font-bold text-primary">₱<?php echo number_format($subtotal * 0.5, 2); ?></span>
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
                    <span id="reviewDeliveryFee" class="font-semibold text-gray-800">₱0.00</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2"></div>
                <div class="flex justify-between text-lg">
                    <span class="font-bold text-gray-800">Total Amount</span>
                    <span id="reviewTotal" class="font-bold text-primary">₱<?php echo number_format($subtotal, 2); ?></span>
                </div>
            </div>

            <!-- 50% Downpayment -->
            <div class="mt-3 bg-orange-50 border border-orange-200 rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">50% Downpayment</span>
                    <span id="reviewDownpayment" class="font-bold text-primary">₱<?php echo number_format($subtotal * 0.5, 2); ?></span>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
// ===== State =====
let currentStep = 0; // Start at cart view (step 0)
let orderMethod = 'delivery'; // 'delivery' or 'pickup'
let deliveryFee = 0;
let distanceKm = 0;
let deliveryMap = null;
let pickupMap = null;
let deliveryMarker = null;
let selectedDeliveryCoordinates = '';
const subtotal = <?php echo $subtotal; ?>;
const STORE_ADDRESS = <?php echo json_encode($storeAddress, JSON_UNESCAPED_UNICODE); ?>;
const STORE_LAT = <?php echo json_encode($storeLat); ?>;
const STORE_LNG = <?php echo json_encode($storeLng); ?>;
const DELIVERY_FEE_API = (BASE_PATH || '') + '/api/delivery-fee';
const REVERSE_GEOCODE_API = (BASE_PATH || '') + '/api/reverse-geocode';

function initMaps() {
    if (typeof L === 'undefined') return;

    if (!deliveryMap) {
        deliveryMap = L.map('deliveryMap').setView([STORE_LAT, STORE_LNG], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(deliveryMap);

        const storePin = L.marker([STORE_LAT, STORE_LNG]).addTo(deliveryMap);
        storePin.bindPopup('Lola\'s Kusina (Store)').openPopup();

        deliveryMap.on('click', function (event) {
            const lat = Number(event.latlng.lat).toFixed(6);
            const lng = Number(event.latlng.lng).toFixed(6);
            selectedDeliveryCoordinates = lat + ',' + lng;

            if (deliveryMarker) {
                deliveryMap.removeLayer(deliveryMarker);
            }
            deliveryMarker = L.marker([lat, lng]).addTo(deliveryMap);
            deliveryMarker.bindPopup('Pinned delivery location').openPopup();

            document.getElementById('deliveryAddress').value = 'Loading address...';
            reverseGeocodeAndPopulateAddress(lat, lng);
            calculateDeliveryFee();
        });
    }

    if (!pickupMap) {
        pickupMap = L.map('pickupMap').setView([STORE_LAT, STORE_LNG], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(pickupMap);

        const marker = L.marker([STORE_LAT, STORE_LNG]).addTo(pickupMap);
        marker.bindPopup('Lola\'s Kusina (Pickup Location)').openPopup();
    }
}

function ensureMapLayout() {
    if (deliveryMap) deliveryMap.invalidateSize();
    if (pickupMap) pickupMap.invalidateSize();
}

async function reverseGeocodeAndPopulateAddress(lat, lng) {
    const addressInput = document.getElementById('deliveryAddress');

    try {
        const response = await fetch(REVERSE_GEOCODE_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            },
            body: new URLSearchParams({ lat: String(lat), lng: String(lng) }).toString(),
        });

        const payload = await response.json();
        if (!response.ok || !payload.success) {
            throw new Error(payload.message || 'Unable to convert pin location into address.');
        }

        addressInput.value = payload.data.address || selectedDeliveryCoordinates;
    } catch (error) {
        addressInput.value = selectedDeliveryCoordinates;
        showToast(error.message || 'Could not fetch address for pinned location.');
    }
}

// Calculate delivery fee via backend Google Matrix endpoint
async function calculateDeliveryFee() {
    const addressInput = document.getElementById('deliveryAddress');
    const destination = selectedDeliveryCoordinates || (addressInput.value || '').trim();
    const feeMeta = document.getElementById('deliveryFeeMeta');

    if (!destination) {
        showToast('Please enter a delivery address first.');
        addressInput.focus();
        return;
    }

    feeMeta.textContent = 'Calculating fee...';

    try {
        const response = await fetch(DELIVERY_FEE_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            },
            body: new URLSearchParams({ destination }).toString(),
        });

        const payload = await response.json();
        if (!response.ok || !payload.success) {
            throw new Error(payload.message || 'Unable to calculate delivery fee.');
        }

        const data = payload.data;
        distanceKm = Number(data.distance_km || 0);
        deliveryFee = Number(data.delivery_fee || 0);

        document.getElementById('distanceInput').value = distanceKm.toFixed(2);
        document.getElementById('deliveryFeeAmount').textContent = deliveryFee.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        feeMeta.textContent = 'Distance: ' + (data.distance_text || (distanceKm.toFixed(2) + ' km')) + ' • ETA: ' + (data.duration_text || 'N/A');

        if (currentStep === 3) populateStep3();
        if (currentStep === 4) populateReview();
    } catch (error) {
        distanceKm = 0;
        deliveryFee = 0;
        document.getElementById('distanceInput').value = '0.00';
        document.getElementById('deliveryFeeAmount').textContent = '0.00';
        feeMeta.textContent = 'Fee calculation unavailable. Please verify address and try again.';
        showToast(error.message || 'Unable to calculate delivery fee.');
    }
}

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

    if (step === 2) {
        setTimeout(ensureMapLayout, 120);
    }

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
        const selectedDate = document.getElementById('customDate').value;
        const selectedTime = document.getElementById('customTime').value;
        if (!address) {
            showToast('Please enter a delivery address');
            document.getElementById('deliveryAddress').focus();
            return false;
        }
        if (deliveryFee <= 0 || distanceKm <= 0) {
            showToast('Please calculate delivery fee first.');
            return false;
        }
        if (!selectedDate) {
            showToast('Please select a delivery date.');
            document.getElementById('customDate').focus();
            return false;
        }
        if (!selectedTime) {
            showToast('Please select a delivery time.');
            document.getElementById('customTime').focus();
            return false;
        }

        const scheduledAt = new Date(selectedDate + 'T' + selectedTime + ':00');
        if (Number.isNaN(scheduledAt.getTime())) {
            showToast('Invalid delivery date/time.');
            return false;
        }

        if (scheduledAt <= new Date()) {
            showToast('Delivery schedule must be later than the current date/time.');
            return false;
        }
    }
    if (step === 2 && orderMethod === 'pickup') {
        const selectedDate = document.getElementById('pickupCustomDate').value;
        const selectedTime = document.getElementById('pickupCustomTime').value;

        if (!selectedDate) {
            showToast('Please select a pickup date.');
            document.getElementById('pickupCustomDate').focus();
            return false;
        }
        if (!selectedTime) {
            showToast('Please select a pickup time.');
            document.getElementById('pickupCustomTime').focus();
            return false;
        }

        const scheduledAt = new Date(selectedDate + 'T' + selectedTime + ':00');
        if (Number.isNaN(scheduledAt.getTime())) {
            showToast('Invalid pickup date/time.');
            return false;
        }

        if (scheduledAt <= new Date()) {
            showToast('Pickup schedule must be later than the current date/time.');
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
    if (method === 'pickup') {
        deliveryFee = 0;
        distanceKm = 0;
        selectedDeliveryCoordinates = '';
        document.getElementById('distanceInput').value = '0.00';
        document.getElementById('deliveryFeeAmount').textContent = '0.00';
        document.getElementById('deliveryFeeMeta').textContent = 'No delivery fee for pickup.';
    } else {
        document.getElementById('deliveryFeeMeta').textContent = 'Fee will be computed via Google Maps distance.';
        calculateDeliveryFee();
    }

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

function getLocalDateString(offsetDays = 0) {
    const date = new Date();
    date.setHours(0, 0, 0, 0);
    date.setDate(date.getDate() + offsetDays);
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return date.getFullYear() + '-' + month + '-' + day;
}

function updateQuickDateState(inputId, todayId, tomorrowId) {
    const input = document.getElementById(inputId);
    const todayChip = document.getElementById(todayId);
    const tomorrowChip = document.getElementById(tomorrowId);
    if (!input || !todayChip || !tomorrowChip) return;

    const today = getLocalDateString(0);
    const tomorrow = getLocalDateString(1);
    todayChip.classList.toggle('selected', input.value === today);
    tomorrowChip.classList.toggle('selected', input.value === tomorrow);
}

function enforceMinTimeForDate(dateInputId, timeInputId) {
    const dateInput = document.getElementById(dateInputId);
    const timeInput = document.getElementById(timeInputId);
    if (!dateInput || !timeInput) return;

    const selectedDate = dateInput.value;
    const today = getLocalDateString(0);
    if (selectedDate === today) {
        const minDate = new Date(Date.now() + (5 * 60 * 1000));
        const minHours = String(minDate.getHours()).padStart(2, '0');
        const minMinutes = String(minDate.getMinutes()).padStart(2, '0');
        const minTime = minHours + ':' + minMinutes;
        timeInput.min = minTime;
        if (timeInput.value && timeInput.value < minTime) {
            timeInput.value = '';
        }
        return;
    }

    timeInput.min = '00:00';
}

// ===== Step 2: Delivery schedule helpers =====
function selectScheduleDate(el, type) {
    const dateInput = document.getElementById('customDate');
    if (!dateInput) return;

    if (type === 'today') {
        dateInput.value = getLocalDateString(0);
    } else if (type === 'tomorrow') {
        dateInput.value = getLocalDateString(1);
    }

    updateQuickDateState('customDate', 'deliveryDateToday', 'deliveryDateTomorrow');
    enforceMinTimeForDate('customDate', 'customTime');
}

// ===== Step 2: Pickup schedule helpers =====
function selectPickupDate(el, type) {
    const dateInput = document.getElementById('pickupCustomDate');
    if (!dateInput) return;

    if (type === 'today') {
        dateInput.value = getLocalDateString(0);
    } else if (type === 'tomorrow') {
        dateInput.value = getLocalDateString(1);
    }

    updateQuickDateState('pickupCustomDate', 'pickupDateToday', 'pickupDateTomorrow');
    enforceMinTimeForDate('pickupCustomDate', 'pickupCustomTime');
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
        document.getElementById('reviewAddress').textContent = "Lola's Kusina Store — " + STORE_ADDRESS;
    }

    // Schedule text
    const isPickup = orderMethod === 'pickup';
    const dateInput = document.getElementById(isPickup ? 'pickupCustomDate' : 'customDate');
    const timeInput = document.getElementById(isPickup ? 'pickupCustomTime' : 'customTime');
    const defaultWait = isPickup ? '~ 30 mins' : '~ 45 mins';
    let schedText = '-';
    if (dateInput.value && timeInput.value) {
        const d = dateInput.value
            ? new Date(dateInput.value + 'T00:00:00').toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'Today';
        const t = timeInput.value;
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

document.addEventListener('DOMContentLoaded', function () {
    initMaps();
    const addressInput = document.getElementById('deliveryAddress');
    const customDateInput = document.getElementById('customDate');
    const pickupCustomDateInput = document.getElementById('pickupCustomDate');
    if (addressInput) {
        addressInput.addEventListener('input', function () {
            selectedDeliveryCoordinates = '';
        });
    }

    if (customDateInput) {
        if (!customDateInput.value) {
            customDateInput.value = getLocalDateString(0);
        }
        updateQuickDateState('customDate', 'deliveryDateToday', 'deliveryDateTomorrow');
        enforceMinTimeForDate('customDate', 'customTime');

        customDateInput.addEventListener('change', function () {
            updateQuickDateState('customDate', 'deliveryDateToday', 'deliveryDateTomorrow');
            enforceMinTimeForDate('customDate', 'customTime');
        });
    }

    if (pickupCustomDateInput) {
        if (!pickupCustomDateInput.value) {
            pickupCustomDateInput.value = getLocalDateString(0);
        }
        updateQuickDateState('pickupCustomDate', 'pickupDateToday', 'pickupDateTomorrow');
        enforceMinTimeForDate('pickupCustomDate', 'pickupCustomTime');

        pickupCustomDateInput.addEventListener('change', function () {
            updateQuickDateState('pickupCustomDate', 'pickupDateToday', 'pickupDateTomorrow');
            enforceMinTimeForDate('pickupCustomDate', 'pickupCustomTime');
        });
    }

    setTimeout(ensureMapLayout, 120);
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
