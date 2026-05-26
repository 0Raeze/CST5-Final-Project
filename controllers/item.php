<?php
// Item Controller
require_once __DIR__ . '/../public/database.config.php';
require_once __DIR__ . '/../models/item.php';

class ItemController {
    // Properties
    private $conn;

    function __construct($server_name, $username, $password, $db_name, $db_port = 3306)
    {
        // Add the port to the mysqli constructor
        $this->conn = new mysqli(
            $server_name,
            $username,
            $password,
            $db_name,
            $db_port
        );

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // CREATE - Add new item
    function create($sku, $name, $description = "", $category_id = 0, $supplier_id = 0,
                   $stock_quantity = 0, $purchase_price = 0.0, $selling_price = 0.0) {
        // Validation
        if ($stock_quantity < 0 || $purchase_price < 0 || $selling_price < 0) {
            return false; // Negative values not allowed
        }

        // Check if SKU already exists
        $checkStmt = $this->conn->prepare("SELECT id FROM items WHERE sku = ?");
        $checkStmt->bind_param("s", $sku);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $checkResult->free();
            $checkStmt->close();
            return false; // SKU must be unique
        }
        $checkResult->free();
        $checkStmt->close();

        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("INSERT INTO items (sku, name, description, category_id, supplier_id, stock_quantity, purchase_price, selling_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiiss", $sku, $name, $description, $category_id, $supplier_id, $stock_quantity, $purchase_price, $selling_price);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return $id;
        } else {
            $stmt->close();
            return false;
        }
    }

    // READ - Get all items with optional filters
    function readAll($searchTerm = "", $categoryId = 0) {
        $items = [];

        $sql = "SELECT i.id, i.sku, i.name, i.description, i.category_id, i.supplier_id,
                       i.stock_quantity, i.purchase_price, i.selling_price, i.created_at,
                       c.name as category_name, s.name as supplier_name
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN suppliers s ON i.supplier_id = s.id
                WHERE 1=1";

        $params = [];
        $types = "";

        if (!empty($searchTerm)) {
            $sql .= " AND (i.name LIKE ? OR i.sku LIKE ?)";
            $searchParam = "%" . $searchTerm . "%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= "ss";
        }

        if ($categoryId > 0) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }

        $sql .= " ORDER BY i.name";

        if (!empty($types)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $items[] = new Item(
                    $row['sku'],
                    $row['name'],
                    $row['description'],
                    $row['category_id'],
                    $row['supplier_id'],
                    $row['stock_quantity'],
                    $row['purchase_price'],
                    $row['selling_price'],
                    $row['created_at'],
                    $row['id']
                );
                // Attach related names for display
                $items[count($items)-1]->category_name = $row['category_name'] ?? '';
                $items[count($items)-1]->supplier_name = $row['supplier_name'] ?? '';
            }
        }
        $result->free();
        if (!empty($types)) {
            $stmt->close();
        }
        return $items;
    }

    // READ - Get single item by ID
    function readById($id) {
        $stmt = $this->conn->prepare("SELECT i.id, i.sku, i.name, i.description, i.category_id, i.supplier_id,
                                           i.stock_quantity, i.purchase_price, i.selling_price, i.created_at,
                                           c.name as category_name, s.name as supplier_name
                                    FROM items i
                                    LEFT JOIN categories c ON i.category_id = c.id
                                    LEFT JOIN suppliers s ON i.supplier_id = s.id
                                    WHERE i.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item = new Item(
                $row['sku'],
                $row['name'],
                $row['description'],
                $row['category_id'],
                $row['supplier_id'],
                $row['stock_quantity'],
                $row['purchase_price'],
                $row['selling_price'],
                $row['created_at'],
                $row['id']
            );
            // Attach related names
            $item->category_name = $row['category_name'] ?? '';
            $item->supplier_name = $row['supplier_name'] ?? '';

            $result->free();
            $stmt->close();
            return $item;
        }

        $result->free();
        $stmt->close();
        return null;
    }

    // UPDATE - Update existing item
    function update($id, $sku, $name, $description = "", $category_id = 0, $supplier_id = 0,
                   $stock_quantity = 0, $purchase_price = 0.0, $selling_price = 0.0) {
        // Validation
        if ($stock_quantity < 0 || $purchase_price < 0 || $selling_price < 0) {
            return false; // Negative values not allowed
        }

        // Check if SKU already exists for another item
        $checkStmt = $this->conn->prepare("SELECT id FROM items WHERE sku = ? AND id != ?");
        $checkStmt->bind_param("si", $sku, $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $checkResult->free();
            $checkStmt->close();
            return false; // SKU must be unique
        }
        $checkResult->free();
        $checkStmt->close();

        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("UPDATE items SET sku = ?, name = ?, description = ?, category_id = ?, supplier_id = ?, stock_quantity = ?, purchase_price = ?, selling_price = ? WHERE id = ?");
        $stmt->bind_param("sssiiissi", $sku, $name, $description, $category_id, $supplier_id, $stock_quantity, $purchase_price, $selling_price, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // DELETE - Remove item
    function delete($id) {
        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Utility: Get all categories for dropdown
    function getAllCategories() {
        $categories = [];
        $result = $this->conn->query("SELECT id, name FROM categories ORDER BY name");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        $result->free();
        return $categories;
    }

    // Utility: Get all suppliers for dropdown
    function getAllSuppliers() {
        $suppliers = [];
        $result = $this->conn->query("SELECT id, name FROM suppliers ORDER BY name");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
        }
        $result->free();
        return $suppliers;
    }

    // Utility: Seed initial items
    function seedInitialItems() {
        // First ensure we have categories and suppliers
        $categories = $this->getAllCategories();
        $suppliers = $this->getAllSuppliers();

        if (empty($categories) || empty($suppliers)) {
            return 0; // Can't seed items without categories/suppliers
        }

        // Find required category and supplier IDs
        $seedsStartsId = null;
        $artisanGoodsId = null;
        $livestockFeedId = null;

        $pierreId = null;
        $jojaId = null;
        $marnieId = null;

        foreach ($categories as $category) {
            if ($category['name'] === 'Seeds & Starts') $seedsStartsId = $category['id'];
            if ($category['name'] === 'Artisan Goods') $artisanGoodsId = $category['id'];
            if ($category['name'] === 'Livestock & Feed') $livestockFeedId = $category['id'];
        }

        foreach ($suppliers as $supplier) {
            if ($supplier['name'] === "Pierre's General Store") $pierreId = $supplier['id'];
            if ($supplier['name'] === "JojaCorp") $jojaId = $supplier['id'];
            if ($supplier['name'] === "Marnie's Ranch") $marnieId = $supplier['id'];
        }

        // Define items to seed
        $itemsToSeed = [
            // SKU, Name, Description, Category ID, Supplier ID, Stock, Purchase Price, Selling Price
            ['PARSNIP', 'Parsnip Seeds', 'Seeds for planting parsnips', $seedsStartsId, $pierreId, 50, 0.5, 1.0],
            ['JOJACOLA', 'Joja Cola', 'Refreshing cola from JojaCorp', $artisanGoodsId, $jojaId, 100, 0.3, 0.7],
            ['GOATCHEESE', 'Goat Cheese', 'Artisan cheese from goat milk', $artisanGoodsId, $marnieId, 15, 1.0, 2.5],
            ['HAY', 'Hay', 'Dried grass for animal feed', $livestockFeedId, $marnieId, 200, 0.1, 0.3]
        ];

        $seeded = 0;
        foreach ($itemsToSeed as $itemData) {
            // Check if item with this SKU already exists
            $checkStmt = $this->conn->prepare("SELECT id FROM items WHERE sku = ?");
            $checkStmt->bind_param("s", $itemData[0]);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows == 0) {
                // Item doesn't exist, insert it
                $checkResult->free();
                $checkStmt->close();

                $insertStmt = $this->conn->prepare("INSERT INTO items (sku, name, description, category_id, supplier_id, stock_quantity, purchase_price, selling_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insertStmt->bind_param("sssiiiss", $itemData[0], $itemData[1], $itemData[2], $itemData[3], $itemData[4], $itemData[5], $itemData[6], $itemData[7]);
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