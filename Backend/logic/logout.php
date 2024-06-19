<?php
session_start();

// Löschen aller Session-Variablen
session_unset();

if (isset($_COOKIE['rememberMe'])) {
    setcookie('rememberMe', '', time() - 3600, "/"); // Unset cookie
}

// Zerstören der Session
session_destroy();
?>