<?php
class Database {
    // This is the internal property your app is looking for!
    public $conn;

    public function getConnection() {
        $this->conn = null;

        if (file_exists(__DIR__.'/../.env')) {
            $lines = file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
                    continue;
                }
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                putenv("$name=$value");
            }
        }

        $SERVER_NAME = getenv('SERVER_NAME');
        $USERNAME = getenv('USERNAME');
        $PASSWORD = getenv('PASSWORD');
        $DB_NAME = getenv('DB_NAME');

        try {
            $this->conn = new PDO("mysql:host=$SERVER_NAME;dbname=$DB_NAME;charset=utf8mb4", $USERNAME, $PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }
}