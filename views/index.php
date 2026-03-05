<?php
// Home Page - Packages View
$pageTitle = "Lola's Kusina - Home";
$currentPage = "packages";
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-6 max-w-md mb-20">
    <!-- Custom Orders Banner -->
    <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-6 text-white mb-6 shadow-lg">
        <h2 class="text-2xl font-bold mb-2">Gusto mo ba ng iba?</h2>
        <p class="text-sm mb-4 opacity-90">Create your own perfect menu combination for your special event.</p>
        <a href="/build_package.php" class="inline-block bg-white text-primary px-6 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-md">
            BUILD YOUR OWN PACKAGE
        </a>
    </div>

    <!-- Category Menu -->
    <div class="flex space-x-2 mb-6 overflow-x-auto pb-2">
        <button onclick="filterPackages('all')" class="bg-primary text-white px-6 py-2 rounded-full font-semibold whitespace-nowrap shadow-md category-btn active" data-category="all">
            All Packages
        </button>
        <button onclick="filterPackages('fiesta')" class="bg-white text-gray-700 px-6 py-2 rounded-full font-semibold whitespace-nowrap shadow-md hover:bg-gray-100 category-btn" data-category="fiesta">
            Fiesta Trays
        </button>
        <button onclick="filterPackages('dessert')" class="bg-white text-gray-700 px-6 py-2 rounded-full font-semibold whitespace-nowrap shadow-md hover:bg-gray-100 category-btn" data-category="dessert">
            Desserts
        </button>
    </div>

    <!-- Best Sellers Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Our Best Sellers</h3>
            <a href="/build_package.php" class="text-primary font-semibold text-sm hover:underline">View All</a>
        </div>

        <?php
        $packages = [
            [
                'id' => 1,
                'name' => 'Paborito Package',
                'description' => 'Good for 6-7 pax',
                'details' => 'Includes Lechon Kawali, Pata, Pancit Canton, Steamed Rice, and Buko Pandan.',
                'price' => 2500,
                'image' => 'paborito-package.jpg',
                'rating' => 4.8,
                'reviews' => 20,
                'badge' => 'Best Seller'
            ],
            [
                'id' => 2,
                'name' => 'Family Fiesta',
                'description' => 'Good for 10-12 pax',
                'details' => 'The ultimate gathering set! Chicken Inasal, Kare-Kare, Lumpia Shanghai, Garlic Rice, and Halo-halo.',
                'price' => 4200,
                'image' => 'family-fiesta.jpg',
                'rating' => 4.9,
                'reviews' => 35,
                'badge' => 'Popular'
            ],
            [
                'id' => 3,
                'name' => 'Salo-Salo Special',
                'description' => 'Good for 15-20 pax',
                'details' => 'Perfect for big celebrations! Crispy Pata, Beef Caldereta, Seafood Pancit, Java Rice, Fruit Salad.',
                'price' => 6500,
                'image' => 'salo-salo.jpg',
                'rating' => 4.7,
                'reviews' => 18,
                'badge' => 'New'
            ]
        ];

        foreach ($packages as $package):
        ?>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-4 hover:shadow-xl transition">
            <!-- Package Image -->
            <div class="relative">
                <img src="/images/<?php echo $package['image']; ?>" alt="<?php echo $package['name']; ?>" class="w-full h-48 object-cover" onerror="this.src='/images/placeholder.svg'">
                <?php if (isset($package['badge'])): ?>
                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                    <?php echo $package['badge']; ?>
                </span>
                <?php endif; ?>
                <button class="absolute top-3 left-3 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
            </div>

            <!-- Package Info -->
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800"><?php echo $package['name']; ?></h4>
                        <p class="text-sm text-gray-500"><?php echo $package['description']; ?></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold text-primary">₱<?php echo number_format($package['price'], 2); ?></span>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-3"><?php echo $package['details']; ?></p>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700"><?php echo $package['rating']; ?></span>
                        <span class="text-xs text-gray-500">(<?php echo $package['reviews']; ?>)</span>
                    </div>

                    <a href="/order_details.php?id=<?php echo $package['id']; ?>" class="bg-primary text-white px-5 py-2 rounded-full font-semibold text-sm hover:bg-orange-600 transition shadow-md">
                        Order Now
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Reviews Section -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 text-center mb-6 border border-purple-200">
        <p class="text-gray-700 mb-3">Curious about what others think?</p>
        <a href="/reviews.php" class="inline-block bg-primary text-white px-6 py-3 rounded-full font-bold hover:bg-orange-600 transition shadow-md touch-feedback">
            Tingnan ang Reviews
        </a>
    </div>
</div>

<script>
// Category filtering functionality
function filterPackages(category) {
    // Update button styles
    const buttons = document.querySelectorAll('.category-btn');
    buttons.forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.remove('bg-white', 'text-gray-700');
            btn.classList.add('bg-primary', 'text-white', 'active');
        } else {
            btn.classList.remove('bg-primary', 'text-white', 'active');
            btn.classList.add('bg-white', 'text-gray-700');
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

<?php include __DIR__ . '/layouts/footer.php'; ?>
