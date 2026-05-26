<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$errors = "";
$messages = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Validation
    if (empty($username)) {
        $errors = "Username is required";
    } elseif (empty($password)) {
        $errors = "Password is required";
    } elseif (empty($confirm_password)) {
        $errors = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $errors = "Password must be at least 6 characters long";
    } else {
        // Check if username already exists
        $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        $existingUser = $controller->login($username, ""); // This will return false if user doesn't exist, but we need a better way

        // Actually, let's just try to register and catch the error if username exists
        $credentials = new Account($username, $password);
        $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

        $result = $controller->register(
            $credentials->username,
            $credentials->password
        );

        if ($result === false) {
            $errors = "Username already exists or registration failed";
        } else {
            $messages = "Registration successful! Please login.";
            // Clear form data
            $username = "";
            $password = "";
            $confirm_password = "";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Stardew Valley Inventory</title>
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
                                <input type="text" class="form-control font-body" id="username" name="username" required value="<?= htmlspecialchars($username) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label font-pixel">Password</label>
                                <input type="password" class="form-control font-body" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label font-pixel">Confirm Password</label>
                                <input type="password" class="form-control font-body" id="confirm_password" name="confirm_password" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-success">
                                    <span class="nes-btn is-small">Register Account</span>
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="font-body text-muted">
                                Already have an account? <a href="/login.php" class="alert-link">Login here</a>
                            </p>
                            <p class="font-body text-muted small">
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