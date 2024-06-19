<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../logic/UserServices.php";
require_once "../logic/CartService.php";

$userService = new UserService();
$cartService = new CartService();

// Check if the "remember me" cookie is set and validate it
$userService->checkRememberMe();

// Initialize the response array
$response = [
    'success' => false,
];

// If the user is logged in, set success to true and include user details in the response
if (isset($_SESSION['user_id'])) {
    $response['success'] = true;
    $response['username'] = $_SESSION['username'];
    $response['role'] = $_SESSION['role'];
    $response['user_id'] = $_SESSION['user_id']; 

    // Synchronize cart items from local storage to server-side cart if provided
    if (isset($_POST['cartItems'])) {
        $cartItems = json_decode($_POST['cartItems'], true);
        foreach ($cartItems as $item) {
            // Add each item to the server-side cart
            $cartService->addToCart($item['id'], $item['quantity']);
        }
    }
}

// Set the response header to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
?>
