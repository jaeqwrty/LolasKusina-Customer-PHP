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
    
    /**
     * Establish a database connection.
     *
     * @throws RuntimeException If the connection fails.
     */
    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            throw new RuntimeException(
                "Database connection failed: " . $this->conn->connect_error
            );
        }
        
        $this->conn->set_charset("utf8mb4");
        
        // NFR-T03: Ensure database session uses Asia/Manila timezone
        $this->conn->query("SET time_zone = '+08:00'");
    }
    
    /** @inheritDoc */
    public function getConnection() {
        return $this->conn;
    }
    
    /** @inheritDoc */
    public function query($sql) {
        $result = $this->conn->query($sql);
        if ($result === false) {
            throw new RuntimeException("Query failed: " . $this->conn->error);
        }
        return $result;
    }
    
    /** @inheritDoc */
    public function prepare($sql) {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException("Prepare failed: " . $this->conn->error);
        }
        return $stmt;
    }
    
    /** @inheritDoc */
    public function close() {
        $this->conn->close();
    }
}
?>
