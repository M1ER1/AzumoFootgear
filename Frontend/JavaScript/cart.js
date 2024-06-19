$(document).ready(function() {
    let cartTotal = 0.00;
    let appliedCouponId = null;
    let isLoggedIn = false; // Track user login status

    // Function to check user login status and synchronize cart items
    function checkLoginStatus() {
        const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
        $.ajax({
            url: '../../Backend/logic/check_login.php', // Endpoint to check login status
            type: 'POST',
            dataType: 'json',
            data: { cartItems: JSON.stringify(cartItems) }, // Send cart items to server for synchronization
            success: function(response) {
                isLoggedIn = response.success; // Update login status
                updateCheckoutButton();
                updateCartCount(true); // Update cart count from server
            },
            error: function(xhr, status, error) {
                console.error('Failed to check login status:', error);
            }
        });
    }

    // Function to update the checkout button based on login status
    function updateCheckoutButton() {
        if (isLoggedIn) {
            $('#checkout').text('Checkout').removeClass('btn-secondary').addClass('btn-success');
        } else {
            $('#checkout').text('Login to Checkout').removeClass('btn-success').addClass('btn-secondary');
        }
    }

    // Function to load cart items
    function loadCartItems() {
        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=cart", // Endpoint to get cart items
            type: "GET",
            dataType: "json",
            success: function(cartItems) {
                let cartItemsContainer = $('#cartItemsContainer');
                cartItemsContainer.empty();
                cartTotal = 0;

                cartItems.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    cartTotal += itemTotal;
                    const itemHtml = `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">${item.name}</h5>
                                <p class="card-text">Price: $${item.price}</p>
                                <p class="card-text">Quantity: ${item.quantity}</p>
                                <p class="card-text">Total: $${itemTotal.toFixed(2)}</p>
                                <button class="btn btn-danger btn-sm remove-from-cart" data-id="${item.id}">Remove</button>
                            </div>
                        </div>
                    `;
                    cartItemsContainer.append(itemHtml);
                });

                updateCartTotal(); // Update the total cart amount
            },
            error: function(xhr, status, error) {
                console.error("Failed to load cart items:", error);
            }
        });
    }

    // Function to update the total cart amount
    function updateCartTotal() {
        $('#cartTotal').text(cartTotal.toFixed(2));
    }

    // Event listener for applying a coupon
    $('#applyCoupon').on('click', function() {
        const couponCode = $('#couponCode').val();
        if (!couponCode) {
            alert('Please enter a coupon code.');
            return;
        }

        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=apply_coupon", // Endpoint to apply a coupon
            type: "POST",
            data: { code: couponCode },
            success: function(response) {
                if (response.coupon) {
                    appliedCouponId = response.coupon.id; // Save the coupon ID
                    const discount = parseFloat(response.coupon.value);
                    cartTotal -= discount;
                    if (cartTotal < 0) cartTotal = 0;
                    updateCartTotal();
                    $('#cartMessage').html('<div class="alert alert-success">Coupon applied successfully!</div>');
                } else {
                    $('#cartMessage').html('<div class="alert alert-danger">Failed to apply coupon.</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Failed to apply coupon:", error);
                alert("Failed to apply coupon: " + xhr.responseJSON.error);
            }
        });
    });

    // Event listener for checkout button
    $('#checkout').on('click', function() {
        if (!isLoggedIn) {
            window.location.href = '../Sites/login2.html'; // Redirect to login page if not logged in
            return;
        }

        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=cart", // Endpoint to get cart items
            type: "GET",
            dataType: "json",
            success: function(cartItems) {
                if (cartItems.length === 0) {
                    alert('Your cart is empty.');
                    return;
                }

                const requestData = {
                    total: cartTotal.toFixed(2),
                    cart: cartItems,
                    couponId: appliedCouponId // Include the coupon ID in the request
                };

                $.ajax({
                    url: "../../Backend/Handler/RequestHandler.php?resource=order", // Endpoint to place an order
                    type: "POST",
                    data: JSON.stringify(requestData),
                    contentType: "application/json",
                    success: function(response) {
                        if (response.success) {
                            localStorage.removeItem('cartItems'); // Clear local storage cart items
                            $('#cartMessage').html('<div class="alert alert-success">Order placed successfully!</div>');
                            loadCartItems(); // Reload cart items after order placement
                            updateCartCount(true);
                            alert('Order placed successfully!');
                        } else {
                            $('#cartMessage').html('<div class="alert alert-danger">Failed to place order.</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to place order:", error);
                        $('#cartMessage').html('<div class="alert alert-danger">Failed to place order: ' + xhr.responseJSON.error + '</div>');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Failed to get cart items:", error);
            }
        });
    });

    // Event listener for removing an item from the cart
    $(document).on('click', '.remove-from-cart', function() {
        const productId = $(this).data('id');

        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=remove_from_cart", // Endpoint to remove an item from the cart
            type: "POST",
            data: { productId: productId },
            success: function(response) {
                if (response.success) {
                    alert("Product removed from cart successfully!");
                    loadCartItems(); // Refresh cart items

                    // Trigger custom event to update cart count
                    const event = new Event('cartUpdated');
                    window.dispatchEvent(event);
                } else {
                    alert("Failed to remove product from cart.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Failed to remove product from cart:", error);
                alert("Failed to remove product from cart: " + xhr.responseJSON.error);
            }
        });
    });

    // Function to update the cart count
    function updateCartCount() {
        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=cart", // Endpoint to get cart items
            type: "GET",
            dataType: "json",
            success: function(response) {
                const cartItems = Object.values(response);
                $('#cartCount').text(cartItems.length); // Update cart count in the UI
            },
            error: function(xhr, status, error) {
                console.error('Failed to update cart count:', error);
            }
        });
    }

    // Initial load of cart items and check login status
    loadCartItems();
    checkLoginStatus();
    window.addEventListener('cartUpdated', updateCartCount); // Event listener for cart updates
});
