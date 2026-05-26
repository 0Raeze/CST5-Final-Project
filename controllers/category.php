<?php
// Category Controller
require_once __DIR__ . '/../public/database.config.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../models/account.php'; // For auth checks if needed

class CategoryController {
    // Properties
    private $conn;

    function __construct($server_name, $username, $password, $db_name)
    {
        $this->conn = new mysqli(
            $server_name,
            $username,
            $password,
            $db_name
        );

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // CREATE - Add new category
    function create($name, $description = "") {
        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return $id;
        } else {
            $stmt->close();
            return false;
        }
    }

    // READ - Get all categories
    function readAll() {
        $categories = [];
        $result = $this->conn->query("SELECT id, name, description, created_at FROM categories ORDER BY name");

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = new Category(
                    $row['name'],
                    $row['description'],
                    $row['created_at'],
                    $row['id']
                );
            }
        }
        $result->free();
        return $categories;
    }

    // READ - Get single category by ID
    function readById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, description, created_at FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $category = new Category(
                $row['name'],
                $row['description'],
                $row['created_at'],
                $row['id']
            );
            $result->free();
            $stmt->close();
            return $category;
        }

        $result->free();
        $stmt->close();
        return null;
    }

    // UPDATE - Update existing category
    function update($id, $name, $description = "") {
        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // DELETE - Remove category
    function delete($id) {
        // First check if category has items associated
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM items WHERE category_id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        // If items exist, prevent deletion
        if ($count > 0) {
            return false; // Cannot delete category with associated items
        }

        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Utility: Seed initial categories
    function seedInitialCategories() {
        $categories = [
            ['Seeds & Starts', 'Seeds, seedlings, and starter plants'],
            ['Artisan Goods', 'Crafted items like wine, jelly, cheese, etc.'],
            ['Livestock & Feed', 'Animals, animal products, and feed'],
            ['Farm Tools', 'Tools, equipment, and upgrades for the farm']
        ];

        $seeded = 0;
        foreach ($categories as $categoryData) {
            // Check if category already exists
            $checkStmt = $this->conn->prepare("SELECT id FROM categories WHERE name = ?");
            $checkStmt->bind_param("s", $categoryData[0]);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows == 0) {
                // Category doesn't exist, insert it
                $checkResult->free();
                $checkStmt->close();

                $insertStmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                $insertStmt->bind_param("ss", $categoryData[0], $categoryData[1]);
                if ($insertStmt->execute()) {
                    $seeded++;
                }
                $insertStmt->close();
            } else {
                $checkResult->free();
                $checkStmt->close();
            }
        }

        return $seeded;
    }
}
?>