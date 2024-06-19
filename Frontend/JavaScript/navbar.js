$(document).ready(function () {
    // Function to load the navbar with user-specific links
    function loadNavbar() {
        $.ajax({
            type: 'GET',
            url: '../../Backend/logic/check_login.php', // Request to check login status
            dataType: 'json',
            success: function (response) {
                let navbarHtml = '<nav class="navbar navbar-expand-lg bg-dark navbar-dark">';
                navbarHtml += '<div class="container">';

                if (response.success) {
                    // If user is logged in, display a welcome message with the username
                    navbarHtml += `<a class='navbar-brand'>Welcome, ${response.username}!</a>`;
                } else {
                    // If user is not logged in, display the brand name with a link to the home page
                    navbarHtml += `<a href='../../Frontend/Sites/index.html' class='navbar-brand'>AzumoFootgear</a>`;
                }

                navbarHtml += `
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navmenu">
                        <ul class="navbar-nav ms-auto">`;

                if (response.success) {
                    // Links for logged-in users
                    if (response.role === 'admin') {
                        // Additional links for admin users
                        navbarHtml += `<li class='nav-item'><a href='../../Frontend/Sites/index.html' class='nav-link'>Home</a></li>`;
                        navbarHtml += `<li class='nav-item'><a href='../../Frontend/Sites/add_product.html' class='nav-link'>Add Product</a></li>`;
                        navbarHtml += `<li class='nav-item'><a href='../../Frontend/Sites/editProduct.html' class='nav-link'>Products</a></li>`;
                        navbarHtml += `<li class='nav-item'><a href='../../Frontend/Sites/editUsers.html' class='nav-link'>Users</a></li>`;
                        navbarHtml += `<li class='nav-item'><a href='../../Frontend/Sites/coupons.html' class='nav-link'>Coupons</a></li>`;
                        navbarHtml += `<li class='nav-item'><a href='../../Frontend/Sites/index.html' id="logout" class='nav-link'>Logout</a></li>`;
                    } else {
                        // Links for regular users
                        navbarHtml += `
                            <li class='nav-item'><a href='../../Frontend/Sites/index.html' class='nav-link'>Home</a></li>
                            <li class='nav-item'><a href='../../Frontend/Sites/profile.html' class='nav-link'>Profile</a></li>
                            <li class='nav-item'><a href='../../Frontend/Sites/orders.html' class='nav-link'>My Orders</a></li>
                            <li class='nav-item'><a href='../../Frontend/Sites/index.html' id="logout" class='nav-link'>Logout</a></li>`;
                    }
                } else {
                    // Links for users who are not logged in
                    navbarHtml += `
                        <li class='nav-item'><a href='../../Frontend/Sites/index.html' class='nav-link'>Home</a></li>
                        <li class='nav-item'><a href='../Sites/login2.html' id="loginLink" class='nav-link'>Login</a></li>
                        <li class='nav-item'><a href='../Sites/register2.html' class='nav-link'>Register</a></li>`;
                }

                navbarHtml += `
                        <li class='nav-item'><a href='../Sites/cart.html' class='nav-link'>Cart <span id="cartCount" class="badge bg-secondary">0</span></a></li>
                    </ul>
                </div>
                </div>
                </nav>`;

                // Add the generated navbar HTML to the body
                $('body').prepend(navbarHtml);
                if (response.success) {
                    logoutBTN(); // Add logout functionality if user is logged in
                    updateCartCount(true); // Update cart count from server
                } else {
                    updateCartCount(false); // Update cart count from local storage
                }

                $('#loginLink').on('click', function() {
                    // Update local storage cart count before redirecting to login
                    updateCartCount(false);
                    window.location.href = '../Sites/login2.html';
                });
            },
            error: function (xhr, status, error) {
                console.error('Failed to load user data:', error);
                console.error('Response Text:', xhr.responseText); // Log the response text
            }
        });
    }

    // Function to update the cart count
    function updateCartCount(fromServer = false) {
        if (fromServer) {
            // Fetch cart count from the server if user is logged in
            $.ajax({
                url: "../../Backend/Handler/RequestHandler.php?resource=cart",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    const cartItems = Object.values(response);
                    $('#cartCount').text(cartItems.length);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to update cart count from server:', error);
                }
            });
        } else {
            // Fetch cart count from local storage if user is not logged in
            const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            $('#cartCount').text(cartItems.length);
        }
    }

    // Function to handle logout
    function logoutBTN() {
        document.getElementById("logout").addEventListener("click", function() {
            $.ajax({
                type: 'POST',
                url: '../../Backend/logic/logout.php',
                success: function() {
                    window.location.href = '../../Frontend/Sites/index.html';
                },
                error: function() {
                    alert('Logout failed: ' + error);
                }
            });
        });
    }

    // Event listener for cart updates
    window.addEventListener('cartUpdated', function() {
        updateCartCount(isLoggedIn);
    });

    // Load the navbar on document ready
    loadNavbar();
});
