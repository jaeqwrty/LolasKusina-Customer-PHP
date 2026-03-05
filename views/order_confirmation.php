<?php
// Order Confirmation View
$pageTitle = "Order Confirmation";
$currentPage = "order";

// Get order ID from URL
$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    header('Location: /');
    exit();
}

include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-2xl mb-20 md:mb-8">
    <!-- Success Icon -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-4">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h1>
        <p class="text-gray-600">Thank you for your order</p>
    </div>

    <!-- Order Number -->
    <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-6 text-white text-center mb-6 shadow-lg">
        <p class="text-sm opacity-90 mb-2">Order Number</p>
        <h2 class="text-3xl font-bold">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></h2>
    </div>

    <!-- Order Status -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Status</h3>
        
        <div class="space-y-4">
            <!-- Timeline -->
            <div class="flex items-start">
                <div class="flex items-center flex-col mr-4">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="w-1 h-12 bg-gray-300"></div>
                </div>
                <div class="flex-1 pt-2">
                    <h4 class="font-semibold text-gray-800">Order Placed</h4>
                    <p class="text-sm text-gray-500">Your order has been received</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex items-center flex-col mr-4">
                    <div class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="w-1 h-12 bg-gray-300"></div>
                </div>
                <div class="flex-1 pt-2">
                    <h4 class="font-semibold text-gray-500">Preparing Order</h4>
                    <p class="text-sm text-gray-400">We're preparing your order</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex items-center flex-col mr-4">
                    <div class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                        </svg>
                    </div>
                    <div class="w-1 h-12 bg-gray-300"></div>
                </div>
                <div class="flex-1 pt-2">
                    <h4 class="font-semibold text-gray-500">Out for Delivery</h4>
                    <p class="text-sm text-gray-400">Your order is on the way</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex items-center flex-col mr-4">
                    <div class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 pt-2">
                    <h4 class="font-semibold text-gray-500">Delivered</h4>
                    <p class="text-sm text-gray-400">Order delivered successfully</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Delivery Information</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Estimated Delivery:</span>
                <span class="font-semibold text-gray-800"><?php echo date('M d, Y h:i A', strtotime('+2 hours')); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Payment Method:</span>
                <span class="font-semibold text-gray-800">Cash on Delivery</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Total Amount:</span>
                <span class="font-bold text-primary text-lg">₱2,620.00</span>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Need Help?</h4>
                <p class="text-sm text-gray-600">Contact us at <strong>0917-123-4567</strong> or email <strong>support@lolaskusina.com</strong></p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-3">
        <a href="/" class="block w-full bg-primary text-white py-4 rounded-xl font-bold text-center hover:bg-orange-600 transition shadow-lg">
            CONTINUE SHOPPING
        </a>
        <button onclick="window.print()" class="block w-full bg-white text-primary border-2 border-primary py-4 rounded-xl font-bold hover:bg-orange-50 transition">
            PRINT RECEIPT
        </button>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
