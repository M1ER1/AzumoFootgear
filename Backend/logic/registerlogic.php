<?php
require_once('../db/dbaccess.php');

function sendJsonResponse($isSuccess) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $isSuccess]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $firstName = $_POST['fname'] ?? '';
    $lastName = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['pwd'] ?? '';
    $repeatPassword = $_POST['pwd-repeat'] ?? '';

    if ($password !== $repeatPassword) {
        sendJsonResponse(false);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db_obj->prepare("INSERT INTO users (title, first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $title, $firstName, $lastName, $email, $username, $hashedPassword);
        if ($stmt->execute()) {
            sendJsonResponse(true);
        } else {
            sendJsonResponse(false);
        }
        $stmt->close();
    }
    $db_obj->close();
} else {
    sendJsonResponse(false);
}
?>
