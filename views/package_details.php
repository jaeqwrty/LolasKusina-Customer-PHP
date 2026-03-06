<?php
// Package Details View
// Note: session_start() is already called in public/index.php

// Load package data from DB or fall back to static sample
$package = null;
$items = [];

$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($packageId > 0) {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Package.php';
    try {
        $db = new Database();
        $packageModel = new Package($db);
        $package = $packageModel->getPackageById($packageId);
        if ($package) {
            $items = $packageModel->getPackageItems($packageId);
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
include __DIR__ . '/layouts/header.php';
?>

<!-- Hero image: edge-to-edge, sits behind the fixed top bar -->
<div class="relative w-full bg-orange-50" style="min-height:260px;">
    <img
        src="<?php echo BASE_PATH; ?>/images/<?php echo htmlspecialchars($package['image'] ?? 'placeholder.svg'); ?>"
        alt="<?php echo htmlspecialchars($package['name']); ?>"
        class="w-full object-cover"
        style="height:260px;"
        onerror="this.onerror=null;this.onerror=null;this.src='<?php echo BASE_PATH; ?>/images/placeholder.svg'"
    >

    <!-- Gradient overlay so text is readable -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

    <!-- Back button -->
    <button onclick="goBack()" class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md touch-feedback">
        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <!-- Action buttons: favourite + share -->
    <div class="absolute top-4 right-4 flex space-x-2">
        <button id="favBtn" onclick="toggleFavourite()" class="bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md touch-feedback">
            <svg id="favIcon" class="w-5 h-5 text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        <button onclick="sharePackage()" class="bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-md touch-feedback">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
        </button>
    </div>

    <!-- Badge (Best Seller / Popular / New) -->
    <?php if (!empty($package['badge'])): ?>
    <span class="absolute bottom-4 left-4 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
        🔥 <?php echo htmlspecialchars($package['badge']); ?>
    </span>
    <?php endif; ?>
</div>

<!-- Page content -->
<div class="container mx-auto px-4 md:px-8 max-w-md md:max-w-2xl pb-36">

    <!-- ── Package title + price ─────────────────────────── -->
    <div class="mt-5 mb-2 flex items-start justify-between gap-3">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800 leading-tight">
                <?php echo htmlspecialchars($package['name']); ?>
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <?php echo htmlspecialchars($package['description']); ?>
            </p>
        </div>
        <span class="text-2xl font-extrabold text-primary shrink-0">
            ₱<?php echo number_format((float)($package['price'] ?? 0), 2); ?>
        </span>
    </div>

    <!-- ── Rating + pax badge ────────────────────────────── -->
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <!-- Stars -->
        <div class="flex items-center space-x-1">
            <?php
            $rating  = (float)($package['rating'] ?? 0);
            $reviews = (int)($package['reviews'] ?? 0);
            $full    = (int)floor($rating);
            $half    = ($rating - $full) >= 0.5;
            for ($s = 1; $s <= 5; $s++):
                if ($s <= $full): ?>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                <?php elseif ($half && $s === $full + 1): ?>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <defs><clipPath id="half"><rect x="0" y="0" width="10" height="20"/></clipPath></defs>
                        <path clip-path="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        <path fill="#D1D5DB" clip-path="url(#half)" transform="scale(-1,1) translate(-20,0)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                <?php else: ?>
                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                <?php endif;
            endfor; ?>
            <span class="text-sm font-semibold text-gray-700 ml-1"><?php echo $rating; ?></span>
            <span class="text-xs text-gray-500">(<?php echo $reviews; ?> reviews)</span>
        </div>

        <!-- Pax pill -->
        <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full">
            👨‍👩‍👧‍👦 <?php echo htmlspecialchars($package['description']); ?>
        </span>
    </div>

    <!-- ── About / details ───────────────────────────────── -->
    <?php if (!empty($package['details'])): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
        <h2 class="text-base font-semibold text-gray-800 mb-1">About this Package</h2>
        <p class="text-sm text-gray-600 leading-relaxed">
            <?php echo htmlspecialchars($package['details']); ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- ── What's Included ───────────────────────────────── -->
    <?php if (!empty($items)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-800">Mga Kasama sa Package</h2>
            <span class="text-xs font-medium text-primary bg-orange-50 px-2 py-0.5 rounded-full">
                <?php echo count($items); ?> items
            </span>
        </div>

        <ul class="divide-y divide-gray-100">
        <?php foreach ($items as $item): ?>
            <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-[#FFF3EE]">
                    <img
                        src="<?php echo BASE_PATH; ?>/images/<?php echo htmlspecialchars($item['image'] ?? 'placeholder.svg'); ?>"
                        alt="<?php echo htmlspecialchars($item['name']); ?>"
                        class="w-full h-full object-cover"
                        onerror="this.onerror=null;this.src='<?php echo BASE_PATH; ?>/images/placeholder.svg'"
                    >
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm leading-tight truncate">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        <?php echo htmlspecialchars($item['description'] ?? $item['item_description'] ?? ''); ?>
                    </p>
                </div>
                <!-- Checkmark -->
                <svg class="w-6 h-6 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ── Quantity selector ─────────────────────────────── -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-gray-800 text-sm">Quantity</p>
                <p class="text-xs text-gray-500">How many packages?</p>
            </div>
            <div class="flex items-center space-x-4">
                <button
                    onclick="changeQty(-1)"
                    class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 active:bg-gray-300 transition touch-feedback"
                    aria-label="Decrease quantity"
                >
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <span id="qtyDisplay" class="text-xl font-bold text-gray-800 w-6 text-center select-none">1</span>
                <button
                    onclick="changeQty(1)"
                    class="w-9 h-9 bg-primary rounded-full flex items-center justify-center hover:bg-orange-600 active:bg-orange-700 transition touch-feedback"
                    aria-label="Increase quantity"
                >
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ── Reviews shortcut ──────────────────────────────── -->
    <a href="<?php echo BASE_PATH; ?>/reviews.php?package=<?php echo (int)($package['id'] ?? 0); ?>"
       class="flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-100 px-4 py-3 mb-4 touch-feedback hover:bg-gray-50 transition">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="text-sm font-semibold text-gray-800">
                <?php echo $rating; ?> &nbsp;·&nbsp; <?php echo $reviews; ?> Reviews
            </span>
        </div>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

</div><!-- /container -->

<!-- ── Sticky Add-to-Cart bar ────────────────────────────── -->
<div class="fixed bottom-20 md:bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 safe-bottom z-40 md:z-50 md:relative md:border-0 md:bg-transparent md:px-8 md:pb-8">
    <div class="max-w-md md:max-w-2xl mx-auto flex items-center gap-3">
        <!-- Price summary -->
        <div class="flex flex-col leading-tight">
            <span class="text-xs text-gray-500">Total</span>
            <span id="totalDisplay" class="text-lg font-extrabold text-primary"></span>
        </div>

        <!-- Add to cart button -->
        <button
            id="addToCartBtn"
            onclick="addToCart()"
            class="flex-1 bg-primary text-white py-3.5 rounded-2xl font-bold text-base shadow-lg hover:bg-orange-600 active:bg-orange-700 transition touch-feedback"
        >
            ADD TO CART
        </button>
    </div>
</div>

<!-- Toast notification -->
<div id="toast"
     class="fixed top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-5 py-2.5 rounded-full shadow-xl z-[100] opacity-0 pointer-events-none transition-all duration-300 whitespace-nowrap">
    ✓ Added to cart!
</div>

<script>
const BASE_PRICE  = <?php echo (float)($package['price'] ?? 0); ?>;
const PACKAGE_ID  = <?php echo (int)($package['id'] ?? 0); ?>;
const PACKAGE_NAME = <?php echo json_encode($package['name'] ?? ''); ?>;
const PACKAGE_IMG  = <?php echo json_encode($package['image'] ?? 'placeholder.svg'); ?>;

let qty = 1;

// ── Navigation ────────────────────────────────────────
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = BASE_PATH || '/';
    }
}

function fmt(n) {
    return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function changeQty(delta) {
    qty = Math.max(1, Math.min(99, qty + delta));
    document.getElementById('qtyDisplay').textContent = qty;
    document.getElementById('totalDisplay').textContent = fmt(BASE_PRICE * qty);
}

// Init display
document.getElementById('totalDisplay').textContent = fmt(BASE_PRICE);

// ── Favourite toggle ──────────────────────────────────────
function toggleFavourite() {
    const icon = document.getElementById('favIcon');
    const isFav = icon.getAttribute('fill') === 'currentColor' && icon.classList.contains('text-red-500');

    if (icon.classList.contains('text-red-500')) {
        icon.setAttribute('fill', 'none');
        icon.classList.remove('text-red-500');
        icon.classList.add('text-gray-600');
        showToast('Removed from favourites');
    } else {
        icon.setAttribute('fill', 'currentColor');
        icon.classList.remove('text-gray-600');
        icon.classList.add('text-red-500');
        showToast('❤️ Saved to favourites!');
    }
}

// ── Share ─────────────────────────────────────────────────
function sharePackage() {
    if (navigator.share) {
        navigator.share({
            title: PACKAGE_NAME + " – Lola's Kusina",
            text: 'Check out this package from Lola\'s Kusina!',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('🔗 Link copied!');
        });
    }
}

// ── Add to Cart ───────────────────────────────────────────
function addToCart() {
    // Read existing cart from sessionStorage as a simple JSON array
    let cart = [];
    try { cart = JSON.parse(sessionStorage.getItem('cart') || '[]'); } catch(e) {}

    const existing = cart.find(i => i.id === PACKAGE_ID && i.type === 'package');
    if (existing) {
        existing.quantity += qty;
    } else {
        cart.push({
            id:       PACKAGE_ID,
            type:     'package',
            name:     PACKAGE_NAME,
            image:    PACKAGE_IMG,
            price:    BASE_PRICE,
            quantity: qty
        });
    }
    sessionStorage.setItem('cart', JSON.stringify(cart));

    // Also POST to server-side cart if available
    fetch(BASE_PATH + '/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', item_type: 'package', item_id: PACKAGE_ID, quantity: qty })
    }).catch(() => {}); // Silently fail if endpoint not ready

    showToast('✓ ' + PACKAGE_NAME + ' added to cart!');
    document.getElementById('addToCartBtn').textContent = 'VIEW CART';
    document.getElementById('addToCartBtn').onclick = () => { window.location.href = BASE_PATH + '/cart.php'; };
}

// ── Toast helper ──────────────────────────────────────────
function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.classList.remove('opacity-0');
    toast.classList.add('opacity-100');
    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
    }, 2200);
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
