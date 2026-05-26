<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$SERVER_NAME = getenv('SERVER_NAME') ?: ($_ENV['SERVER_NAME'] ?? 'localhost');
$USERNAME    = getenv('USERNAME') ?: ($_ENV['USERNAME'] ?? 'root');
$PASSWORD    = getenv('PASSWORD') ?: ($_ENV['PASSWORD'] ?? '');
$DB_NAME     = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'railway');

// Dummy credentials (replace with database later)
$valid_user = "admin";
$valid_pass = "123456";

$errors = "";
$messages = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    $credentials = new Account($username, $password);
    $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    $result = $controller->login(
        $credentials->username,
        $credentials->password
    );

    if ($result) {
        // Set session user data
        $_SESSION["user_id"] = $result;
        $_SESSION["username"] = $username;
        header("Location: /views/dashboard/index.php");
        exit();
    } else {
        $errors = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stardew Valley Inventory</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- NES.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/nes.css/2.3.0/css/nes.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Plus+Jakarta+Sans:wght@400;500;600&family=Courier+Prime&display=swap" rel="stylesheet">
    <!-- Main stylesheet -->
    <link rel="stylesheet" href="/public/styles.css">
</head>
<body>
    <?php require 'views/partial/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="font-pixel text-center mb-4">Stardew Valley Inventory</h1>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($errors) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($messages)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($messages) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label font-pixel">Username</label>
                                <input type="text" class="form-control font-body" id="username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label font-pixel">Password</label>
                                <input type="password" class="form-control font-body" id="password" name="password" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="login" class="btn btn-primary">
                                    <span class="nes-btn is-small">Login to Farm</span>
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="font-body text-muted">
                                Demo login: admin / 123456<br>
                                <small>(In production, use secure authentication)</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'views/partial/footer.php'; ?>
</body>
</html>