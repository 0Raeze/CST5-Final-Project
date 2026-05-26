<?php
require_once '../../controllers/category.php';
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

$controller = new CategoryController($host, $user, $pass, $dbname, $db_port);

// Determine if we are editing or adding
$isEdit = false;
$category = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $category = $controller->readById($id);
    if (!$category) {
        // Category not found
        header('Location: index.php');
        exit();
    }
    $isEdit = true;
}

// Handle form submission
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save"])) {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // Basic validation
    if (empty($name)) {
        $message = "Category name is required";
        $messageType = "danger";
    } else {
        if ($isEdit && $category) {
            // Update existing category
            if ($controller->update($category->id, $name, $description)) {
                $message = "Category updated successfully";
                $messageType = "success";
            } else {
                $message = "Failed to update category";
                $messageType = "danger";
            }
        } else {
            // Create new category
            $newId = $controller->create($name, $description);
            if ($newId !== false) {
                $message = "Category created successfully";
                $messageType = "success";
                // Clear form for next entry
                $name = "";
                $description = "";
            } else {
                $message = "Failed to create category";
                $messageType = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add New' ?> Category - Stardew Valley Inventory</title>
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

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="font-pixel"><?= $isEdit ? 'Edit' : 'Add New' ?> Category</h1>
            <a href="index.php" class="btn btn-outline-secondary"><span class="nes-btn is-small">← Back to List</span></a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label font-pixel">Category Name *</label>
                                <input type="text" class="form-control font-body" id="name" name="name" required
                                       value="<?= htmlspecialchars($isEdit ? $category->name : '') ?>">
                                <div class="form-text">Enter the category name (e.g., Seeds & Starts)</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label font-pixel">Description</label>
                                <textarea class="form-control font-body" id="description" name="description"
                                          rows="3"><?= htmlspecialchars($isEdit ? $category->description : '') ?></textarea>
                                <div class="form-text">Optional description of what this category contains</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <span class="nes-btn is-small"><?= $isEdit ? 'Update Category' : 'Create Category' ?></span>
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <span class="nes-btn is-small">Cancel</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require '../partial/footer.php'; ?>
</body>
</html>