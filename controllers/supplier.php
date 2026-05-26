<?php
// Supplier Controller
require_once __DIR__ . '/../public/database.config.php';
require_once __DIR__ . '/../models/supplier.php';

class SupplierController {
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

    // CREATE - Add new supplier
    function create($name, $contact_person = "", $phone = "", $email = "", $address = "") {
        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $contact_person, $phone, $email, $address);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return $id;
        } else {
            $stmt->close();
            return false;
        }
    }

    // READ - Get all suppliers
    function readAll() {
        $suppliers = [];
        $result = $this->conn->query("SELECT id, name, contact_person, phone, email, address, created_at FROM suppliers ORDER BY name");

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $suppliers[] = new Supplier(
                    $row['name'],
                    $row['contact_person'],
                    $row['phone'],
                    $row['email'],
                    $row['address'],
                    $row['created_at'],
                    $row['id']
                );
            }
        }
        $result->free();
        return $suppliers;
    }

    // READ - Get single supplier by ID
    function readById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, contact_person, phone, email, address, created_at FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $supplier = new Supplier(
                $row['name'],
                $row['contact_person'],
                $row['phone'],
                $row['email'],
                $row['address'],
                $row['created_at'],
                $row['id']
            );
            $result->free();
            $stmt->close();
            return $supplier;
        }

        $result->free();
        $stmt->close();
        return null;
    }

    // UPDATE - Update existing supplier
    function update($id, $name, $contact_person = "", $phone = "", $email = "", $address = "") {
        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("UPDATE suppliers SET name = ?, contact_person = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $contact_person, $phone, $email, $address, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // DELETE - Remove supplier
    function delete($id) {
        // First check if supplier has items associated (if we have items table with supplier_id)
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM items WHERE supplier_id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        // If items exist, prevent deletion
        if ($count > 0) {
            return false; // Cannot delete supplier with associated items
        }

        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Utility: Seed initial suppliers
    function seedInitialSuppliers() {
        $suppliers = [
            ["Pierre's General Store", "Pierre", "555-0101", "pierre@stardewvalley.net", "Pierre's General Store, Stardew Valley"],
            ["JojaCorp", "Morris", "555-0102", "morris@joja.co", "JojaMart, Stardew Valley"],
            ["Marnie's Ranch", "Marnie", "555-0103", "marnie@stardewvalley.net", "Marnie's Ranch, Stardew Valley"]
        ];

        $seeded = 0;
        foreach ($suppliers as $supplierData) {
            // Check if supplier already exists
            $checkStmt = $this->conn->prepare("SELECT id FROM suppliers WHERE name = ?");
            $checkStmt->bind_param("s", $supplierData[0]);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows == 0) {
                // Supplier doesn't exist, insert it
                $checkResult->free();
                $checkStmt->close();

                $insertStmt = $this->conn->prepare("INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
                $insertStmt->bind_param("sssss", $supplierData[0], $supplierData[1], $supplierData[2], $supplierData[3], $supplierData[4]);
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