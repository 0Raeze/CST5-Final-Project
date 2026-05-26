<?php
// Load environment variables from .env file if it exists
if (file_exists(__DIR__.'/../.env')) {
    $lines = file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments and empty lines
        if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
            continue;
        }
        // Parse key=value
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        // Put into environment
        putenv("$name=$value");
    }
}

$SERVER_NAME = getenv('SERVER_NAME');
$USERNAME = getenv('USERNAME');
$PASSWORD = getenv('PASSWORD');
$DB_NAME = getenv('DB_NAME');