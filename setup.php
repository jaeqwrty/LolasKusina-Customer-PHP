<?php
/**
 * Quick Setup Script for Lola's Kusina
 * 
 * This script will help you set up the database and get started quickly.
 * Run this file once to initialize the database.
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lolas_kusina';

echo "=== Lola's Kusina Setup Script ===\n\n";

try {
    // Connect to MySQL server
    echo "1. Connecting to MySQL server...\n";
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    echo "   ✓ Connected successfully\n\n";
    
    // Create database
    echo "2. Creating database...\n";
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ Database '$dbname' created or already exists\n\n";
    } else {
        die("   ✗ Error creating database: " . $conn->error . "\n");
    }
    
    // Select database
    $conn->select_db($dbname);
    
    // Read and execute schema.sql
    echo "3. Creating tables and inserting sample data...\n";
    $schema = file_get_contents(__DIR__ . '/config/schema.sql');
    
    // Split SQL statements and execute them
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        function($stmt) { return !empty($stmt); }
    );
    
    $successCount = 0;
    foreach ($statements as $statement) {
        if ($conn->query($statement) === TRUE) {
            $successCount++;
        } else {
            echo "   Warning: " . $conn->error . "\n";
        }
    }
    
    echo "   ✓ Executed $successCount SQL statements successfully\n\n";
    
    // Verify tables
    echo "4. Verifying tables...\n";
    $result = $conn->query("SHOW TABLES");
    $tableCount = $result->num_rows;
    echo "   ✓ Created $tableCount tables\n\n";
    
    // Close connection
    $conn->close();
    
    echo "=== Setup Complete! ===\n\n";
    echo "Next steps:\n";
    echo "1. Update database credentials in config/database.php if needed\n";
    echo "2. Add your food images to public/images/ folder\n";
    echo "3. Start the development server:\n";
    echo "   cd public\n";
    echo "   php -S localhost:8000\n";
    echo "4. Open your browser and visit: http://localhost:8000\n\n";
    echo "Enjoy Lola's Kusina! 🍽️\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
