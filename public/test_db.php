<?php
require_once __DIR__ . '/database.config.php';

// 1. Create a new instance of the Database class
$database = new Database();

// 2. Call the method to establish the connection
$db_connection = $database->getConnection();

// 3. Verify if $db_connection (which maps to $this->conn internally) is alive
if ($db_connection instanceof PDO) {
    echo "🎉 Success! The OOP Database connection initialized perfectly using \$this->conn.";
} else {
    echo "❌ Error: Could not instantiate the connection class object.";
}