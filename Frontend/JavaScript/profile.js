$(document).ready(function() {
    // Fetch the user profile when the page loads
    fetchProfile();

    // Handle form submission
    $('#register-form').submit(function(event) {
        event.preventDefault();

        const formData = {
            firstname: $('#floatingInputFirstName').val(),
            lastname: $('#floatingInputLastName').val(),
            gender: $('#inputGroupSelect01').val(),
            address: $('#floatingInputAddress').val(),
            postcode: $('#floatingInputPostcode').val(),
            city: $('#floatingInputCity').val(),
            email: $('#floatingInputEmail').val(),
            username: $('#floatingInputUsername').val(),
            password: $('#floatingPasswordCurrent').val(),
            npassword: $('#floatingPasswordNew').val(),
            npassword2: $('#floatingPasswordConfirm').val()
        };

        // Validate new password
        if (formData.npassword !== formData.npassword2) {
            alert('New passwords do not match.');
            return;
        }

        // Send the form data to the server
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=update_profile',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    alert('Profile updated successfully!');
                } else {
                    alert('Profile update failed.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to update profile:', error);
            }
        });
    });

    function fetchProfile() {
        $.ajax({
            url: '../../Backend/Handler/RequestHandler.php?resource=user', // Request profile data from the server
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // If the request is successful, populate the form fields with the received data
                    const data = response.data;
                    $('#floatingInputFirstName').val(data.firstname);
                    $('#floatingInputLastName').val(data.lastname);
                    $('#inputGroupSelect01').val(data.gender);
                    $('#floatingInputAddress').val(data.address);
                    $('#floatingInputPostcode').val(data.postcode);
                    $('#floatingInputCity').val(data.city);
                    $('#floatingInputEmail').val(data.email);
                    $('#floatingInputUsername').val(data.username);
                } else {
                    // If the request fails, alert the user
                    alert('Failed to fetch profile data.');
                }
            },
            error: function(xhr, status, error) {
                // Log any errors encountered during the request
                console.error('Failed to fetch profile data:', error);
            }
        });
    }
    
});
