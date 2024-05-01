$(document).ready(function () {
    // Funktion, um den Navbar-HTML dynamisch zu generieren
    function loadNavbar() {
        $.ajax({
            type: 'GET',
            url: '../../Backend/logic/check_login.php',
            dataType: 'json',
            success: function (response) {
                let navbarHtml = '<nav class="navbar navbar-expand-lg bg-dark navbar-dark">';
                navbarHtml += '<div class="container">';

                if (response.success) { // Prüft, ob der Benutzer eingeloggt ist
                    navbarHtml += `<a class='navbar-brand'>Welcome, ${response.username}!</a>`;
                } else {
                    navbarHtml += `<a href='../../Frontend/Sites/index.html' class='navbar-brand'>AzumoFootgear</a>`;
                }

                navbarHtml += `
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navmenu">
                        <ul class="navbar-nav ms-auto">`;

                if (response.success) { 
                    navbarHtml += `
                        <li class='nav-item'><a href='../../Frontend/Sites/profile.html' class='nav-link'>Profile</a></li>
                        <li class='nav-item'><a href='' id="logout" class='nav-link'>Logout</a></li>`;
                } else {
                    navbarHtml += `
                        <li class='nav-item'><a href='../../Frontend/Sites/login.html' class='nav-link'>Login</a></li>
                        <li class='nav-item'><a href='../../Frontend/Sites/register.html' class='nav-link'>Register</a></li>`;
                }

                navbarHtml += `</ul></div></div></nav>`;
                $('body').prepend(navbarHtml);
                logoutBTN();
            },
            error: function () {
                alert('Failed to load user data.');
            }
        });
    }

    loadNavbar();
});

function logoutBTN() {
    document.getElementById("logout").addEventListener("click", function() {
        $.ajax({
            type: 'POST',  
            url: '../../Backend/logic/logout.php',  
            success: function() {
                window.location.href = '../../Frontend/Sites/index.html';
            },
            error: function() {
                alert('Logout failed: ' + error);
            }
        });
    });
}

// Die Funktion aufrufen, um sicherzustellen, dass der Listener gesetzt wird
logoutBTN();
