<?php

// Instantiate the RequestHandler and handle the incoming request
$requestHandler = new RequestHandler();
$requestHandler->handleRequest();

class RequestHandler {

    // Properties for service dependencies
    private $userService;
    private $productService;
    private $orderService;
    private $cartService;
    private $couponService;

    // Constructor to initialize service dependencies
    public function __construct() {
        require_once "../logic/UserServices.php";
        require_once "../logic/OrderService.php";
        require_once "../logic/ProductService.php";
        require_once "../logic/CouponService.php";
        require_once "../logic/CartService.php";
        
        $this->userService = new UserService();
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
        $this->couponService = new CouponService();
        $this->cartService = new CartService();
    }

    // Main method to handle the incoming request
    public function handleRequest() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $resource = $_GET['resource'] ?? '';
        $params = $_GET['params'] ?? [];
        
        // Route the request based on HTTP method and resource
        switch ($requestMethod) {
            case 'GET':
                $this->handleGetRequest($resource, $params);
                break;
            case 'POST':
                $this->handlePostRequest($resource, $params);
                break;
            case 'PUT':
                $this->handlePutRequest($resource, $params);
                break;
            case 'DELETE':
                $this->handleDeleteRequest($resource, $params);
                break;
            default:
                $this->error(501, "Method not implemented");
                break;
        }
    }

    // Handle GET requests
    private function handleGetRequest(string $resource, array $params) {
        switch ($resource) {
            case 'users':
                $this->success(200, $this->userService->fetchAllUsers());
                break;
            case 'user':
                $this->success(200, $this->userService->getUserById($params['id']));
                break;
            case 'products':
                $search = $_GET['search'] ?? null;
                $categories = $_GET['categories'] ?? [];
                $products = $this->productService->getAllProducts($search, $categories);
                $this->respondWithSuccess($products);
                break;
            case 'cart':
                $this->success(200, $this->cartService->getCartItems());
                break;
            case 'cart_count':
                $cartCount = $this->cartService->getCartCount();
                $this->respondWithSuccess(['count' => $cartCount]);
                break;
            case 'orders':
                if (!isset($_SESSION['user_id'])) {
                    $this->respondWithError(401, "User not logged in");
                } else {
                    $userId = $_SESSION['user_id'];
                    $orders = $this->orderService->getOrdersByUserId($userId);
                    $this->respondWithSuccess($orders);
                }
                break;
            case 'orderDetails':
                if (!isset($_GET['orderId'])) {
                    $this->error(400, "Missing order ID");
                } else {
                    try {
                        $this->success(200, $this->orderService->getOrderDetailsById($_GET['orderId']));
                    } catch (Exception $e) {
                        $this->error(404, $e->getMessage());
                    }
                }
                break;
            case 'userOrders':
                $userId = $_GET['userId'] ?? null;
                if ($userId) {
                    $orders = $this->orderService->getOrdersByUserId($userId);
                    $this->respondWithSuccess($orders);
                } else {
                    $this->respondWithError(400, "Missing user ID");
                }
                break;
            case 'coupons':
                $this->success(200, $this->couponService->getCoupons());
                break;
            default:
                $this->error(404, "Resource not found");
                break;
        }
    }

    // Handle POST requests
    private function handlePostRequest(string $resource, array $params) {
        $requestData = $_POST;
        $files = $_FILES;

        try {
            switch ($resource) {
                case 'login':
                    // Handle user login
                    $requestData = $this->getRequestBody();
                    $result = $this->userService->loginUser($requestData);
                    if ($result) {
                        $this->respondWithSuccess(['success' => true]);
                    } else {
                        $error = $this->userService->getLastError();
                        $this->respondWithError(401, $error ? $error : "Invalid username or password");
                    }
                    break;
                case 'register':
                    // Handle user registration
                    $requestData = $this->getRequestBody();
                    $user = $this->userService->registerUser($requestData);
                    if ($user) {
                        $this->respondWithSuccess(['success' => true]);
                    } else {
                        $error = $this->userService->getLastError();
                        $this->respondWithError(400, $error ? $error : "User registration failed");
                    }
                    break;
                case 'update_profile':
                    // Handle updating user profile
                    $requestData = $this->getRequestBody();
                    $result = $this->userService->updateUserProfile($requestData);
                    if ($result) {
                        $this->respondWithSuccess(['success' => true]);
                    } else {
                        $this->respondWithError(400, "Profile update failed");
                    }
                    break;
                case 'update_product':
                    // Editing an existing product
                    $this->productService->editProduct($_POST, $_FILES);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'delete_product':
                    // Deleting a product
                    $productId = $_POST['id'];
                    $this->productService->deleteProduct($productId);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'product':
                    // Handle adding a new product
                    $this->productService->addProduct($_POST, $_FILES);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'order':
                    // Handle creating a new order
                    $requestData = $this->getRequestBody();
                    $this->orderService->createOrder($requestData);
                    unset($_SESSION['cart']);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'update_user':
                    $this->userService->updateUser($_POST);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'deactivate_user':
                    $userId = $requestData['id'];
                    $this->userService->deactivateUser($userId);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'remove_product':
                    $orderId = $requestData['orderId'];
                    $productId = $requestData['productId'];
                    $this->orderService->removeProductFromOrder($orderId, $productId);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'create_coupon':
                    $code = $this->couponService->generateRandomCode();
                    $value = $requestData['value'];
                    $expiryDate = $requestData['expiryDate'];
                    $this->couponService->createCoupon($code, $value, $expiryDate);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'apply_coupon':
                    $code = $requestData['code'];
                    $coupon = $this->couponService->validateAndUseCoupon($code);
                    $this->respondWithSuccess(['coupon' => $coupon]);
                    break;
                case 'add_to_cart':
                    $productId = $requestData['productId'];
                    $quantity = $requestData['quantity'];
                    $this->cartService->addToCart($productId, $quantity);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                case 'remove_from_cart':
                    $productId = $requestData['productId'];
                    $this->cartService->removeItemFromCart($productId);
                    $this->respondWithSuccess(['success' => true]);
                    break;
                default:
                    $this->error(500, "Post request failed");
                    break;
            }
        } catch (Exception $e) {
            $this->respondWithError(400, $e->getMessage());
        }
    }

    // Handle PUT requests
    private function handlePutRequest(string $resource, array $params) {
        parse_str(file_get_contents('php://input'), $putData); // Parse the form data from the PUT request
    
        if ($_SERVER['CONTENT_TYPE'] === 'multipart/form-data') {
            $putData = $_POST;
        }
    
        switch ($resource) {
            case 'product':
                error_log("PUT Request Data: " . print_r($putData, true));
                error_log("PUT Request Files: " . print_r($_FILES, true));
                $this->productService->editProduct($putData, $_FILES);
                $this->respondWithSuccess(['success' => true]);
                break;
            default:
                $this->error(500, "Put request failed");
                break;
        }
    }

    // Handle DELETE requests
    private function handleDeleteRequest(string $resource, array $params) {
        switch ($resource) {
            case 'product':
                $productId = $params['id'] ?? null;
                if ($productId) {
                    $this->productService->deleteProduct($productId);
                    $this->respondWithSuccess(['success' => true]);
                } else {
                    $this->error(400, "Missing product ID");
                }
                break;
            default:
                $this->error(404, "Resource not found");
                break;
        }
    }

    // Helper method to format success response and exit
    private function success(int $code, mixed $data) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Helper method to format error response and exit
    private function error(int $code, $msg) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $msg]);
        exit;
    }

    // Helper method to get the POST request body if it was JSON and returns it as JSON decoded
    private function getRequestBody(): mixed {
        // Get the request body
        $requestBody = file_get_contents('php://input');
        $requestData = json_decode($requestBody, true);
        // Check if the request body is valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error(400, "Invalid request body");
        }
        return $requestData;
    }

    // Helper method to respond with success and exit
    private function respondWithSuccess($data) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Helper method to respond with error and exit
    private function respondWithError(int $code, string $message) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }
}
?>
