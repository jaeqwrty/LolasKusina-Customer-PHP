<?php
/**
 * Database Interface — DIP abstraction
 * 
 * All models depend on this interface, never on the concrete Database class.
 * This allows swapping implementations (e.g., for testing) without touching business logic.
 */
interface DatabaseInterface {
    /**
     * Execute a raw SQL query.
     * @param string $sql
     * @return \mysqli_result|bool
     */
    public function query($sql);

    /**
     * Prepare a parameterized statement.
     * @param string $sql
     * @return \mysqli_stmt
     */
    public function prepare($sql);

    /**
     * Get the underlying connection (for insert_id, etc.).
     * @return \mysqli
     */
    public function getConnection();

    /**
     * Close the database connection.
     */
    public function close();

    /**
     * Execute a parameterized query using prepared statements.
     * 
     * @param string $sql    SQL query with ? placeholders
     * @param array  $params Array of parameters to bind
     * @return array|bool    Array of results for SELECT, true for INSERT/UPDATE/DELETE, false on error
     */
    public function execute($sql, $params = []);
}
?>
