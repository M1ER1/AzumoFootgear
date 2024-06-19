<?php
require_once('../db/dbaccess.php');
session_start();

function sendJsonResponse($isSuccess) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $isSuccess]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // SQL-Abfrage, um den Benutzer zu überprüfen
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $db_obj->query($sql);

    if ($result->num_rows == 1) {
        // Benutzer gefunden
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        // Überprüfen, ob das eingegebene Passwort mit dem gehashten Passwort übereinstimmt
        if (password_verify($password, $hashedPassword)) {
            // Authentifizierung erfolgreich
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            sendJsonResponse(true);
        } else {
            // Authentifizierung fehlgeschlagen
            sendJsonResponse(false);
        }
    } else {
        // Benutzer nicht gefunden
        sendJsonResponse(false);
    }
}

$db_obj->close();
?>