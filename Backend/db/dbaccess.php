<?php
// Datenbankverbindungskonfiguration
$host = "localhost";         // Datenbank-Host
$user = "web1user";          // Datenbank-Benutzername
$password = "MomoAzur7";     // Datenbank-Passwort
$database = "AzumoFootgear"; // Datenbank-Name

// Create a new mysqli object for database connection
$db_obj = new mysqli($host, $user, $password, $database);