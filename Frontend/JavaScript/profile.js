$(document).ready(function () {
    function loadProfileData() {
        $.ajax({
            type: 'GET',
            url: '../../Backend/logic/get_profile.php',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log(response.data); 
                    $('#inputGroupSelect01').val(response.data.title);
                    $('#floatingInputFirstName').val(response.data.first_name);
                    $('#floatingInputLastName').val(response.data.last_name);
                    $('#floatingInputUsername').val(response.data.username);
                    $('#floatingInputEmail').val(response.data.email);
                } else {
                    alert('Failed to load profile data: ' + response.message);
                }
            },
            error: function () {
                alert('An error occurred while loading profile data.');
            }
        });
    }

    loadProfileData();

    $('#register-form').submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '../../Backend/logic/profilelogic.php',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Profile successfully updated.');
                    window.location.href = '../Sites/profile.html';
                } else {
                    alert('Failed to update profile: ' + response.message);
                }
            },
            error: function () {
                alert('An error occurred during the profile update. Please try again.');
            }
        });
    });
});
