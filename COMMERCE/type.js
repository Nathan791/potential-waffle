  // --- MOBILE MENU TOGGLE LOGIC (Existing) ---
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('hidden');
            });
        }
        
        const closeButton = document.querySelector('#mobile-menu > div > .text-gray-500');
        if (closeButton && mobileMenu) {
            closeButton.addEventListener('click', function(e) {
                e.preventDefault();
                mobileMenu.classList.add('hidden');
            });
        }
        
        // --- E-COMMERCE JAVASCRIPT LOGIC (New) ---
        
        let cartCount = 0;
        const cartCountElement = document.getElementById('cart-count');
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        const toastMessage = document.getElementById('toast-message');

        /**
         * Displays a temporary confirmation toast message.
         * @param {string} message - The text to display in the toast.
         */
        function showToast(message) {
            if (!toastMessage) return;
            toastMessage.textContent = message;
            // Make the toast visible
            toastMessage.classList.remove('opacity-0');
            toastMessage.classList.add('opacity-100');

            // Hide the toast after 1.5 seconds
            setTimeout(() => {
                toastMessage.classList.remove('opacity-100');
                toastMessage.classList.add('opacity-0');
            }, 1500); 
        }

        /**
         * Handles the "Add to Cart" button click event.
         */
        function addToCart(event) {
            if (event && event.preventDefault) event.preventDefault();
            
            // Get the product name from the button's data attribute
            const target = event && (event.currentTarget || event.target);
            const productName = target && target.getAttribute ? target.getAttribute('data-product-name') : '';
            
            // 1. Update Cart Count
            cartCount++;
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
            }

            // 2. Provide UI Feedback
            showToast(productName ? `${productName} added!` : 'Added to cart');
        }

        // Attach the event listener to all "Add to Cart" buttons
        if (addToCartButtons && addToCartButtons.length) {
            addToCartButtons.forEach(button => {
                if (button && button.addEventListener) {
                    button.addEventListener('click', addToCart);
                }
            });
        }