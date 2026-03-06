<?php
/**
 * Database — Concrete implementation of DatabaseInterface
 * 
 * SRP: This file only holds the Database class.
 * Config constants live in app.php.
 * DIP: Implements DatabaseInterface so consumers depend on the abstraction.
 */
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/DatabaseInterface.php';

class Database implements DatabaseInterface {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    public function close() {
        $this->conn->close();
    }
}
?>
