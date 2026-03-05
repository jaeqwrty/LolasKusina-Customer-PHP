<?php
// Reviews Page - Mga Sabi Nila
$pageTitle = "Reviews - Lola's Kusina";
$currentPage = "reviews";
include __DIR__ . '/layouts/header.php';

$reviews = [
    [
        'id' => 1,
        'name' => 'Juan Dela Cruz',
        'initial' => 'JD',
        'color' => 'bg-orange-400',
        'verified' => true,
        'rating' => 5,
        'time' => '2 weeks ago',
        'text' => 'Sobrang sarap ng food! The lechon belly was crispy pa nung dumating kahit medyo traffic. Solid yung delivery service. Highly recommended for events!',
        'photos' => ['lechon-kawali.jpg', 'pancit-canton.jpg'],
        'package' => 'Paborito Package'
    ],
    [
        'id' => 2,
        'name' => 'Maria Santos',
        'initial' => 'MS',
        'color' => 'bg-pink-400',
        'verified' => true,
        'rating' => 4,
        'time' => '1 month ago',
        'text' => 'Okay naman yung food, medyo na-late lang onti yung rider pero balik naman ni kuya. Will order again po pag may occasion!',
        'photos' => [],
        'package' => 'Family Fiesta'
    ],
    [
        'id' => 3,
        'name' => 'Kiko M.',
        'initial' => 'KM',
        'color' => 'bg-blue-400',
        'verified' => true,
        'rating' => 5,
        'time' => '2 months ago',
        'text' => 'Bitin yung rice kasi ang sarap ng ulam hahaha. Next time mag extra rice na ako. Sulit na sulit!',
        'photos' => ['kare-kare.jpg'],
        'package' => 'Salo-Salo Special'
    ],
];

$starBreakdown = [5 => 72, 4 => 18, 3 => 6, 2 => 3, 1 => 1];
?>

<div class="container mx-auto px-4 py-6 max-w-md mb-20">

    <!-- Page Title -->
    <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">Mga Sabi Nila</h1>

    <!-- Overall Rating Card -->
    <div class="bg-white rounded-2xl shadow-md p-5 mb-5">
        <div class="flex items-start space-x-4">
            <!-- Big Rating Number -->
            <div class="text-center">
                <div class="text-5xl font-bold text-gray-800">4.8</div>
                <div class="flex mt-1 justify-center space-x-0.5">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="w-4 h-4 <?php echo $i <= 4 ? 'text-yellow-400' : 'text-yellow-200'; ?>" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <?php endfor; ?>
                </div>
                <div class="text-xs text-gray-500 mt-1">153 REVIEWS</div>
            </div>

            <!-- Star Breakdown Bars -->
            <div class="flex-1 space-y-1.5">
                <?php foreach ($starBreakdown as $star => $pct): ?>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 w-3"><?php echo $star; ?></span>
                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full" style="width: <?php echo $pct; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Leave Review Button -->
        <a href="/write_review.php" class="mt-4 flex items-center justify-center space-x-2 w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-orange-600 active:bg-orange-700 transition touch-feedback shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>MAG-LEAVE NG REVIEW</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 mb-5 overflow-x-auto pb-1 hide-scrollbar">
        <button onclick="filterReviews('all')" class="filter-btn active bg-dark text-white px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap" data-filter="all">All</button>
        <button onclick="filterReviews('photos')" class="filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="photos">Photos</button>
        <button onclick="filterReviews('5')" class="filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="5">5 Star</button>
        <button onclick="filterReviews('4')" class="filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="4">4 Star</button>
        <button onclick="filterReviews('3')" class="filter-btn bg-white text-gray-700 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-sm border border-gray-200" data-filter="3">3 Star</button>
    </div>

    <!-- Reviews List -->
    <div id="reviewsList" class="space-y-4">
        <?php foreach ($reviews as $review): ?>
        <div class="review-card bg-white rounded-2xl shadow-md p-4 transition"
             data-rating="<?php echo $review['rating']; ?>"
             data-has-photos="<?php echo !empty($review['photos']) ? 'true' : 'false'; ?>">

            <!-- Reviewer Header -->
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 <?php echo $review['color']; ?> rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        <?php echo $review['initial']; ?>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <span class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($review['name']); ?></span>
                            <?php if ($review['verified']): ?>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-1.5 py-0.5 rounded flex items-center space-x-0.5">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>VERIFIED</span>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-gray-400"><?php echo $review['time']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Stars -->
            <div class="flex space-x-0.5 mb-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <svg class="w-4 h-4 <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-200'; ?>" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <?php endfor; ?>
            </div>

            <!-- Review Text -->
            <p class="text-sm text-gray-700 mb-3 leading-relaxed"><?php echo htmlspecialchars($review['text']); ?></p>

            <!-- Package Tag -->
            <div class="flex items-center space-x-1 mb-3">
                <span class="bg-orange-50 text-primary text-xs px-2 py-1 rounded-full border border-orange-200">📦 <?php echo htmlspecialchars($review['package']); ?></span>
            </div>

            <!-- Photos -->
            <?php if (!empty($review['photos'])): ?>
            <div class="flex space-x-2">
                <?php foreach ($review['photos'] as $photo): ?>
                <img src="/images/<?php echo $photo; ?>"
                     alt="Review photo"
                     class="w-20 h-20 rounded-xl object-cover cursor-pointer hover:opacity-90 transition"
                     onerror="this.src='/images/placeholder.svg'">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Load More Button -->
    <div class="mt-5 text-center">
        <button onclick="loadMoreReviews()" class="bg-white border-2 border-gray-200 text-gray-700 px-8 py-3 rounded-xl font-semibold hover:border-primary hover:text-primary transition touch-feedback">
            Load More Reviews
        </button>
    </div>

</div>

<script>
function filterReviews(filter) {
    // Update tab styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-dark', 'text-white');
        btn.classList.add('bg-white', 'text-gray-700');
    });
    const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'text-gray-700');
        activeBtn.classList.add('bg-dark', 'text-white');
    }

    // Filter review cards
    document.querySelectorAll('.review-card').forEach(card => {
        const rating = card.dataset.rating;
        const hasPhotos = card.dataset.hasPhotos === 'true';
        let show = true;

        if (filter === 'photos') {
            show = hasPhotos;
        } else if (filter !== 'all') {
            show = rating === filter;
        }

        card.style.display = show ? 'block' : 'none';
    });
}

function loadMoreReviews() {
    const btn = event.target;
    btn.textContent = 'Loading...';
    btn.disabled = true;
    setTimeout(() => {
        btn.textContent = 'No more reviews';
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }, 1000);
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
