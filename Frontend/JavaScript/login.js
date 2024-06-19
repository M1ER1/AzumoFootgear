$(document).ready(function() {
    $('#login-form').submit(function(event) {
        event.preventDefault();
        
        var formData = $(this).serialize(); // Verwendet jQuery, um Formulardaten zu serialisieren

        $.ajax({
            type: 'POST',
            url: '../../Backend/logic/loginlogic.php',
            data: formData,
            // Weiterleitung nur bei erfolgreichem Login
            success: function() {
                window.location.href = '../Sites/index.html';
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
