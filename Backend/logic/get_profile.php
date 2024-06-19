<?php
require_once('../db/dbaccess.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Nehmen wir an, dass eine SQL-Abfrage die Benutzerdaten basierend auf der Benutzer-ID holt
$query = "SELECT title, first_name, last_name, username, email FROM users WHERE id = ?";
if ($stmt = $db_obj->prepare($query)) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No data found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
$db_obj->close();
?>