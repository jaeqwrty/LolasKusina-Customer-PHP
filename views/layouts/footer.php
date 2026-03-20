    <!-- Footer Navigation (mobile only) -->
    <footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 safe-bottom z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-around items-center py-3">
                <a href="<?php echo BASE_PATH; ?>/" class="flex flex-col items-center <?php echo ($currentPage ?? '') == 'packages' ? 'text-primary' : 'text-gray-500'; ?> hover:text-primary transition touch-feedback min-w-[60px]">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span class="text-xs font-medium">Packages</span>
                </a>
                
                <a href="<?php echo BASE_PATH; ?>/reviews.php" class="flex flex-col items-center <?php echo ($currentPage ?? '') == 'reviews' ? 'text-primary' : 'text-gray-500'; ?> hover:text-primary transition touch-feedback min-w-[60px]">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs font-medium">Reviews</span>
                </a>
                
                <a href="<?php echo BASE_PATH; ?>/cart.php" class="flex flex-col items-center <?php echo ($currentPage ?? '') == 'order' ? 'text-primary' : 'text-gray-500'; ?> hover:text-primary transition touch-feedback relative min-w-[60px]">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                    </svg>
                    <?php if (isset($cartCount) && $cartCount > 0): ?>
                        <span class="absolute -top-1 right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                    <span class="text-xs font-medium">Order</span>
                </a>
                
                <a href="<?php echo BASE_PATH; ?>/profile.php" class="flex flex-col items-center <?php echo ($currentPage ?? '') == 'account' ? 'text-primary' : 'text-gray-500'; ?> hover:text-primary transition touch-feedback min-w-[60px]">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-xs font-medium">Account</span>
                </a>
            </div>
        </div>
    </footer>
    
    <!-- Mobile spacer for fixed footer -->
    <div class="h-24 md:h-0"></div>
    
    </div><!-- End desktop-main / md:ml-64 wrapper -->
    
    <!-- Service Worker Registration for PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo BASE_PATH; ?>/sw.js').catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    <script src="<?php echo BASE_PATH; ?>/js/localStorageManager.js"></script>
</body>
</html>
