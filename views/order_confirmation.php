<?php
// Order Received / Confirmation View
$pageTitle = "Order Received";
$currentPage = "order";

// Get order details from URL params
$refNumber = $_GET['ref'] ?? 'LK-' . date('Y') . '-XXXXX';
$totalAmount = $_GET['total'] ?? 0;
$method = $_GET['method'] ?? 'delivery';
$customerName = $_GET['name'] ?? '';
$address = $_GET['address'] ?? '';

include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-6 py-4 max-w-md md:max-w-2xl mb-20 md:mb-8">
    
    <!-- Success Icon -->
    <div class="text-center mb-4 pt-2">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-full mb-4 shadow-lg">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-800 mb-1">SALAMAT!</h1>
        <h2 class="text-lg font-bold text-primary mb-2">ORDER RECEIVED!</h2>
        <p class="text-sm text-gray-500 max-w-xs mx-auto">We've received your request and the kitchen is getting ready.</p>
    </div>

    <!-- Reference Number Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-3 text-center">
        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Reference Number</p>
        <div class="flex items-center justify-center gap-2 mb-3">
            <span id="refNumber" class="text-xl font-bold font-mono text-gray-800 tracking-wider"><?php echo htmlspecialchars($refNumber); ?></span>
            <button onclick="copyRef()" class="p-1.5 bg-gray-100 rounded-lg hover:bg-gray-200 transition" title="Copy">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
        </div>
        
        <!-- Status Badge -->
        <div class="inline-flex items-center gap-1.5 bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm font-semibold px-4 py-1.5 rounded-full">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
            Pending Approval
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3.5 mb-3">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Total Amount</span>
            <span class="text-lg font-bold text-primary">₱<?php echo number_format((float)$totalAmount, 2); ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Estimated Time</span>
            <span class="text-sm font-semibold text-gray-800">15–20 mins</span>
        </div>
    </div>

    <!-- Track Order CTA -->
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-4 mb-3 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <h3 class="font-bold text-gray-800 text-sm">GUSTO MO BA I-TRACK ANG ORDER?</h3>
        </div>
        <p class="text-xs text-gray-500 mb-3">Create an account to see real-time updates and earn points for your next meal!</p>
        <a href="<?php echo BASE_PATH; ?>/auth_gate.php" class="inline-block w-full bg-primary text-white py-2.5 rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-md">
            GUMAWA NG ACCOUNT →
        </a>
    </div>

    <!-- Back to Home -->
    <a href="<?php echo BASE_PATH ?: '/'; ?>" class="block text-center text-sm text-gray-500 font-medium hover:text-primary transition py-2">
        Bumalik sa Home
    </a>
</div>

<script>
function copyRef() {
    const ref = document.getElementById('refNumber').textContent;
    navigator.clipboard.writeText(ref).then(() => {
        showToast('Reference number copied!');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = ref;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Reference number copied!');
    });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-lg z-[100] text-sm font-medium';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
