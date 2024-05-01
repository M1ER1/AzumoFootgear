<?php
session_start();

function sendJsonResponse($success, $message, $additionalData = []) {
    header('Content-Type: application/json');
    $response = ['success' => $success, 'message' => $message] + $additionalData;
    echo json_encode($response);
    exit;
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $userData = [
        'username' => $_SESSION['username'] ?? 'N/A',
        'role' => $_SESSION['role'] ?? 'N/A',
        'loggedin' => true  // Setzt loggedin explizit auf true
    ];
    sendJsonResponse(true, "User is logged in.", $userData);
} else {
    sendJsonResponse(false, "User is not logged in.", ['loggedin' => false]);
}
?>
