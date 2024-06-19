<?php
class CartService {
    private DatabaseConnection $dbConnection;

    // Constructor to initialize the database connection
    public function __construct() {
        require_once "../db/dbaccess.php";
        $this->dbConnection = new DatabaseConnection;
    }

    // Method to get all items in the cart
    public function getCartItems() {
        return isset($_SESSION['cart']) ? array_values($_SESSION['cart']) : [];
    }

    // Method to get the count of items in the cart
    public function getCartCount() {
        $cartItems = $this->getCartItems();
        return count($cartItems);
    }

    // Method to add an item to the cart
    public function addToCart($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product is already in the cart
        if (isset($_SESSION['cart'][$productId])) {
            throw new Exception("Product already in cart");
        } else {
            // Fetch product details from the database
            $query = "SELECT id, name, price FROM products WHERE id = :id";
            $params = [':id' => $productId];
            $result = $this->dbConnection->runQuery($query, $params);

            // If product exists, add to cart
            if (!empty($result)) {
                $product = $result[0];
                $_SESSION['cart'][$productId] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
        }
    }

    // Method to remove an item from the cart
    public function removeItemFromCart($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    // Method to apply a coupon to the cart
    public function applyCoupon($code) {
        // Fetch coupon details from the database
        $query = "SELECT * FROM coupons WHERE code = :code AND expiry_date >= CURDATE() AND active = 1";
        $params = [':code' => $code];
        $result = $this->dbConnection->runQuery($query, $params);

        // Check if coupon is valid
        if (empty($result)) {
            return ['success' => false, 'error' => 'Invalid or expired coupon code'];
        }

        $coupon = $result[0];
        $cartItems = $this->getCartItems();
        $total = array_reduce($cartItems, function($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        // Calculate new total after applying coupon
        $newTotal = $total - $coupon['value'];
        if ($newTotal < 0) {
            $newTotal = 0;
        }

        return ['success' => true, 'newTotal' => $newTotal];
    }
}
?>
