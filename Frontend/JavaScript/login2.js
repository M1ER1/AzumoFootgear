$(document).ready(function() {
    // Event handler for login form submission
    $('#loginForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Collect form data
        const formData = {
            action: 'login',
            username: $('#username').val(),
            password: $('#password').val(),
            rememberMe: $('#rememberMe').is(':checked')
        };

        // AJAX request to login the user
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=login', // URL to handle login
            type: 'POST',
            data: JSON.stringify(formData), // Send form data as JSON
            contentType: 'application/json', // Content type is JSON
            success: function(response) {
                console.log('Response:', response); // Log response for debugging
                if (response.success) {
                    window.location.href = '../Sites/index.html'; // Redirect to index page on success
                } else if (response.message === 'User is not enabled') {
                    $('#loginMessage').html('<div class="alert alert-danger">Your account is disabled. Please contact support.</div>'); // Show error if account is disabled
                } else {
                    $('#loginMessage').html('<div class="alert alert-danger">' + response.message + '</div>'); // Show other error messages
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error); // Log error details
                console.error('Status:', status);
                console.dir(xhr);
                if (xhr.responseJSON && xhr.responseJSON.error === 'User is not enabled') {
                    $('#loginMessage').html('<div class="alert alert-danger">Your account is disabled. Please contact support.</div>'); // Show error if account is disabled
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    $('#loginMessage').html('<div class="alert alert-danger">' + xhr.responseJSON.error + '</div>'); // Show other error messages
                } else {
                    $('#loginMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>'); // Show generic error message
                }
            }
        });
    });
});
