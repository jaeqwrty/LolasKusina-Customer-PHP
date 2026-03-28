<?php
// Home Page - Packages View
$pageTitle = "Lola's Kusina";
$currentPage = "packages";
$packages = (isset($packages) && is_array($packages)) ? $packages : [];
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 md:px-8 py-6 max-w-md md:max-w-none mb-20 md:mb-8">
    <!-- Top section: banner + category (stacked on mobile, side-by-side on desktop) -->
    <div class="md:flex md:items-start md:gap-6 mb-6">
        <!-- Custom Orders Banner -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-6 text-white mb-6 md:mb-0 shadow-lg md:flex-1">
            <h2 class="text-2xl font-bold mb-2">Gusto mo ba ng iba?</h2>
            <p class="text-sm mb-4 opacity-90">Create your own perfect menu combination for your special event.</p>
            <a href="<?php echo BASE_PATH; ?>/build_package.php" class="inline-block bg-white text-primary px-6 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-md">
                BUILD YOUR OWN PACKAGE
            </a>
        </div>

        <!-- Category Menu (inline on desktop, frameless) -->
        <div class="hidden md:block w-56 shrink-0">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-[0.2em] mb-2">Quick Filter</p>
            <div class="space-y-2">
                <button onclick="filterPackages('all')" class="category-btn active w-full flex items-center justify-between text-left px-4 py-2.5 rounded-xl font-semibold transition duration-200 bg-primary text-white shadow-md" data-category="all" aria-pressed="true">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M2 5.5A2.5 2.5 0 014.5 3h11A2.5 2.5 0 0118 5.5v9a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 012 14.5v-9zm3.5.5a.5.5 0 000 1h9a.5.5 0 000-1h-9zm0 3a.5.5 0 000 1h6a.5.5 0 000-1h-6zm0 3a.5.5 0 000 1h8a.5.5 0 000-1h-8z"></path>
                        </svg>
                        <span>All Packages</span>
                    </span>
                </button>
                <button onclick="filterPackages('fiesta')" class="category-btn w-full flex items-center justify-between text-left px-4 py-2.5 rounded-xl font-semibold transition duration-200 bg-white text-gray-700 border border-gray-200 hover:bg-orange-50 hover:border-orange-200" data-category="fiesta" aria-pressed="false">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 2a1 1 0 01.92.606l1.38 3.22 3.48.3a1 1 0 01.57 1.76l-2.64 2.28.8 3.38a1 1 0 01-1.49 1.07L10 12.96 7.01 14.7a1 1 0 01-1.49-1.07l.8-3.38L3.68 7.98a1 1 0 01.57-1.76l3.48-.3 1.38-3.22A1 1 0 0110 2z"></path>
                        </svg>
                        <span>Fiesta Trays</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Menu (horizontal scroll on mobile only, frameless) -->
    <div class="md:hidden mb-6">
        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Filter Packages</p>
        <div class="flex space-x-2 overflow-x-auto pb-1">
            <button onclick="filterPackages('all')" class="category-btn active px-5 py-2.5 rounded-xl font-semibold whitespace-nowrap transition duration-200 bg-primary text-white shadow-md" data-category="all" aria-pressed="true">
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M2 5.5A2.5 2.5 0 014.5 3h11A2.5 2.5 0 0118 5.5v9a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 012 14.5v-9zm3.5.5a.5.5 0 000 1h9a.5.5 0 000-1h-9zm0 3a.5.5 0 000 1h6a.5.5 0 000-1h-6zm0 3a.5.5 0 000 1h8a.5.5 0 000-1h-8z"></path>
                    </svg>
                    <span>All Packages</span>
                </span>
            </button>
            <button onclick="filterPackages('fiesta')" class="category-btn px-5 py-2.5 rounded-xl font-semibold whitespace-nowrap transition duration-200 bg-white text-gray-700 border border-gray-200 hover:bg-orange-50 hover:border-orange-200" data-category="fiesta" aria-pressed="false">
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 2a1 1 0 01.92.606l1.38 3.22 3.48.3a1 1 0 01.57 1.76l-2.64 2.28.8 3.38a1 1 0 01-1.49 1.07L10 12.96 7.01 14.7a1 1 0 01-1.49-1.07l.8-3.38L3.68 7.98a1 1 0 01.57-1.76l3.48-.3 1.38-3.22A1 1 0 0110 2z"></path>
                    </svg>
                    <span>Fiesta Trays</span>
                </span>
            </button>
        </div>
    </div>

    <!-- Best Sellers Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Our Best Sellers</h3>
            <a href="<?php echo BASE_PATH; ?>/build_package.php" class="text-primary font-semibold text-sm hover:underline">View All</a>
        </div>

        <!-- Package grid: 1 col mobile, 3 cols desktop -->
        <div class="md:grid md:grid-cols-3 md:gap-6">
        <?php if (empty($packages)): ?>
        <div class="md:col-span-3 bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-500">
            No packages available right now. Please check again later.
        </div>
        <?php else: ?>
        <?php foreach ($packages as $package): ?>
        <?php
            $packageId = (int) ($package['id'] ?? 0);
            $packageName = htmlspecialchars($package['name'] ?? 'Package', ENT_QUOTES, 'UTF-8');
            $subtitle = htmlspecialchars($package['persons_served'] ?? ($package['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            $details = htmlspecialchars($package['details'] ?? ($package['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            $priceValue = isset($package['price']) ? (float) $package['price'] : 0;
            $imageName = htmlspecialchars($package['image'] ?? 'placeholder.svg', ENT_QUOTES, 'UTF-8');
            $rating = number_format((float) ($package['rating'] ?? 0), 1);
            $reviews = (int) ($package['reviews_count'] ?? ($package['reviews'] ?? 0));
            $badge = !empty($package['badge'])
                ? htmlspecialchars($package['badge'], ENT_QUOTES, 'UTF-8')
                : (!empty($package['is_bestseller']) ? 'Best Seller' : '');
        ?>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-4 hover:shadow-xl transition flex flex-col">
            <!-- Package Image -->
            <div class="relative overflow-hidden bg-[#FFF3EE]">
                <a href="<?php echo BASE_PATH; ?>/package_details.php?id=<?php echo $packageId; ?>" class="block">
                    <img src="<?php echo BASE_PATH; ?>/images/<?php echo $imageName; ?>" alt="<?php echo $packageName; ?>" class="w-full h-48 object-cover" onerror="this.onerror=null;this.src='<?php echo BASE_PATH; ?>/images/placeholder.svg'">
                </a>
                <?php if ($badge !== ''): ?>
                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                    <?php echo $badge; ?>
                </span>
                <?php endif; ?>
                <button class="absolute top-3 left-3 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
            </div>

            <!-- Package Info -->
            <div class="p-4 flex flex-col flex-1">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800"><?php echo $packageName; ?></h4>
                        <p class="text-sm text-gray-500"><?php echo $subtitle; ?></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold text-primary">₱<?php echo number_format($priceValue, 2); ?></span>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-3 flex-1"><?php echo $details; ?></p>

                <div class="flex items-center justify-between mt-auto">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700"><?php echo $rating; ?></span>
                        <span class="text-xs text-gray-500">(<?php echo $reviews; ?>)</span>
                    </div>

                    <a href="<?php echo BASE_PATH; ?>/package_details.php?id=<?php echo $packageId; ?>" class="bg-primary text-white px-5 py-2 rounded-full font-semibold text-sm hover:bg-orange-600 transition shadow-md">
                        Order Now
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
        </div><!-- End package grid -->
    </div>

    <!-- Reviews Section -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 text-center mb-6 border border-purple-200">
        <p class="text-gray-700 mb-3">Curious about what others think?</p>
        <a href="<?php echo BASE_PATH; ?>/reviews.php" class="inline-block bg-primary text-white px-6 py-3 rounded-full font-bold hover:bg-orange-600 transition shadow-md touch-feedback">
            Tingnan ang Reviews
        </a>
    </div>
</div>

<script>
const FILTER_ACTIVE_ANIMATION_CLASS = 'filter-active-pop';

// Category filtering functionality
function filterPackages(category) {
    // Update button styles
    const buttons = document.querySelectorAll('.category-btn');
    buttons.forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-orange-50', 'hover:border-orange-200');
            btn.classList.add('bg-primary', 'text-white', 'shadow-md', 'active');
            btn.setAttribute('aria-pressed', 'true');

            // Re-trigger a subtle scale animation every time this filter is selected.
            btn.classList.remove(FILTER_ACTIVE_ANIMATION_CLASS);
            void btn.offsetWidth;
            btn.classList.add(FILTER_ACTIVE_ANIMATION_CLASS);
        } else {
            btn.classList.remove('bg-primary', 'text-white', 'active', 'shadow-md', FILTER_ACTIVE_ANIMATION_CLASS);
            btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-orange-50', 'hover:border-orange-200');
            btn.setAttribute('aria-pressed', 'false');
        }
    });
    
    // In a real implementation, this would filter the packages
    // For now, we'll just show a message
    console.log('Filtering packages by:', category);
}

// Smooth scroll to top when navigating
window.addEventListener('beforeunload', function() {
    window.scrollTo(0, 0);
});
</script>

<style>
@keyframes filterPop {
    0% { transform: scale(1); }
    45% { transform: scale(1.04); }
    100% { transform: scale(1); }
}

.filter-active-pop {
    animation: filterPop 240ms ease-out;
}
</style>

<?php include __DIR__ . '/layouts/footer.php'; ?>
