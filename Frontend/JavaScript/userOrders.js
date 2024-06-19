$(document).ready(function() {
    // Function to load all orders
    function loadOrders() {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=orders',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert('Failed to load orders: ' + response.error);
                } else {
                    displayOrders(response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load orders:', error);
                alert('Failed to load orders.');
            }
        });
    }

    // Function to display all orders
    function displayOrders(orders) {
        let ordersContainer = $('#ordersContainer');
        ordersContainer.empty();

        orders.forEach(order => {
            let orderHtml = `
                <div class="order">
                    <h3>Order #${order.id}</h3>
                    <p>Total: $${order.total}</p>
                    <p>Date: ${order.date}</p>
                    <button class="btn btn-secondary print-invoice" data-id="${order.id}">Print Invoice</button>
                    <div class="order-details" id="order-details-${order.id}" style="display: none;"></div>
                </div>
            `;
            ordersContainer.append(orderHtml);
        });
    }

    // Function to load details of a specific order
   /*  function loadOrderDetails(orderId) {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=orderDetails&params[orderId]=' + orderId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert('Failed to load order details: ' + response.error);
                } else {
                    displayOrderDetails(orderId, response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load order details:', error);
                alert('Failed to load order details.');
            }
        });
    }

    // Function to display details of a specific order
    function displayOrderDetails(orderId, details) {
        let detailsContainer = $(`#order-details-${orderId}`);
        detailsContainer.empty();

        let orderDetailsHtml = `
            <h4>Order Details</h4>
            <p>Customer: ${details[0].firstname} ${details[0].lastname}</p>
            <p>Address: ${details[0].address}, ${details[0].city}, ${details[0].postcode}</p>
            <p>Date: ${details[0].date}</p>
            <ul>`;
        details.forEach(item => {
            orderDetailsHtml += `<li>${item.product_name} - $${item.price} x ${item.quantity}</li>`;
        });
        orderDetailsHtml += `</ul>`;
        
        detailsContainer.append(orderDetailsHtml);
        detailsContainer.toggle();
    } */

    // Function to load order details for invoice printing
    function printInvoice(orderId) {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=orderDetails&orderId=' + orderId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert('Failed to load order details for invoice: ' + response.error);
                } else {
                    generateInvoice(orderId, response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load order details for invoice:', error);
                alert('Failed to load order details for invoice.');
            }
        });
    }
    
    // Function to generate and open the invoice in a new window
    function generateInvoice(orderId) {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php',
            type: 'GET',
            data: {
                resource: 'orderDetails',
                orderId: orderId
            },
            dataType: 'json',
            success: function(response) {
                console.log("Invoice Response:", response); // Log the response for debugging
    
                if (!response.customerFirstname || !response.customerLastname) {
                    console.error("Response does not contain customer name details:", response);
                    alert("Failed to load invoice details.");
                    return;
                }
    
                const invoiceInfo = response;
                const {
                    invoiceNumber,
                    orderDate,
                    customerFirstname,
                    customerLastname,
                    customerAddress,
                    customerCity,
                    customerPostcode,
                    invoiceTotal,
                    orderItems
                } = invoiceInfo;
    
                let invoiceWindow = window.open('', '_blank');
                invoiceWindow.document.write(`
                    <html>
                    <head>
                        <title>Invoice ${invoiceNumber}</title>
                        <style>
                            /* Add some styles for better readability */
                        </style>
                    </head>
                    <body>
                        <h1>Invoice #${invoiceNumber}</h1>
                        <p><strong>Date:</strong> ${orderDate}</p>
                        <p><strong>Customer:</strong> ${customerFirstname} ${customerLastname}</p>
                        <p><strong>Address:</strong> ${customerAddress}, ${customerCity}, ${customerPostcode}</p>
                        <h2>Order Items</h2>
                        <table border="1">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${orderItems.map(item => `
                                    <tr>
                                        <td>${item.productName}</td>
                                        <td>${item.productQuantity}</td>
                                        <td>${item.productPrice}</td>
                                        <td>${item.productSubtotal}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <h2>Total: ${invoiceTotal}</h2>
                    </body>
                    </html>
                `);
            },
            error: function(xhr, status, error) {
                console.error('Failed to load order details for invoice:', error);
            }
        });
    }

  
    // Event listener for printing the invoice
    $(document).on('click', '.print-invoice', function() {
        const orderId = $(this).data('id');
        printInvoice(orderId);
    });

    // Initial load of all orders
    loadOrders();
});
