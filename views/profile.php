<?php
// Auth guard — redirect unauthenticated users to the auth gate
require_once __DIR__ . '/../config/auth.php';
requireAuth('/profile.php');

// Profile Page
$pageTitle = "My Profile - Lola's Kusina";
$currentPage = "account";
include __DIR__ . '/layouts/header.php';

// Order data for display
$orders = [
    [
        'ref'     => 'PH-88210',
        'date'    => 'Oct 24, 2023 • 12:30 PM',
        'name'    => 'Lechon Package B (Good for 20)',
        'price'   => 8500,
        'status'  => 'Delivered',
        'id'      => 1,
    ],
    [
        'ref'     => 'PH-88195',
        'date'    => 'Sep 12, 2023 • 10:00 AM',
        'name'    => 'Paborito Package (Good for 6-7)',
        'price'   => 2500,
        'status'  => 'Delivered',
        'id'      => 2,
    ],
    [
        'ref'     => 'PH-88310',
        'date'    => 'Mar 5, 2026 • 2:00 PM',
        'name'    => 'Family Fiesta (Good for 10-12)',
        'price'   => 4200,
        'status'  => 'Ongoing',
        'id'      => 3,
    ],
];
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-2xl mb-20 md:mb-8">

    <!-- Header -->
    <h1 class="text-xl font-bold text-gray-800 text-center mb-6">My Profile</h1>

    <!-- Avatar & Name -->
    <div class="flex flex-col items-center mb-6">
        <div class="relative mb-3">
            <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                JD
            </div>
            <button class="absolute bottom-0 right-0 bg-dark text-white rounded-full p-1.5 shadow-md hover:bg-gray-700 transition touch-feedback">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
            </button>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Juan Dela Cruz</h2>
        <p class="text-gray-500 text-sm">+63 917 123 4567</p>
    </div>

    <!-- Update Info Button -->
    <button onclick="document.getElementById('editModal').classList.remove('hidden')"
        class="w-full bg-primary text-white py-4 rounded-xl font-bold text-base hover:bg-orange-600 active:bg-orange-700 transition touch-feedback shadow-md mb-6">
        UPDATE INFO
    </button>

    <!-- Orders Section -->
    <div class="bg-white rounded-2xl shadow-md mb-4 overflow-hidden">
        <!-- My Orders Toggle Button -->
        <button onclick="toggleOrdersSection()" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 touch-feedback transition">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="font-semibold text-gray-800">My Orders</span>
            </div>
            <svg id="ordersToggleIcon" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Orders List (Hidden by default) -->
        <div id="ordersSection" class="hidden border-t border-gray-100 px-4 py-4">
            <!-- Filter Tabs -->
            <div class="flex space-x-2 mb-4 overflow-x-auto pb-1 hide-scrollbar">
                <button onclick="filterOrders('all')" class="order-filter-btn active bg-primary text-white px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap" data-filter="all">All</button>
                <button onclick="filterOrders('ongoing')" class="order-filter-btn bg-white text-gray-700 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="ongoing">Ongoing</button>
                <button onclick="filterOrders('delivered')" class="order-filter-btn bg-white text-gray-700 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="delivered">Completed</button>
                <button onclick="filterOrders('cancelled')" class="order-filter-btn bg-white text-gray-700 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="cancelled">Cancelled</button>
            </div>

            <!-- Orders List -->
            <div id="ordersList" class="space-y-3">
                <?php foreach ($orders as $order): ?>
                <?php
                    $statusColor = match($order['status']) {
                        'Delivered'  => 'bg-green-100 text-green-700',
                        'Ongoing'    => 'bg-blue-100 text-blue-700',
                        'Cancelled'  => 'bg-red-100 text-red-700',
                        default      => 'bg-gray-100 text-gray-700',
                    };
                    $statusFilter = strtolower($order['status']);
                ?>
                <div class="order-card bg-gray-50 rounded-xl p-3 border border-gray-100" data-status="<?php echo $statusFilter; ?>">
                    <!-- Order Header -->
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="text-xs text-gray-400 font-medium">REF #<?php echo $order['ref']; ?></span>
                            <div class="text-xs font-semibold text-gray-700 mt-0.5"><?php echo $order['date']; ?></div>
                        </div>
                        <span class="<?php echo $statusColor; ?> text-xs font-bold px-2.5 py-0.5 rounded-full">
                            <?php echo $order['status']; ?>
                        </span>
                    </div>

                    <!-- Package Name -->
                    <p class="text-xs text-gray-600 font-medium mb-2"><?php echo htmlspecialchars($order['name']); ?></p>

                    <!-- Price -->
                    <div class="mb-3">
                        <span class="text-xs text-gray-500">Total Price</span>
                        <div class="text-sm font-bold text-primary">₱<?php echo number_format($order['price'], 2); ?></div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="<?php echo BASE_PATH; ?>/order_details.php?id=<?php echo $order['id']; ?>"
                           class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-lg font-semibold text-xs text-center hover:border-primary hover:text-primary transition touch-feedback">
                            Details
                        </a>
                        <?php if ($order['status'] === 'Delivered'): ?>
                        <a href="<?php echo BASE_PATH; ?>/write_review.php?package=<?php echo $order['id']; ?>"
                           class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-xs text-center hover:bg-orange-600 transition touch-feedback">
                            Review
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty state (hidden by default) -->
            <div id="emptyState" class="hidden text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-500 font-medium text-sm">Walang orders dito</p>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        <a href="<?php echo BASE_PATH; ?>/reviews.php" class="flex items-center justify-between p-4 hover:bg-gray-50 touch-feedback transition">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
                <span class="font-semibold text-gray-800">My Reviews</span>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>

        <div class="border-t border-gray-100"></div>

        <div class="flex items-center justify-between p-4 hover:bg-gray-50 touch-feedback transition cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                </div>
                <span class="font-semibold text-gray-800">Notifications</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" checked>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>
    </div>

    <!-- Logout -->
    <button class="w-full bg-white border-2 border-red-200 text-red-500 py-4 rounded-xl font-bold text-base hover:bg-red-50 active:bg-red-100 transition touch-feedback">
        Mag-logout
    </button>

</div>

<!-- Edit Profile Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[100] flex items-end justify-center md:items-center">
    <div class="bg-white w-full max-w-[480px] rounded-t-2xl md:rounded-2xl p-6 shadow-xl">
        <h2 class="text-lg font-bold text-gray-800 mb-4">I-Update ang Info</h2>
        <div class="space-y-3 mb-5">
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Full Name</label>
                <input type="text" value="Juan Dela Cruz" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary text-sm">
            </div>
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Phone Number</label>
                <input type="tel" value="+63 917 123 4567" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary text-sm">
            </div>
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Email (optional)</label>
                <input type="email" placeholder="juan@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary text-sm">
            </div>
        </div>
        <div class="flex space-x-3">
            <button onclick="document.getElementById('editModal').classList.add('hidden')"
                class="flex-1 border-2 border-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition touch-feedback">
                Kanselahin
            </button>
            <button onclick="saveProfile()"
                class="flex-1 bg-primary text-white py-3 rounded-xl font-bold hover:bg-orange-600 transition touch-feedback shadow-md">
                I-Save
            </button>
        </div>
    </div>
</div>

<script>
function toggleOrdersSection() {
    const ordersSection = document.getElementById('ordersSection');
    const toggleIcon = document.getElementById('ordersToggleIcon');
    
    ordersSection.classList.toggle('hidden');
    toggleIcon.style.transform = ordersSection.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(90deg)';
}

function filterOrders(filter) {
    document.querySelectorAll('.order-filter-btn').forEach(btn => {
        btn.classList.remove('bg-primary', 'text-white');
        btn.classList.add('bg-white', 'text-gray-700');
    });
    const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'text-gray-700');
        activeBtn.classList.add('bg-primary', 'text-white');
    }

    let visibleCount = 0;
    document.querySelectorAll('.order-card').forEach(card => {
        const status = card.dataset.status;
        const show = filter === 'all' || status === filter;
        card.style.display = show ? 'block' : 'none';
        if (show) visibleCount++;
    });

    document.getElementById('emptyState').classList.toggle('hidden', visibleCount > 0);
}

function saveProfile() {
    document.getElementById('editModal').classList.add('hidden');
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm font-medium';
    toast.textContent = '✓ Profile updated!';
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

// Close modal on backdrop tap
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
