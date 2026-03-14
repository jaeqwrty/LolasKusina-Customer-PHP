<?php
// Order Status Tracker (FR-C09 prep)
// This view is a placeholder for the order tracking feature.
// Customers will be able to search by reference number to check order status.
$pageTitle = "Track Your Order - Lola's Kusina";
$currentPage = "track";
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-2xl mb-20 md:mb-8">
    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Track Your Order</h2>
            <p class="text-gray-500">Enter your reference number to check your order status.</p>
        </div>
        
        <form method="GET" action="<?php echo BASE_PATH; ?>/track_order.php" class="space-y-4">
            <div>
                <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-1">Reference Number</label>
                <input 
                    type="text" 
                    id="reference_number" 
                    name="ref" 
                    placeholder="e.g. LK-20260314-A7B2"
                    value="<?php echo htmlspecialchars($_GET['ref'] ?? ''); ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition text-lg text-center tracking-widest font-mono"
                    required
                >
            </div>
            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-orange-600 active:bg-orange-700 transition shadow-md">
                Track Order
            </button>
        </form>
        
        <?php if (isset($_GET['ref']) && !empty($_GET['ref'])): ?>
        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-center">
            <p class="text-yellow-700 font-medium">
                <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Order tracking is coming soon. Please contact us directly for order updates.
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
