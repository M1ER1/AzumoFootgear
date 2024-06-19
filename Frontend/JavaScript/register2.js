

$(document).ready(function() {
    $('#registerForm').submit(function(event) {
        event.preventDefault();

        if (!validateForm()) {
            return;
        }

        const formData = {
            action: 'register',
            username: $('#username').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            firstname: $('#firstname').val(),
            lastname: $('#lastname').val(),
            gender: $('#gender').val(),
            address: $('#address').val(),
            postcode: $('#postcode').val(),
            city: $('#city').val(),
            payment_method: $('#payment_method').val()
        };

        console.log('Form Data:', formData); // Log form data

        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=register',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                console.log('Response:', response); // Log response
                if (response.success) {
                    window.location.href = '../Sites/login2.html';
                } else {
                    $('#registerMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Status:', status);
                console.dir(xhr);
                console.log('Response Text:', xhr.responseText); // Log full response text
                $('#registerMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });

    function validateForm() {
        // Check the gender field (not empty)
        var gender = $('#gender').val();
        if (gender === "") {
            alert("Gender is required.");
            return false;
        }

        // Check the first name field (not empty and only allowed characters)
        var firstname = $('#firstname').val();
        if (firstname.trim() === '') {
            alert("First name is required.");
            return false;
        } else if (!/^[A-Za-z\sßüäöÄÜÖ\-]+$/.test(firstname)) {
            alert("Invalid first name. Please enter only letters, spaces, hyphens, and 'ß, ä, ö, ü'.");
            return false;
        }

        // Check the last name field (not empty and only allowed characters)
        var lastname = $('#lastname').val();
        if (lastname.trim() === '') {
            alert("Last name is required.");
            return false;
        } else if (!/^[A-Za-z\sßüäöÄÜÖ\-]+$/.test(lastname)) {
            alert("Invalid last name. Please enter only letters, spaces, hyphens, and 'ß, ä, ö, ü'.");
            return false;
        }

        // Check the address field (not empty and only allowed characters)
        var address = $('#address').val();
        if (address.trim() === '') {
            alert("Address is required.");
            return false;
        } else if (!/^[A-Za-z0-9\s\/\-\.ßüäöÄÜÖ]+$/.test(address)) {
            alert("Invalid address. Please enter only letters, numbers, spaces, slashes, hyphens, dots, and 'ß, ä, ö, ü'.");
            return false;
        }

        // Check the postcode field (not empty and only allowed characters)
        var postcode = $('#postcode').val();
        if (postcode.trim() === '') {
            alert("Postcode is required.");
            return false;
        } else if (!/^[0-9]+$/.test(postcode)) {
            alert("Invalid postcode. Please enter only numbers.");
            return false;
        }

        // Check the city field (not empty and only allowed characters)
        var city = $('#city').val();
        if (city.trim() === '') {
            alert("City is required.");
            return false;
        } else if (!/^[a-zA-Z\s-ßüäöÄÜÖ]+$/.test(city)) {
            alert("Invalid city. Please enter only letters, spaces, hyphens, and 'ß, ä, ö, ü'.");
            return false;
        }

        // Check the email field (not empty and only with correct format)
        var email = $('#email').val();
        if (email.trim() === '') {
            alert("Email is required.");
            return false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert("Invalid email format. Please enter a valid email address.");
            return false;
        }

        // Check the username field (not empty and only allowed characters)
        var username = $('#username').val();
        if (username.trim() === '') {
            alert("Username is required.");
            return false;
        } else if (!/^[a-zA-Z0-9]+$/.test(username)) {
            alert("Invalid username. Please enter only letters and numbers.");
            return false;
        }

        // Check the password field (not empty)
        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();
        if (password.trim() === '') {
            alert("Password is required.");
            return false;
        } else if (password.length < 5) {
            alert("Password must be at least 5 characters long.");
            return false;
        } else if (!/[A-Z]/.test(password)) {
            alert("Password must contain at least one uppercase letter.");
            return false;
        } else if (!/[a-z]/.test(password)) {
            alert("Password must contain at least one lowercase letter.");
            return false;
        } else if (!/[0-9]/.test(password)) {
            alert("Password must contain at least one number.");
            return false;
        } else if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }

        // Check the payment method field (not empty)
        var paymentMethod = $('#payment_method').val();
        if (paymentMethod.trim() === '') {
            alert("Payment method is required.");
            return false;
        }

        // If all checks are successful, return true
        return true;
    }
});

