$(document).ready(function() {
    // Form submission using Ajax
    $('#register-form').submit(function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Serialize form data
        var formData = $(this).serialize();

        // Send Ajax request
        $.ajax({
            type: 'POST',
            url: '../../logic/registerlogic.php',
            data: formData,
            success: function(response) {
                console.log("AJAX request successful"); // Debugging statement
                console.log(response); // Debugging statement
                // Redirect to index.html
                window.location.href = '../Sites/index.html';
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error("AJAX request error:", error); // Debugging statement
                console.error(xhr.responseText); // Debugging statement
            }
        });
    });
});
