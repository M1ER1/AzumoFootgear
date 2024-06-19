$(document).ready(function () {
    // Function to load products with optional search and category filters
    function loadProducts(search = '', categories = []) {
        $.ajax({
            type: 'GET',
            url: '../../Backend/Handler/RequestHandler.php?resource=products', // Endpoint to get products
            data: { search: search, categories: categories }, // Optional filters
            dataType: 'json',
            success: function (response) {
                const productList = $('#productList');
                productList.empty(); // Clear existing products

                response.forEach(product => {
                    // Generate HTML for each product
                    const productCard = `
                        <div class="col">
                            <div class="card h-100">
                                <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text">${product.description}</p>
                                    <p class="card-text">Category: ${product.category}</p>
                                    <p class="card-text">Price: $${product.price}</p>
                                    <button class="btn btn-primary buy-now" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">Buy Now</button>
                                </div>
                            </div>
                        </div>`;
                    productList.append(productCard); // Append product HTML to the list
                });
            },
            error: function (xhr, status, error) {
                console.error('Failed to load products:', error);
            }
        });
    }

    // Event listener for search input
    $('#searchInput').on('input', function () {
        const search = $(this).val();
        loadProducts(search); // Load products based on search input
    });

    // Event listener for category filter form submission
    $('#categoryFilterForm').submit(function (event) {
        event.preventDefault();
        const categories = [];
        $('input[name="category[]"]:checked').each(function () {
            categories.push($(this).val()); // Collect selected categories
        });
        loadProducts('', categories); // Load products based on selected categories
    });

    // Event listener for "Buy Now" button click
    $('#productList').on('click', '.buy-now', function () {
        const productId = $(this).data('id');
        const quantity = 1;  // Set quantity (could be dynamic)

        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=add_to_cart", // Endpoint to add product to cart
            type: "POST",
            data: { productId: productId, quantity: quantity },
            success: function(response) {
                if (response.success) {
                    alert("Product added to cart successfully!");

                    // Trigger custom event to update cart count
                    const event = new Event('cartUpdated');
                    window.dispatchEvent(event);
                } else {
                    alert("Failed to add product to cart.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Failed to add product to cart:", error);
                alert("Failed to add product to cart: " + xhr.responseJSON.error);
            }
        });
    });

    /* // Function to update the cart count
    function updateCartCount() {
        $.ajax({
            type: 'GET',
            url: '../../Backend/Handler/RequestHandler.php?resource=cart_count', // Endpoint to get cart count
            success: function (response) {
                $('#cartCount').text(response.count); // Update cart count display
            },
            error: function (xhr, status, error) {
                console.error('Failed to update cart count:', error);
            }
        });
    } */

    // Initial load of products when the document is ready
    loadProducts();
});
