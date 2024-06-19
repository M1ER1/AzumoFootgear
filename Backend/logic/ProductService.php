<?php
require_once "../db/dbaccess.php";

class ProductService {

    private DatabaseConnection $dbConnection;

    // Constructor to initialize the database connection
    public function __construct() {
        $this->dbConnection = new DatabaseConnection();
    }

    // Method to add a new product
    public function addProduct($data, $files) {
        // Extract product details from the data array
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $stock = $data['stock'];
        $category = $data['category'];

        // Handle image upload
        $imageUrl = '';
        if (isset($files['image']) && $files['image']['error'] == 0) {
            $image = $files['image'];
            $uploadDir = '../../uploads/';
            $uploadFile = $uploadDir . basename($image['name']);

            error_log('Attempting to upload image: ' . $uploadFile);
            error_log('Temporary image path: ' . $image['tmp_name']);

            // Move the uploaded file to the specified directory
            if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
                $imageUrl = $uploadFile;
            } else {
                throw new Exception("Failed to upload image.");
            }

            // Log the final image URL
            error_log("Final image URL: " . $imageUrl);
        }

        // Insert the product into the database
        $query = "INSERT INTO products (name, description, price, stock, category, image_url) 
                  VALUES (:name, :description, :price, :stock, :category, :image_url)";
        $params = [
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':category' => $category,
            ':image_url' => $imageUrl
        ];

        $this->dbConnection->runQuery($query, $params);
    }

    // Method to get all products, with optional search and category filters
    public function getAllProducts($search = null, $categories = []) {
        $query = "SELECT * FROM products";
        $params = [];

        // Add search filter if provided
        if ($search) {
            $query .= " WHERE name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        // Add category filters if provided
        if (!empty($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $query .= ($search ? ' AND' : ' WHERE') . " category IN ($placeholders)";
            $params = array_merge($params, $categories);
        }

        return $this->dbConnection->runQuery($query, $params);
    }

    // Method to edit an existing product
    public function editProduct($data, $files) {
        // Extract product details from the data array
        $productId = $data['id'];
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $stock = $data['stock'];
        $category = $data['category'];
    
        // Update product details in the database
        $query = "UPDATE products SET name = :name, description = :description, price = :price, stock = :stock, category = :category WHERE id = :id";
        $params = [
            ':id' => $productId,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':category' => $category
        ];
    
        // Handle image upload if a new image is provided
        if (isset($files['image']) && $files['image']['error'] == 0) {
            $image = $files['image'];
            $uploadDir = '../../uploads/';
            $uploadFile = $uploadDir . basename($image['name']);
    
            if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
                $query .= ", image_url = :image_url";
                $params[':image_url'] = $uploadFile;
            } else {
                throw new Exception("Failed to upload image.");
            }
        }
    
        $this->dbConnection->runQuery($query, $params);
    }
    
    // Method to delete a product by ID
    public function deleteProduct($productId) {
        // Query to delete a product from the database
        $query = "DELETE FROM products WHERE id = :id";
        $params = [':id' => $productId];
        error_log('Deleting product with ID: ' . $productId);
        $this->dbConnection->runQuery($query, $params);
    }
}
?>
