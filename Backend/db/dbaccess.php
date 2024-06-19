<?php

class DatabaseConnection {
    private $host = "localhost";
    private $username = "web1user";
    private $password = "MomoAzur7";
    private $dbName = "AzumoFootgear";
    private $pdoInstance;

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
        try {
            $this->pdoInstance = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            throw new Exception('Database connection failed: ' . $exception->getMessage());
        }
    }

    public function runQuery($sql, $parameters = []) {
        try {
            $statement = $this->pdoInstance->prepare($sql);
            $statement->execute($parameters);
            return $statement->fetchAll();
        } catch (PDOException $exception) {
            error_log('Database query failed: ' . $exception->getMessage());
            throw new Exception('Database query failed: ' . $exception->getMessage());
        }
    }

    public function getLastInsertId() {
        return $this->pdoInstance->lastInsertId();
    }
}
?>

