$(document).ready(function() {
    $('#register-form').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize(); // Verwendet jQuery, um Formulardaten zu serialisieren

        $.ajax({
            type: 'POST',
            url: '../../Backend/logic/registerlogic.php',
            data: formData, 
            success: function(data) {
                window.location.href = '../Sites/login.html'; 
            },
            error: function() {
                alert('An error occurred. Please try again later.');
            }
        });
    });
});