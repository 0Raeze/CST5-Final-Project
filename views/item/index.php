<?php
// 1. Include the correct controllers
require_once '../../controllers/item.php';
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

// 2. Initialize BOTH controllers
$controller = new ItemController($host, $user, $pass, $dbname, $db_port);
$catController = new CategoryController($host, $user, $pass, $dbname, $db_port);

$searchTerm = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// Handle form submissions
$message = "";
$messageType = "";

// Process delete action (for items)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($controller->delete(intval($_GET['delete']))) {
        $message = "Item deleted successfully";
        $messageType = "success";
    } else {
        $message = "Failed to delete item";
        $messageType = "danger";
    }
}

// Process seeding action
if (isset($_GET['seed'])) {
    $seeded = $controller->seedInitialItems();
    $message = $seeded > 0 ? "Seeded $seeded items" : "No new items to seed";
    $messageType = "info";
}

// Get all items and categories for display
$items = $controller->readAll($searchTerm, $categoryFilter);
$categories = $catController->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management - Stardew Valley Inventory</title>
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
            <h1 class="font-pixel">Item Management</h1>
            <div>
                <a href="../dashboard/index.php" class="btn btn-outline-secondary me-2">Dashboard</a>
                <a href="index.php?seed=1" class="btn btn-outline-success me-2">Seed Items</a>
                <a href="form.php" class="btn btn-primary">+ Add New Item</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType === 'success' ? 'success' : ($messageType === 'error' ? 'danger' : 'info') ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <h2 class="font-pixel mb-4">Item Inventory</h2>

        <!-- Search and Filter Bar -->
        <div class="row g-3 my-3">
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control font-body" placeholder="Search by name or SKU..."
                           name="search" value="<?= htmlspecialchars($searchTerm) ?>" id="searchInput">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <span class="nes-btn is-small">🔍 Search</span>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select font-body" id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->id ?>" <?= $categoryFilter == $category->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary w-100" id="applyFilters">
                    <span class="nes-btn is-small">Apply Filters</span>
                </button>
            </div>
            <div class="col-md-3">
                <a href="index.php" class="btn btn-outline-secondary w-100">
                    <span class="nes-btn is-small">Reset</span>
                </a>
            </div>
        </div>

        <?php if (empty($items)): ?>
            <div class="alert alert-info">
                No items found matching your criteria.
                <?php if (empty($searchTerm) && $categoryFilter == 0): ?>
                    <a href="form.php" class="alert-link">Add your first item</a> or
                    <a href="index.php?seed=1" class="alert-link">seed initial items</a>.
                <?php else: ?>
                    <a href="index.php" class="alert-link">Clear filters to see all items</a>.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="font-pixel">SKU</th>
                            <th class="font-pixel">Item Name</th>
                            <th class="font-pixel">Description</th>
                            <th class="font-pixel">Category</th>
                            <th class="font-pixel">Supplier</th>
                            <th class="font-pixel mono">Stock</th>
                            <th class="font-pixel mono">Purchase Price</th>
                            <th class="font-pixel mono">Selling Price</th>
                            <th class="font-pixel">Created At</th>
                            <th class="font-pixel">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="font-mono"><?= htmlspecialchars($item->sku) ?></td>
                                <td class="font-body d-flex align-items-center">
                                    <?php
                                    // Item sprite placeholder based on SKU - using colored squares for now
                                    $colorMap = [
                                        'PARSNIP' => '#28a745',  // Green for seeds
                                        'JOJACOLA' => '#007bff', // Blue for Joja Cola
                                        'GOATCHEESE' => '#ffc107', // Yellow for cheese
                                        'HAY' => '#6f42c1' // Purple for hay
                                    ];

                                    $bgColor = isset($colorMap[$item->sku]) ? $colorMap[$item->sku] : '#6c757d';
                                    ?>
                                    <div style="width: 24px; height: 24px; background-color: <?= $bgColor ?>; margin-right: 8px; display: inline-block;"></div>
                                    <?= htmlspecialchars($item->name) ?>
                                </td>
                                <td class="font-body"><?= htmlspecialchars($item->description) ?></td>
                                <td class="font-body"><?= htmlspecialchars($item->category_name ?? 'N/A') ?></td>
                                <td class="font-body"><?= htmlspecialchars($item->supplier_name ?? 'N/A') ?></td>
                                <td class="font-mono"><?= htmlspecialchars(number_format($item->stock_quantity, 0)) ?></td>
                                <td class="font-mono d-flex align-items-center">
                                    <!-- Gold coin placeholder -->
                                    <div style="width: 16px; height: 16px; background-color: #ffc107; border-radius: 50%; margin-right: 4px; display: inline-block;"></div>
                                    $<?= htmlspecialchars(number_format($item->purchase_price, 2)) ?>
                                </td>
                                <td class="font-mono d-flex align-items-center">
                                    <!-- Gold coin placeholder -->
                                    <div style="width: 16px; height: 16px; background-color: #ffc107; border-radius: 50%; margin-right: 4px; display: inline-block;"></div>
                                    $<?= htmlspecialchars(number_format($item->selling_price, 2)) ?>
                                </td>
                                <td class="font-mono"><?= htmlspecialchars($item->created_at) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="form.php?id=<?= $item->id ?>" class="btn btn-sm btn-outline-primary me-1"><span class="nes-btn is-small">Edit</span></a>
                                        <a href="index.php?delete=<?= $item->id ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this item?')"><span class="nes-btn is-small">Delete</span></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php require '../partial/footer.php'; ?>

    <script>
        // JavaScript for dynamic filtering
        document.getElementById('searchBtn').addEventListener('click', function() {
            applyFilters();
        });

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        document.getElementById('applyFilters').addEventListener('click', function() {
            applyFilters();
        });

        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            const categoryId = document.getElementById('categoryFilter').value;

            let url = 'index.php?';
            if (searchTerm) {
                url += 'search=' + encodeURIComponent(searchTerm) + '&';
            }
            if (categoryId) {
                url += 'category=' + categoryId + '&';
            }

            // Remove trailing &
            url = url.endsWith('&') ? url.slice(0, -1) : url;

            window.location.href = url;
        }
    </script>
</body>
</html>