<?php
require_once('../db/dbaccess.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from POST
    $title = $_POST['title'] ?? '';
    $firstName = $_POST['fname'] ?? '';
    $lastName = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['pwd'] ?? '';
    $repeatPassword = $_POST['pwd-repeat'] ?? '';

    // Basic validation (more should be added)
    if ($password !== $repeatPassword) {
        die('Passwords do not match.');
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL to insert new record
    $stmt = $db_obj->prepare("INSERT INTO users (title, first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $firstName, $lastName, $email, $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "New user registered successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $db_obj->close();
}
?>
