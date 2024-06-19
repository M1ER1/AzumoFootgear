$(document).ready(function () {
    // Function to load all products
    function loadProducts() {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=products', // Endpoint to get all products
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                let productsContainer = $('#productsContainer');
                productsContainer.empty(); // Clear any existing content
                response.forEach(product => {
                    // Generate HTML for each product
                    let productHtml = `
                    <div class="card mb-3">
                        <div class="card-body">
                            <form id="productForm-${product.id}" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="${product.id}">
                                <div class="mb-3">
                                    <label for="name-${product.id}" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="name-${product.id}" name="name" value="${product.name}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description-${product.id}" class="form-label">Description</label>
                                    <textarea class="form-control" id="description-${product.id}" name="description" rows="3" required>${product.description}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="price-${product.id}" class="form-label">Price</label>
                                    <input type="number" step="0.01" class="form-control" id="price-${product.id}" name="price" value="${product.price}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stock-${product.id}" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock-${product.id}" name="stock" value="${product.stock}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category-${product.id}" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category-${product.id}" name="category" value="${product.category}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image-${product.id}" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image-${product.id}" name="image" accept="image/*">
                                </div>
                                <button type="button" class="btn btn-primary update-product" data-id="${product.id}">Save Changes</button>
                                <button type="button" class="btn btn-danger delete-product" data-id="${product.id}">Delete Product</button>
                            </form>
                        </div>
                    </div>`;
                    productsContainer.append(productHtml); // Append product HTML to the container
                });

                // Event listener for updating a product
                $(document).on('click', '.update-product', function () {
                    const id = $(this).data('id');
                    const form = $(`#productForm-${id}`)[0];
                    const formData = new FormData(form);

                    $.ajax({
                        url: '../../Backend/Handler/RequestHandler.php?resource=update_product', // Endpoint to update a product
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('#productMessage').html('<div class="alert alert-success">Product updated successfully</div>'); // Success message
                            loadProducts(); // Reload products after update
                        },
                        error: function (xhr, status, error) {
                            console.error('Failed to update product:', error);
                            $('#productMessage').html('<div class="alert alert-danger">Failed to update product</div>'); // Error message
                        }
                    });
                });

                // Event listener for deleting a product
                $(document).on('click', '.delete-product', function () {
                    const id = $(this).data('id');
                    if (confirm('Are you sure you want to delete this product?')) {
                        $.ajax({
                            url: '../../Backend/Handler/RequestHandler.php?resource=delete_product', // Endpoint to delete a product
                            type: 'POST',
                            data: { id: id },
                            success: function (response) {
                                $('#productMessage').html('<div class="alert alert-success">Product deleted successfully</div>'); // Success message
                                loadProducts(); // Reload products after deletion
                            },
                            error: function (xhr, status, error) {
                                console.error('Failed to delete product:', error);
                                $('#productMessage').html('<div class="alert alert-danger">Failed to delete product</div>'); // Error message
                            }
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Failed to load products:', xhr.responseText); // Log error
            }
        });
    }

    // Initial load of products when the document is ready
    loadProducts();
});
