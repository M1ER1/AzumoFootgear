<?php
class CouponService {
    private DatabaseConnection $dbConnection;

    // Constructor to initialize the database connection
    public function __construct() {
        require_once "../db/dbaccess.php";
        $this->dbConnection = new DatabaseConnection();
    }

    // Method to create a new coupon
    public function createCoupon($code, $value, $expiryDate) {
        // Ensure the expiry date is not in the past
        $currentDate = date('Y-m-d');
        if ($expiryDate < $currentDate) {
            throw new Exception("Expiry date cannot be in the past.");
        }
        try {
            // Insert the new coupon into the database
            $query = "INSERT INTO coupons (code, value, expiryDate, isUsed) VALUES (:code, :value, :expiryDate, 0)";
            $params = [
                ':code' => $code,
                ':value' => $value,
                ':expiryDate' => $expiryDate
            ];
            $this->dbConnection->runQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error creating coupon: " . $e->getMessage());
            throw $e;
        }
    }

    // Method to get all coupons from the database
    public function getCoupons() {
        $query = "SELECT * FROM coupons";
        return $this->dbConnection->runQuery($query);
    }

    // Method to validate a coupon and mark it as used
    public function validateAndUseCoupon($code) {
        // Check if the coupon is valid and not expired
        $query = "SELECT * FROM coupons WHERE code = :code AND isUsed = 0 AND expiryDate >= CURDATE()";
        $params = [':code' => $code];
        $result = $this->dbConnection->runQuery($query, $params);
        
        if (empty($result)) {
            throw new Exception("Invalid or expired coupon code.");
        }

        // Mark the coupon as used
        $this->markCouponAsUsed($code);
        return $result[0];
    }

    // Method to mark a coupon as used
    private function markCouponAsUsed($code) {
        $query = "UPDATE coupons SET isUsed = 1 WHERE code = :code";
        $params = [':code' => $code];
        $this->dbConnection->runQuery($query, $params);
    }

    // Method to generate a random coupon code
    public function generateRandomCode($length = 5) {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }
}
?>
