<?php require 'partial/header.php'; ?>

<div class="container my-5">
    <h1 class="font-pixel text-center mb-4">System Settings</h1>
    <div class="card shadow-sm">
        <div class="card-header bg-light font-pixel">
            <h2 class="h5 mb-0">System Maintenance</h2>
        </div>
        <div class="card-body">
            <p class="font-body">
                Use the button below to reset the system to its initial state with all starter data.
                This will clear all custom entries in categories, suppliers, items, and transactions,
                but keep user accounts.
            </p>
            <form method="POST" class="d-grid gap-2">
                <button type="submit" name="reset_system" class="btn btn-danger btn-lg">
                    <span class="nes-btn is-small">🔄 System Reset / Re-Seed Database</span>
                </button>
                <p class="font-body text-muted small mt-2">
                    <strong>Warning:</strong> This action cannot be undone!
                </p>
            </form>
        </div>
    </div>
</div>

<?php
// Handle reset logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset_system"])) {
    require_once 'public/database.config.php';
    require_once 'controllers/category.php';
    require_once 'controllers/supplier.php';
    require_once 'controllers/item.php';

    $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $DB_PORT);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Disable foreign key checks temporarily
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Clear tables (preserve accounts)
    $conn->query("TRUNCATE TABLE transactions");
    $conn->query("TRUNCATE TABLE items");
    $conn->query("TRUNCATE TABLE suppliers");
    $conn->query("TRUNCATE TABLE categories");

    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    $conn->close();

    // Re-seed initial data
    $categoryController = new CategoryController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
    $supplierController = new SupplierController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
    $itemController = new ItemController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    $catsSeeded = $categoryController->seedInitialCategories();
    $suppSeeded = $supplierController->seedInitialSuppliers();
    $itemsSeeded = $itemController->seedInitialItems();

    echo "<div class='alert alert-success mt-4'>System reset complete! Seeded: {$catsSeeded} categories, {$suppSeeded} suppliers, {$itemsSeeded} items.</div>";
}
?>

<?php require 'partial/footer.php'; ?>