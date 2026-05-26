<?php
require_once '../../controllers/supplier.php';
require_once '../../public/database.config.php';

$host     = getenv('SERVER_NAME') ?: ($_ENV['SERVER_NAME'] ?? 'mysql.railway.internal');
$user     = getenv('USERNAME') ?: ($_ENV['USERNAME'] ?? 'root');
$pass     = getenv('PASSWORD') ?: ($_ENV['PASSWORD'] ?? '');
$dbname   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'railway');
$db_port  = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306);

$conn = new mysqli($host, $user, $pass, $dbname, $db_port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
$message = "";
$messageType = "";

// Process delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($controller->delete($id)) {
        $message = "Supplier deleted successfully";
        $messageType = "success";
    } else {
        $message = "Cannot delete supplier - it may have items associated with it";
        $messageType = "danger";
    }
}

// Process seeding action (for initial setup)
if (isset($_GET['seed'])) {
    $seeded = $controller->seedInitialSuppliers();
    if ($seeded > 0) {
        $message = "Seeded $seeded initial suppliers";
        $messageType = "success";
    } else {
        $message = "No new suppliers to seed (all already exist)";
        $messageType = "info";
    }
}

// Get all suppliers for display
$suppliers = $controller->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management - Stardew Valley Inventory</title>
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
    <?php require '../partial/header.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="font-pixel">Supplier Management</h1>
            <div>
                <a href="../dashboard/index.php" class="btn btn-outline-secondary me-2">Dashboard</a>
                <a href="index.php?seed=1" class="btn btn-outline-success me-2">Seed Suppliers</a>
                <a href="form.php" class="btn btn-primary">+ Add New Supplier</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType === 'success' ? 'success' : ($messageType === 'error' ? 'danger' : 'info') ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <h2 class="font-pixel mb-3">All Suppliers</h2>

        <?php if (empty($suppliers)): ?>
            <div class="alert alert-info">
                No suppliers found. <a href="form.php" class="alert-link">Add your first supplier</a> or <a href="index.php?seed=1" class="alert-link">seed initial suppliers</a>.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="font-pixel">ID</th>
                            <th class="font-pixel">Name</th>
                            <th class="font-pixel">Contact Person</th>
                            <th class="font-pixel">Phone</th>
                            <th class="font-pixel">Email</th>
                            <th class="font-pixel">Address</th>
                            <th class="font-pixel">Created At</th>
                            <th class="font-pixel">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?= htmlspecialchars($supplier->id) ?></td>
                                <td class="font-body"><?= htmlspecialchars($supplier->name) ?></td>
                                <td class="font-body"><?= htmlspecialchars($supplier->contact_person) ?></td>
                                <td class="font-mono"><?= htmlspecialchars($supplier->phone) ?></td>
                                <td class="font-body"><?= htmlspecialchars($supplier->email) ?></td>>
                                <td class="font-body"><?= htmlspecialchars($supplier->address) ?></td>
                                <td class="font-mono"><?= htmlspecialchars($supplier->created_at) ?></td>
                                <td>
                                    <a href="form.php?id=<?= $supplier->id ?>" class="btn btn-sm btn-outline-primary me-1"><span class="nes-btn is-small">Edit</span></a>
                                    <a href="index.php?delete=<?= $supplier->id ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this supplier?')"><span class="nes-btn is-small">Delete</span></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php require '../partial/footer.php'; ?>
</body>
</html>