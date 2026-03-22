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
        try {
            $port = defined('DB_PORT') ? DB_PORT : 3306;
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, $port);
            
            if ($this->conn->connect_error) {
                throw new RuntimeException("Database connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            throw new RuntimeException("Database connection error: " . $e->getMessage());
        }
        
        $this->conn->set_charset("utf8mb4");
        
        // NFR-T03: Ensure database session uses Asia/Manila timezone
        $this->conn->query("SET time_zone = '+08:00'");
    }
    
    /**
     * Execute a parameterized query using prepared statements.
     * 
     * @param string $sql SQL query with ? placeholders
     * @param array $params Array of parameters to bind
     * @return array|bool Array of results for SELECT, true for INSERT/UPDATE/DELETE, false on error
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new RuntimeException("Prepare failed: " . $this->conn->error);
            }
            
            // Bind parameters if provided
            if (!empty($params)) {
                $types = '';
                $bindParams = [];
                
                // Determine parameter types
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                    $bindParams[] = $param;
                }
                
                // Bind parameters
                $stmt->bind_param($types, ...$bindParams);
            }
            
            // Execute statement
            if (!$stmt->execute()) {
                throw new RuntimeException("Execute failed: " . $stmt->error);
            }
            
            // Get result
            $result = $stmt->get_result();
            
            if ($result === false) {
                // Not a SELECT query (INSERT, UPDATE, DELETE)
                return true;
            }
            
            // Fetch all results as associative arrays
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            
            $stmt->close();
            return $rows;
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
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
