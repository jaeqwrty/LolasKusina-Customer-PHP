<?php
// Order History Page
$pageTitle = "Order History - Lola's Kusina";
$currentPage = "order";
include __DIR__ . '/layouts/header.php';

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

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-4xl mb-20 md:mb-8">

    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="/profile.php" class="bg-white rounded-full p-2 shadow-md touch-feedback mr-3">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Order History</h1>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 mb-5 overflow-x-auto pb-1 hide-scrollbar">
        <button onclick="filterOrders('all')" class="order-filter-btn active bg-primary text-white px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap" data-filter="all">All Orders</button>
        <button onclick="filterOrders('ongoing')" class="order-filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="ongoing">Ongoing</button>
        <button onclick="filterOrders('delivered')" class="order-filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="delivered">Completed</button>
        <button onclick="filterOrders('cancelled')" class="order-filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="cancelled">Cancelled</button>
    </div>

    <!-- Orders List -->
    <div id="ordersList" class="space-y-4">
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
        <div class="order-card bg-white rounded-2xl shadow-md p-4" data-status="<?php echo $statusFilter; ?>">
            <!-- Order Header -->
            <div class="flex items-start justify-between mb-3">
                <div>
                    <span class="text-xs text-gray-400 font-medium">REF #<?php echo $order['ref']; ?></span>
                    <div class="text-sm font-semibold text-gray-800 mt-0.5"><?php echo $order['date']; ?></div>
                </div>
                <span class="<?php echo $statusColor; ?> text-xs font-bold px-3 py-1 rounded-full">
                    <?php echo $order['status']; ?>
                </span>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100 mb-3"></div>

            <!-- Package Name -->
            <p class="text-sm text-gray-700 font-medium mb-1"><?php echo htmlspecialchars($order['name']); ?></p>

            <!-- Price -->
            <div class="mb-4">
                <span class="text-xs text-gray-500">Total Price</span>
                <div class="text-lg font-bold text-primary">₱<?php echo number_format($order['price'], 2); ?></div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
                <a href="/order_details.php?id=<?php echo $order['id']; ?>"
                   class="flex-1 border-2 border-gray-200 text-gray-700 py-2.5 rounded-xl font-semibold text-sm text-center hover:border-primary hover:text-primary transition touch-feedback">
                    View Details
                </a>
                <?php if ($order['status'] === 'Delivered'): ?>
                <a href="/write_review.php?package=<?php echo $order['id']; ?>"
                   class="flex-1 bg-primary text-white py-2.5 rounded-xl font-semibold text-sm text-center hover:bg-orange-600 transition touch-feedback shadow-sm">
                    Mag-review
                </a>
                <?php else: ?>
                <button class="flex-1 bg-gray-100 text-gray-400 py-2.5 rounded-xl font-semibold text-sm cursor-not-allowed">
                    Mag-review
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty state (hidden by default) -->
    <div id="emptyState" class="hidden text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <p class="text-gray-500 font-medium">Walang orders dito</p>
    </div>

</div>

<script>
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
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
