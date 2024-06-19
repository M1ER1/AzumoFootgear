$(document).ready(function() {
    // Function to load all users
    function loadUsers() {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=users', // Endpoint to get all users
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Users loaded:', response); // Log the response for debugging
                if (response.error) {
                    alert('Failed to load users: ' + response.error);
                } else {
                    displayUsers(response); // Display the users if the request is successful
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load users:', error);
                alert('Failed to load users.');
            }
        });
    }

    // Function to display users
    function displayUsers(users) {
        let usersContainer = $('#usersContainer');
        usersContainer.empty(); // Clear any existing content

        users.forEach(user => {
            let userHtml = `
                <div class="user mb-5 p-3 border rounded">
                    <h3>${user.username}</h3>
                    <p>Email: ${user.email}</p>
                    <p>Status: ${user.status}</p>
                    <button class="btn btn-secondary deactivate-user" data-id="${user.id}">Deactivate User</button>
                    <div class="orders-container mt-4" id="orders-container-${user.id}">
                        <h4>Orders for ${user.username}</h4>
                    </div>
                </div>
            `;
            usersContainer.append(userHtml); // Append user HTML to the container
            loadUserOrders(user.id); // Load orders for each user
        });
    }

    // Function to load orders for a specific user
    function loadUserOrders(userId) {
        $.ajax({
            url: `../../Backend/Handler/RequestHandler.php?resource=userOrders&userId=${userId}`, // Endpoint to get orders for a user
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(`Orders for user ${userId} loaded:`, response); // Log the response for debugging
                if (response.error) {
                    alert(`Failed to load orders for user ${userId}: ${response.error}`);
                } else {
                    displayUserOrders(userId, response); // Display orders if the request is successful
                }
            },
            error: function(xhr, status, error) {
                console.error(`Failed to load orders for user ${userId}:`, error);
                alert(`Failed to load orders for user ${userId}.`);
            }
        });
    }
    
    // Function to display orders for a specific user
    function displayUserOrders(userId, orders) {
        let ordersContainer = $(`#orders-container-${userId}`);
        ordersContainer.empty(); // Clear any existing content
    
        if (orders.length === 0) {
            ordersContainer.append('<p>No orders found for this user.</p>'); // Display message if no orders are found
        } else {
            orders.forEach(order => {
                let productsHtml = '';
                if (order.products && Array.isArray(order.products)) {
                    productsHtml = order.products.map(product => `
                        <div class="product mb-2 p-2 border rounded">
                            <p>Product: ${product.name}</p>
                            <p>Quantity: ${product.quantity}</p>
                            <p>Price: ${product.price}</p>
                            <button class="btn btn-danger remove-product" data-order-id="${order.id}" data-product-id="${product.id}">Remove Product</button>
                        </div>
                    `).join('');
                } else {
                    productsHtml = '<p>No products found for this order.</p>';
                }
    
                let orderHtml = `
                    <div class="order mb-3 p-2 border rounded">
                        <p>Order ID: ${order.id}</p>
                        <p>Date: ${order.date}</p>
                        <p>Total: ${order.total}</p>
                        <div class="products-container" id="products-container-${order.id}">
                            ${productsHtml}
                        </div>
                    </div>
                `;
                ordersContainer.append(orderHtml); // Append order HTML to the container
            });
        }
    }
    
    // Event listener for removing a product from an order
    $(document).on('click', '.remove-product', function() {
        const orderId = $(this).data('order-id');
        const productId = $(this).data('product-id');
        removeProductFromOrder(orderId, productId); // Call function to remove the product
    });
    
    // Function to remove a product from an order
    function removeProductFromOrder(orderId, productId) {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=remove_product', // Endpoint to remove a product from an order
            type: 'POST',
            data: { orderId: orderId, productId: productId },
            success: function(response) {
                alert('Product removed successfully');
                loadUsers(); // Reload users and their orders after removing the product
            },
            error: function(xhr, status, error) {
                console.error('Failed to remove product:', error);
                alert('Failed to remove product.');
            }
        });
    }

    // Function to deactivate a user
    function deactivateUser(userId) {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=deactivate_user', // Endpoint to deactivate a user
            type: 'POST',
            data: { id: userId },
            success: function(response) {
                alert('User deactivated successfully');
                loadUsers(); // Reload users after deactivation
            },
            error: function(xhr, status, error) {
                console.error('Failed to deactivate user:', error);
                alert('Failed to deactivate user.');
            }
        });
    }

    // Event listener for deactivating a user
    $(document).on('click', '.deactivate-user', function() {
        const userId = $(this).data('id');
        deactivateUser(userId); // Call function to deactivate the user
    });

    // Initial load of all users
    loadUsers();
});
