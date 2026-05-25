<?php
// This will contain all the processes/functions
// that affect the Account model
require_once __DIR__ . '../public/database.config.php';
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
    }

    function register($username, $password) {
        // account creation logic
    }

    function login($username, $password) {
        // account reading logic
        return true;
    }

    function update($id, $username, $password) {
        // account updating logic
    }

    function delete($id, $username, $password) {
        // account deletion logic
    }
}