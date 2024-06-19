<?php
require_once "../Models/Order.php";
require_once "../db/dbaccess.php";

class OrderService {

    private DatabaseConnection $dbConnection;

    // Constructor to initialize the database connection
    public function __construct() {
        $this->dbConnection = new DatabaseConnection();
    }

    // Method to create a new order
    public function createOrder($post) {
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not logged in.");
        }
    
        // Extract order details from the POST data
        $totalSum = $post['total'];
        $cartitems = $post['cart'];
        $date = date('Y-m-d');
        $coupon = isset($post['couponId']) ? $post['couponId'] : NULL;
        $userId = $_SESSION['user_id'];
    
        // Insert the order into the orders table
        $query = "INSERT INTO orders (total, date, fk_customerId, fk_couponId) 
            VALUES (:total, :date, :customer, :coupon)";
        $params = array(
            ':total' => $totalSum,
            ':date' => $date,
            ':customer' => $userId,
            ':coupon' => $coupon
        );
        $this->dbConnection->runQuery($query, $params);
    
        // Get the ID of the newly created order
        $orderId = $this->dbConnection->getLastInsertId();
    
        // Insert each cart item into the products_in_orders table
        foreach ($cartitems as $item) {
            $productId = $item['id'];
            $quantity = $item['quantity'];
    
            $query = "INSERT INTO products_in_orders (order_id, product_id, quantity) 
                VALUES (:order_id, :product_id, :quantity)";
            $params = array(
                ':order_id' => $orderId,
                ':product_id' => $productId,
                ':quantity' => $quantity
            );
            $this->dbConnection->runQuery($query, $params);
        }

        $this->clearCart($userId);
    }

    private function clearCart($userId) {
        // Clear cart session
        unset($_SESSION['cart']);
    }
    
    // Method to retrieve all orders
    public function getAllOrders() {
        // Implement this function if needed
    }

    // Method to retrieve detailed order information by order ID
    public function getOrderDetailsById($orderId) {
        // Query to get detailed order information
        $query = "
            SELECT
                CONCAT(o.id, u.id) AS invoice_number,
                o.date AS order_date,
                u.id AS customer_id,
                u.firstname AS customer_firstname,
                u.lastname AS customer_lastname,
                u.address AS customer_address,
                u.city AS customer_city,
                u.postcode AS customer_postcode,
                po.product_id AS product_id,
                p.name AS product_name,
                po.quantity,
                p.price,
                po.quantity * p.price AS subtotal,
                o.total AS invoice_total
            FROM
                orders o
                INNER JOIN users u ON o.fk_customerId = u.id
                INNER JOIN products_in_orders po ON o.id = po.order_id
                INNER JOIN products p ON po.product_id = p.id
            WHERE
                o.id = :orderId
        ";
        $params = array(':orderId' => $orderId);
    
        // Execute the query and get the result
        $res = $this->dbConnection->runQuery($query, $params);
    
        // If no order is found, throw an exception
        if (empty($res)) {
            throw new Exception("No order found with this ID.");
        }
    
        $orderItems = [];
        // Process each row to build the order items array
        foreach ($res as $row) {
            $orderLine = array(
                'productId' => $row['product_id'],
                'productName' => $row['product_name'],
                'productQuantity' => $row['quantity'],
                'productPrice' => $row['price'],
                'productSubtotal' => $row['subtotal']
            );
            $orderItems[] = $orderLine;
        }
    
        // Build the invoice info array from the first row
        $row = $res[0];
        $invoiceInfo = array(
            'invoiceNumber' => $row['invoice_number'],
            'orderDate' =>  $row['order_date'],
            'customerId' =>  $row['customer_id'],
            'customerFirstname' =>  $row['customer_firstname'],
            'customerLastname' =>  $row['customer_lastname'],
            'customerAddress' =>  $row['customer_address'],
            'customerCity' =>  $row['customer_city'],
            'customerPostcode' =>  $row['customer_postcode'],
            'invoiceTotal' =>  $row['invoice_total'],
            'orderItems' =>  $orderItems
        );
    
        return $invoiceInfo;
    }
    
    // Method to get all orders by a specific customer ID
    public function getOrderByCustomerId($customerId) {
        // Query to select orders by customer ID
        $query = "SELECT * FROM orders WHERE fk_customerId = :customerId";
        $params = array(':customerId' => $customerId);
    
        // Execute the query and get the result
        $res = $this->dbConnection->runQuery($query, $params);
    
        $orders = [];
        // Process each row to create Order objects
        foreach ($res as $row) {
            $order = new Order(
                $row['id'],
                $row['total'],
                $row['date'],
                $row['fk_customerId'],
                $row['fk_couponId']
            );
            $orders[] = $order;
        }
        return $orders;
    }

    // Method to get orders by user ID, including product details
    public function getOrdersByUserId($userId) {
        // Query to get orders and their product details for a specific user
        $query = "
            SELECT 
                o.id as order_id, 
                o.total, 
                o.date, 
                po.product_id, 
                p.name as product_name, 
                po.quantity, 
                p.price
            FROM orders o
            JOIN products_in_orders po ON o.id = po.order_id
            JOIN products p ON po.product_id = p.id
            WHERE o.fk_customerId = :userId
            ORDER BY o.date DESC
        ";
        $params = [':userId' => $userId];
        $res = $this->dbConnection->runQuery($query, $params);
    
        $orders = [];
        // Process each row to build the orders array with product details
        foreach ($res as $row) {
            $orderId = $row['order_id'];
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $row['order_id'],
                    'total' => $row['total'],
                    'date' => $row['date'],
                    'products' => []
                ];
            }
            $orders[$orderId]['products'][] = [
                'id' => $row['product_id'],
                'name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
        }
        return array_values($orders); // Return as a numerically indexed array
    }

    // Method to get detailed order information
    public function getOrderDetails($orderId) {
        // Query to get detailed order information
        $query = "
            SELECT o.id as order_id, o.total, o.date, c.firstname, c.lastname, c.address, c.city, c.postcode,
                   p.id as product_id, p.name as product_name, p.price, po.quantity
            FROM orders o
            JOIN customers c ON o.fk_customerId = c.id
            JOIN products_in_orders po ON o.id = po.order_id
            JOIN products p ON po.product_id = p.id
            WHERE o.id = :orderId";
        $params = [':orderId' => $orderId];
        return $this->dbConnection->runQuery($query, $params);
    }

    // Method to remove a product from an order
    public function removeProductFromOrder($orderId, $productId) {
        // Delete the product from the order
        $query = "DELETE FROM products_in_orders WHERE order_id = :orderId AND product_id = :productId";
        $params = [':orderId' => $orderId, ':productId' => $productId];
        $this->dbConnection->runQuery($query, $params);
    
        // Recalculate the order total
        $query = "
            SELECT SUM(p.price * po.quantity) as new_total
            FROM products_in_orders po
            JOIN products p ON po.product_id = p.id
            WHERE po.order_id = :orderId
        ";
        $params = [':orderId' => $orderId];
        $result = $this->dbConnection->runQuery($query, $params);
        $newTotal = $result[0]['new_total'] ?? 0;
    
        // Update the order total in the database
        $query = "UPDATE orders SET total = :newTotal WHERE id = :orderId";
        $params = [':newTotal' => $newTotal, ':orderId' => $orderId];
        $this->dbConnection->runQuery($query, $params);
    }
}
?>
