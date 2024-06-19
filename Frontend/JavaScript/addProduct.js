$(document).ready(function () {
    // Event handler for product form submission
    $('#productForm').submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(this); // Create a FormData object from the form

        // AJAX request to add a new product
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=product', // Endpoint to add a product
            type: 'POST',
            data: formData, // Send form data
            processData: false, // Do not process the data
            contentType: false, // Do not set content type
            success: function (response) {
                // Display success message and reset the form
                $('#productMessage').html('<div class="alert alert-success">Product added successfully</div>');
                $('#productForm')[0].reset();
            },
            error: function (xhr, status, error) {
                console.error('Failed to add product:', error); // Log error
                $('#productMessage').html('<div class="alert alert-danger">Failed to add product</div>'); // Display error message
            }
        });
    });
});
