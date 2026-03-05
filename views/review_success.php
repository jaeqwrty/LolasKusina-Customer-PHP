<?php
// Review Success Page
$pageTitle = "Review Submitted - Lola's Kusina";
$currentPage = "reviews";
include __DIR__ . '/layouts/header.php';
?>

<div class="container mx-auto px-4 max-w-md">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 -mt-16">

        <!-- Success Card -->
        <div class="w-full bg-white rounded-2xl shadow-lg p-8 text-center border-2 border-dashed border-primary">

            <!-- Heart Icon -->
            <div class="inline-flex items-center justify-center w-24 h-24 bg-red-50 rounded-full mb-6 border-4 border-red-100">
                <svg class="w-12 h-12 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                </svg>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl font-bold text-gray-800 mb-3">Salamat sa Feedback!</h1>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
                Ang iyong review ay makakatulong sa iba naming customer.
            </p>

            <!-- Buttons -->
            <a href="/reviews.php"
               class="block w-full bg-primary text-white py-4 rounded-xl font-bold text-base hover:bg-orange-600 active:bg-orange-700 transition touch-feedback shadow-md mb-4">
                BUMALIK SA REVIEWS
            </a>

            <a href="/"
               class="block text-primary font-bold text-base hover:underline touch-feedback py-2">
                PUMUNTA SA HOME
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
