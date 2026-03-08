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
    private static $instance = null;
    
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
    
    /**
     * Get singleton instance of Database.
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
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
                throw new Exception("Prepare failed: " . $this->conn->error);
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
                throw new Exception("Execute failed: " . $stmt->error);
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
