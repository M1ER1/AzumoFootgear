<?php
include_once "../models/User.php";
include_once "../db/dbaccess.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class UserService {

    private $lastError;
    private DatabaseConnection $dbConnection;

    // Constructor to initialize the database connection
    public function __construct() {
        $this->dbConnection = new DatabaseConnection();
    }

    // Fetch all users from the database and return them as an array of User objects
    public function fetchAllUsers(): ?array {
        $sql = "SELECT * FROM Users";
        $results = $this->dbConnection->runQuery($sql);

        $usersList = [];
        foreach ($results as $record) {
            // Create a User object for each record
            $user = new User(
                $record['id'],
                $record['username'],
                $record['email'],
                $record['firstname'],
                $record['lastname'],
                $record['gender'],
                $record['address'],
                $record['postcode'],
                $record['city'],
                $record['payment_method'],
                boolval($record['enabled']),
                $record['role'],
                $record['password']
            );
            $usersList[] = $user;
        }
        return $usersList;
    }

    // Register a new user using the provided form data
    public function registerUser($formData): ?User {
        return $this->saveNewUser($formData);
    }

    // Save a new user to the database
    public function saveNewUser(array $userData): ?User {
        // Extract user details from the data array
        $firstname = $userData['firstname'] ?? '';
        $lastname = $userData['lastname'] ?? '';
        $gender = $userData['gender'] ?? '';
        $address = $userData['address'] ?? '';
        $postcode = $userData['postcode'] ?? '';
        $city = $userData['city'] ?? '';
        $email = $userData['email'] ?? '';
        $username = $userData['username'] ?? '';
        $paymentMethod = $userData['payment_method'] ?? '';
        $password = $userData['password'] ?? '';

        // Validate user inputs
        if (empty($firstname) || !preg_match("/^[A-Za-z\sßüäöÄÜÖ\-]+$/", $firstname)) {
            $this->lastError = "Invalid first name";
            return null;
        }

        if (empty($lastname) || !preg_match("/^[A-Za-z\sßüäöÄÜÖ\-]+$/", $lastname)) {
            $this->lastError = "Invalid last name";
            return null;
        }

        if (empty($address) || !preg_match("/^[A-Za-z0-9\s\/\-\.ßüäöÄÜÖ]+$/", $address)) {
            $this->lastError = "Invalid address";
            return null;
        }

        if (empty($postcode) || !preg_match("/^[0-9]+$/", $postcode)) {
            $this->lastError = "Invalid postcode";
            return null;
        }

        if (empty($city) || !preg_match('/^[A-Za-z\sßüäöÄÜÖ\-]+$/', $city)) {
            $this->lastError = "Invalid city";
            return null;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->lastError = "Invalid email";
            return null;
        }

        if (empty($username) || !preg_match("/^[a-zA-Z0-9]+$/", $username)) {
            $this->lastError = "Invalid username";
            return null;
        }

        if (empty($password) || strlen($password) < 5 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)) {
            $this->lastError = "Invalid password";
            return null;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO Users (firstname, lastname, gender, address, postcode, city, email, username, payment_method, password) 
                VALUES (:firstname, :lastname, :gender, :address, :postcode, :city, :email, :username, :paymentMethod, :password)";
        $parameters = [
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':gender' => $gender,
            ':address' => $address,
            ':postcode' => $postcode,
            ':city' => $city,
            ':email' => $email,
            ':username' => $username,
            ':paymentMethod' => $paymentMethod,
            ':password' => $hashedPassword
        ];

        try {
            $this->dbConnection->runQuery($sql, $parameters);
            $newUserId = $this->dbConnection->getLastInsertId();
            if ($newUserId) {
                return $this->getUserById($newUserId);
            }
        } catch (Exception $e) {
            error_log('Error in UserService::saveNewUser - ' . $e->getMessage());
        }

        return null;
    }

    // Handle user login
    public function loginUser($formData): bool {
        $username = $formData['username'];
        $password = $formData['password'];
        $rememberMe = $formData['rememberMe'];
    
        $sql = "SELECT * FROM Users WHERE username = :username";
        $parameters = [':username' => $username];
        $results = $this->dbConnection->runQuery($sql, $parameters);
    
        if (!empty($results)) {
            $user = $results[0];

            // Check if the user is enabled
            if ($user['enabled'] == 0) {
                $this->lastError = 'User is not enabled';
                return false;
            }
            error_log('User found: ' . print_r($user, true));
            error_log('Input password: ' . $password);
            error_log('Stored hash: ' . $user['password']);
            
            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Handle "remember me" functionality
                if ($rememberMe) {
                    $cookieValue = base64_encode(json_encode(['user_id' => $user['id'], 'token' => $user['password']]));
                    setcookie('rememberMe', $cookieValue, time() + (86400 * 30), "/"); // Set cookie for 30 days
                }
                return true;
            } else {
                error_log('Password verification failed.');
            }
        } else {
            error_log('No user found with username: ' . $username);
        }
        return false;
    }
    
    // Get the last error message
    public function getLastError() {
        return $this->lastError;
    }
    
    // Get user by ID
    public function getUserById($id): ?User {
        $sql = "SELECT * FROM Users WHERE id = :userId";
        $parameters = [':userId' => $id];
        $results = $this->dbConnection->runQuery($sql, $parameters);

        if (empty($results)) {
            return null;
        }

        $record = $results[0];
        return new User(
            $record['id'],
            $record['username'],
            $record['email'],
            $record['firstname'],
            $record['lastname'],
            $record['gender'],
            $record['address'],
            $record['postcode'],
            $record['city'],
            $record['payment_method'],
            boolval($record['enabled']),
            $record['role'],
            $record['password']
        );
    }

    // Update user profile
    public function updateUserProfile($formData): bool {
        $firstname = $formData['firstname'] ?? '';
        $lastname = $formData['lastname'] ?? '';
        $gender = $formData['gender'] ?? '';
        $address = $formData['address'] ?? '';
        $postcode = $formData['postcode'] ?? '';
        $city = $formData['city'] ?? '';
        $email = $formData['email'] ?? '';
        $username = $formData['username'] ?? '';
        $password = $formData['password'] ?? '';
        $npassword = $formData['npassword'] ?? '';
    
        // Verify current password
        $sql = "SELECT password FROM Users WHERE id = :userId";
        $parameters = [':userId' => $_SESSION['user_id']];
        $results = $this->dbConnection->runQuery($sql, $parameters);
    
        if (empty($results) || !password_verify($password, $results[0]['password'])) {
            return false;
        }
    
        // Update password if new password is provided
        $hashedPassword = !empty($npassword) ? password_hash($npassword, PASSWORD_DEFAULT) : $results[0]['password'];
    
        // Update user profile in the database
        $sql = "UPDATE Users SET firstname = :firstname, lastname = :lastname, gender = :gender, address = :address, postcode = :postcode, city = :city, email = :email, username = :username, password = :password WHERE id = :userId";
        $parameters = [
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':gender' => $gender,
            ':address' => $address,
            ':postcode' => $postcode,
            ':city' => $city,
            ':email' => $email,
            ':username' => $username,
            ':password' => $hashedPassword,
            ':userId' => $_SESSION['user_id']
        ];
    
        try {
            $this->dbConnection->runQuery($sql, $parameters);
            return true;
        } catch (Exception $e) {
            error_log('Error in UserService::updateUserProfile - ' . $e->getMessage());
            return false;
        }
    }

    // Deactivate a user by setting their enabled status to false
    public function deactivateUser($userId) {
        $query = "UPDATE users SET enabled = 0 WHERE id = :id";
        $params = [':id' => $userId];
        $this->dbConnection->runQuery($query, $params);
    }

    // Get all orders for a specific user
    public function getUserOrders($userId) {
        $query = "SELECT * FROM orders WHERE fk_customerId = :userId";
        $params = [':userId' => $userId];
        return $this->dbConnection->runQuery($query, $params);
    }

    // Update user details
    public function updateUser($data) {
        $userId = $data['id'];
        $username = $data['username'];
        $email = $data['email'];
        $status = $data['status'];

        // Update user details in the database
        $query = "UPDATE users SET username = :username, email = :email, status = :status WHERE id = :id";
        $params = [
            ':id' => $userId,
            ':username' => $username,
            ':email' => $email,
            ':status' => $status
        ];

        $this->dbConnection->runQuery($query, $params);
    }

    // Check the remember me cookie and set the session if valid
    public function checkRememberMe() {
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberMe'])) {
            $cookieData = json_decode(base64_decode($_COOKIE['rememberMe']), true);
            if ($cookieData && isset($cookieData['user_id'], $cookieData['token'])) {
                $user = $this->getUserById($cookieData['user_id']);
                if ($user && password_verify($cookieData['token'], $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                }
            }
        }
    }
}
?>
