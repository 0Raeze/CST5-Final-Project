<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

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
        header("Location: /views/dashboard");
        exit();
    }
}
?>

<?php require 'views/partial/header.php'; ?>

<div class="auth-container flex-center">
    <div class="card auth-card">

        <h1>Login</h1>

<!-- PHP/HTML block -->
<?php if (!empty($message)): ?> 
    <!-- use this syntax to refer variables -->
    <p style="color:green;"><?= $message ?></p>
<?php endif; ?>
<!-- END OF BLOCK -->

<!-- PHP/HTML block -->
<?php if (!empty($errors)): ?> 
    <!-- use this syntax to refer variables -->
    <p style="color:red;"><?= $errors ?></p>
<?php endif; ?>
<!-- END OF BLOCK -->

<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>

    </div>
</div>

<?php require 'views/partial/footer.php'; ?>