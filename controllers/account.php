<?php
// This will contain all the processes/functions
// that affect the Account model
require_once __DIR__ . '/../public/database.config.php';
class AccountController {
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

    function register($username, $password) {
        // Secure prepared statement to prevent SQL injection
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return $id;
        } else {
            $stmt->close();
            return false;
        }
    }

    function login($username, $password) {
        // Secure prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("SELECT id, username, password FROM accounts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_password);
            $stmt->fetch();

            if (password_verify($password, $db_password)) {
                $stmt->close();
                return $id;
            }
        }

        $stmt->close();
        return false;
    }

    function update($id, $username, $password) {
        // Secure prepared statement to prevent SQL injection
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("UPDATE accounts SET username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $hashed_password, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    function delete($id, $username, $password) {
        // Secure prepared statement to prevent SQL injection
        // First verify the account exists and password is correct
        $stmt = $this->conn->prepare("SELECT password FROM accounts WHERE id = ? AND username = ?");
        $stmt->bind_param("is", $id, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_password);
            $stmt->fetch();

            if (password_verify($password, $db_password)) {
                $stmt->close();

                // Now delete the account
                $delete_stmt = $this->conn->prepare("DELETE FROM accounts WHERE id = ?");
                $delete_stmt->bind_param("i", $id);

                if ($delete_stmt->execute()) {
                    $delete_stmt->close();
                    return true;
                } else {
                    $delete_stmt->close();
                    return false;
                }
            }
        }

        $stmt->close();
        return false;
    }
}