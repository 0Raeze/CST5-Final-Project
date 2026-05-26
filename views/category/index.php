<?php
require_once '../../controllers/category.php';
require_once '../../public/database.config.php';

// Initialize controller
$conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $DB_PORT);

// Handle form submissions
$message = "";
$messageType = "";

// Process delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($controller->delete($id)) {
        $message = "Category deleted successfully";
        $messageType = "success";
    } else {
        $message = "Cannot delete category - it may have items associated with it";
        $messageType = "danger";
    }
}

// Process seeding action (for initial setup)
if (isset($_GET['seed'])) {
    $seeded = $controller->seedInitialCategories();
    if ($seeded > 0) {
        $message = "Seeded $seeded initial categories";
        $messageType = "success";
    } else {
        $message = "No new categories to seed (all already exist)";
        $messageType = "info";
    }
}

// Get all categories for display
$categories = $controller->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - Stardew Valley Inventory</title>
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
            <h1 class="font-pixel">Category Management</h1>
            <div>
                <a href="../dashboard/index.php" class="btn btn-outline-secondary me-2">Dashboard</a>
                <a href="index.php?seed=1" class="btn btn-outline-success me-2">Seed Categories</a>
                <a href="form.php" class="btn btn-primary">+ Add New Category</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType === 'success' ? 'success' : ($messageType === 'error' ? 'danger' : 'info') ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <h2 class="font-pixel mb-3">All Categories</h2>

        <?php if (empty($categories)): ?>
            <div class="alert alert-info">
                No categories found. <a href="form.php" class="alert-link">Add your first category</a> or <a href="index.php?seed=1" class="alert-link">seed initial categories</a>.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="font-pixel">ID</th>
                            <th class="font-pixel">Name</th>
                            <th class="font-pixel">Description</th>
                            <th class="font-pixel">Created At</th>
                            <th class="font-pixel">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category->id) ?></td>
                                <td class="font-body"><?= htmlspecialchars($category->name) ?></td>
                                <td class="font-body"><?= htmlspecialchars($category->description) ?></td>
                                <td class="font-mono"><?= htmlspecialchars($category->created_at) ?></td>
                                <td>
                                    <a href="form.php?id=<?= $category->id ?>" class="btn btn-sm btn-outline-primary me-1"><span class="nes-btn is-small">Edit</span></a>
                                    <a href="index.php?delete=<?= $category->id ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this category?')"><span class="nes-btn is-small">Delete</span></a>
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