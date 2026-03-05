<?php
// Auth guard — redirect unauthenticated users to the auth gate
if (empty($_SESSION['user_id'])) {
    $qs = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: /auth_gate.php?redirect=' . urlencode('/write_review.php' . $qs));
    exit;
}

// Write a Review Page
$pageTitle = "Mag-Review - Lola's Kusina";
$currentPage = "reviews";
include __DIR__ . '/layouts/header.php';

// Get package id if passed
$packageId = $_GET['package'] ?? 1;
$packages = [
    1 => ['name' => 'Paborito Package', 'image' => 'paborito-package.jpg', 'order' => 'PH-10795'],
    2 => ['name' => 'Family Fiesta', 'image' => 'family-fiesta.jpg', 'order' => 'PH-10800'],
    3 => ['name' => 'Salo-Salo Special', 'image' => 'salo-salo.jpg', 'order' => 'PH-10812'],
];
$package = $packages[$packageId] ?? $packages[1];
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-2xl mb-20 md:mb-8">

    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="/reviews.php" class="bg-white rounded-full p-2 shadow-md touch-feedback mr-3">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Write a Review</h1>
    </div>

    <!-- Package Being Reviewed -->
    <div class="bg-white rounded-2xl shadow-md p-4 mb-5 flex items-center space-x-3">
        <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-[#FFF3EE]">
            <img src="/images/<?php echo $package['image']; ?>"
                 alt="<?php echo htmlspecialchars($package['name']); ?>"
                 class="w-full h-full object-cover"
                 onerror="this.onerror=null;this.src='/images/placeholder.svg'">
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-800 truncate"><?php echo htmlspecialchars($package['name']); ?></h3>
            <div class="flex items-center space-x-2 mt-0.5">
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">DELIVERED</span>
                <span class="text-xs text-gray-400">Order #<?php echo $package['order']; ?></span>
            </div>
        </div>
    </div>

    <!-- Rating Question -->
    <div class="bg-white rounded-2xl shadow-md p-5 mb-4">
        <h2 class="text-base font-bold text-gray-800 text-center mb-4">Kumusta ang iyong order?</h2>

        <!-- Star Selector -->
        <div class="flex justify-center space-x-2 mb-3" id="starContainer">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <button
                class="star-btn w-12 h-12 rounded-full flex items-center justify-center transition touch-feedback"
                data-value="<?php echo $i; ?>"
                onclick="setRating(<?php echo $i; ?>)">
                <svg class="w-9 h-9 star-icon text-gray-200 transition-all" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            </button>
            <?php endfor; ?>
        </div>

        <!-- Rating Label -->
        <p id="ratingLabel" class="text-center text-sm font-semibold text-gray-400">Mag-tap ng bituin</p>
    </div>

    <!-- Feedback Text -->
    <div class="bg-white rounded-2xl shadow-md p-4 mb-4">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">I-Share ang Iyong Feedback</h2>
        <textarea
            id="reviewText"
            maxlength="500"
            rows="4"
            placeholder="Masarap ba ang pagkain? Kumusta ang delivery?"
            class="w-full px-3 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary resize-none"
            oninput="updateCharCount()"></textarea>
        <div class="flex justify-end mt-1">
            <span id="charCount" class="text-xs text-gray-400">0/500</span>
        </div>
    </div>

    <!-- Add Photos -->
    <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">Add Photos</h2>
        <div class="flex space-x-3" id="photoSlots">
            <!-- Upload Button -->
            <label class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:border-primary hover:bg-orange-50 transition touch-feedback">
                <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-xs text-gray-400">Upload</span>
                <input type="file" accept="image/*" multiple class="hidden" onchange="handlePhotoUpload(event)">
            </label>
            <!-- Empty Slots -->
            <div class="w-20 h-20 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50"></div>
            <div class="w-20 h-20 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50"></div>
        </div>
    </div>

    <!-- Submit Button -->
    <button onclick="submitReview()" id="submitBtn"
        class="w-full bg-primary text-white py-4 rounded-xl font-bold text-base shadow-lg hover:bg-orange-600 active:bg-orange-700 transition touch-feedback">
        I-SUBMIT ANG REVIEW
    </button>
</div>

<script>
let currentRating = 0;
const labels = ['', 'Mahirap', 'Okay Lang', 'Magaling', 'Mahusay', 'Napakahusay!'];

function setRating(value) {
    currentRating = value;
    const stars = document.querySelectorAll('.star-icon');
    stars.forEach((star, index) => {
        if (index < value) {
            star.classList.remove('text-gray-200');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-200');
        }
    });
    const label = document.getElementById('ratingLabel');
    label.textContent = labels[value] || '';
    label.classList.remove('text-gray-400');
    label.classList.add('text-primary');
}

function updateCharCount() {
    const len = document.getElementById('reviewText').value.length;
    document.getElementById('charCount').textContent = `${len}/500`;
}

function handlePhotoUpload(event) {
    const files = Array.from(event.target.files).slice(0, 3);
    const slotsContainer = document.getElementById('photoSlots');
    const emptySlots = slotsContainer.querySelectorAll('.border-gray-200');
    
    files.forEach((file, idx) => {
        if (idx < emptySlots.length) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const slot = emptySlots[idx];
                slot.style.backgroundImage = `url(${e.target.result})`;
                slot.style.backgroundSize = 'cover';
                slot.style.backgroundPosition = 'center';
                slot.classList.remove('border-dashed', 'border-gray-200', 'bg-gray-50');
                slot.classList.add('border-primary');
            };
            reader.readAsDataURL(file);
        }
    });
}

function submitReview() {
    if (currentRating === 0) {
        showToast('Pakipili ang iyong rating first!');
        return;
    }
    const text = document.getElementById('reviewText').value.trim();
    if (text.length < 10) {
        showToast('Pakisulat ng mas mahaba na review (min. 10 characters)');
        return;
    }
    const btn = document.getElementById('submitBtn');
    btn.textContent = 'Nagsu-submit...';
    btn.disabled = true;
    btn.classList.add('opacity-75');
    
    setTimeout(() => {
        window.location.href = '/review_success.php';
    }, 1000);
}

function showToast(message) {
    const existing = document.getElementById('toast');
    if (existing) existing.remove();
    
    const toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm font-medium';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
