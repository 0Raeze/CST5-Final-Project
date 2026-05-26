<?php
require_once 'public/database.config.php';

// Connect to MySQL server
$conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . $DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
if (!$conn->select_db($DB_NAME)) {
    die("Error selecting database: " . $conn->error);
}

// Function to execute a query and report success/failure
function execute_query($conn, $sql, $tableName) {
    if ($conn->query($sql) === TRUE) {
        echo "Table '$tableName' created successfully or already exists<br>";
    } else {
        echo "Error creating table '$tableName': " . $conn->error . "<br>";
    }
}

// 1. Accounts table (for authentication)
$sql = "CREATE TABLE IF NOT EXISTS accounts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
execute_query($conn, $sql, 'accounts');

// 2. Categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
execute_query($conn, $sql, 'categories');

// 3. Suppliers table
$sql = "CREATE TABLE IF NOT EXISTS suppliers (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
execute_query($conn, $sql, 'suppliers');

// 4. Items table
$sql = "CREATE TABLE IF NOT EXISTS items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT(6) UNSIGNED,
    supplier_id INT(6) UNSIGNED,
    quantity INT(11) DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    reorder_level INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
)";
execute_query($conn, $sql, 'items');

// 5. Transactions table
$sql = "CREATE TABLE IF NOT EXISTS transactions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id INT(6) UNSIGNED NOT NULL,
    type ENUM('in', 'out') NOT NULL,
    quantity INT(11) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id)
)";
execute_query($conn, $sql, 'transactions');

$conn->close();
echo "<br>Setup completed.";
?>