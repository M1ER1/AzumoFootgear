<?php
require_once('../db/dbaccess.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request or not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$title = $_POST['title'] ?? '';
$currentPassword = $_POST['password'] ?? null;
$newPassword = $_POST['npassword'] ?? null;
$newPasswordConfirm = $_POST['npassword2'] ?? null;

// Retrieve the current hashed password from the database
$query = "SELECT password FROM users WHERE id = ?";
$stmt = $db_obj->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($hashedPasswordFromDB);
$stmt->fetch();
$stmt->close();

// Check if the current password is correct
if ($currentPassword && !password_verify($currentPassword, $hashedPasswordFromDB)) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit;
}

// Check if new passwords match
if ($newPassword && $newPassword !== $newPasswordConfirm) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit;
}

// If a new password is provided, hash it; otherwise, keep the old one
$hashedNewPassword = $newPassword ? password_hash($newPassword, PASSWORD_DEFAULT) : $hashedPasswordFromDB;

$sql = "UPDATE users SET username=?, first_name=?, last_name=?, email=?, title=?, password=? WHERE id=?";
$stmt = $db_obj->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssssssi", $username, $firstName, $lastName, $email, $title, $hashedNewPassword, $userId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile', 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
$db_obj->close();
?>
