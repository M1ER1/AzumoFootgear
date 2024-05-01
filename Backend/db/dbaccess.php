<?php
// Datenbankverbindungskonfiguration
$host = "localhost";         // Datenbank-Host
$user = "web1user";          // Datenbank-Benutzername
$password = "MomoAzur7";     // Datenbank-Passwort
$database = "AzumoFootgear"; // Datenbank-Name

// Create a new mysqli object for database connection
$db_obj = new mysqli($host, $user, $password, $database);

// Check for a connection error
if ($db_obj->connect_error) {
    $error_message = "Connection Error: " . $db_obj->connect_error;
    // Send error message to browser console
    echo "<script>console.error('$error_message');</script>";
    exit();
} else {
    $success_message = "Connected successfully.";
    // Send success message to browser console
    echo "<script>console.log('$success_message');</script>";
}
?>
