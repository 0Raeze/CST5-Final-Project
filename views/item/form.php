<?php
require_once '../../controllers/item.php';
require_once '../../controllers/category.php';
require_once '../../controllers/supplier.php';
require_once '../../public/database.config.php';

// Initialize controllers
$itemController = new ItemController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$categoryController = new CategoryController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$supplierController = new SupplierController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

// Determine if we are editing or adding
$isEdit = false;
$item = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $item = $itemController->readById($id);
    if (!$item) {
        // Item not found
        header('Location: index.php');
        exit();
    }
    $isEdit = true;
}

// Get categories and suppliers for dropdowns
$categories = $categoryController->readAll();
$suppliers = $supplierController->readAll();

// Handle form submission
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save"])) {
    $sku = trim($_POST["sku"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category_id = isset($_POST["category_id"]) && is_numeric($_POST["category_id"]) ? intval($_POST["category_id"]) : 0;
    $supplier_id = isset($_POST["supplier_id"]) && is_numeric($_POST["supplier_id"]) ? intval($_POST["supplier_id"]) : 0;
    $stock_quantity = isset($_POST["stock_quantity"]) ? floatval($_POST["stock_quantity"]) : 0;
    $purchase_price = isset($_POST["purchase_price"]) ? floatval($_POST["purchase_price"]) : 0;
    $selling_price = isset($_POST["selling_price"]) ? floatval($_POST["selling_price"]) : 0;

    // Basic validation
    $errors = [];

    if (empty($sku)) {
        $errors[] = "SKU is required";
    } elseif (strlen($sku) > 50) {
        $errors[] = "SKU must be less than 50 characters";
    }

    if (empty($name)) {
        $errors[] = "Item name is required";
    }

    if ($category_id <= 0) {
        $errors[] = "Please select a category";
    }

    if ($supplier_id <= 0) {
        $errors[] = "Please select a supplier";
    }

    if ($stock_quantity < 0) {
        $errors[] = "Stock quantity cannot be negative";
    }

    if ($purchase_price < 0) {
        $errors[] = "Purchase price cannot be negative";
    }

    if ($selling_price < 0) {
        $errors[] = "Selling price cannot be negative";
    }

    if (empty($errors)) {
        if ($isEdit && $item) {
            // Update existing item
            if ($itemController->update($item->id, $sku, $name, $description, $category_id, $supplier_id,
                                       $stock_quantity, $purchase_price, $selling_price)) {
                $message = "Item updated successfully";
                $messageType = "success";
            } else {
                $message = "Failed to update item (SKU may already exist)";
                $messageType = "danger";
            }
        } else {
            // Create new item
            $newId = $itemController->create($sku, $name, $description, $category_id, $supplier_id,
                                            $stock_quantity, $purchase_price, $selling_price);
            if ($newId !== false) {
                $message = "Item created successfully";
                $messageType = "success";
                // Clear form for next entry
                $sku = "";
                $name = "";
                $description = "";
                $category_id = 0;
                $supplier_id = 0;
                $stock_quantity = 0;
                $purchase_price = 0;
                $selling_price = 0;
            } else {
                $message = "Failed to create item (SKU may already exist or validation failed)";
                $messageType = "danger";
            }
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add New' ?> Item - Stardew Valley Inventory</title>
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
            <h1 class="font-pixel"><?= $isEdit ? 'Edit' : 'Add New' ?> Item</h1>
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sku" class="form-label font-pixel">SKU/Item Code *</label>
                                    <input type="text" class="form-control font-mono" id="sku" name="sku" required
                                           maxlength="50" value="<?= htmlspecialchars($isEdit ? $item->sku : '') ?>">
                                    <div class="form-text">Unique item identifier (e.g., PARSNIP, JOJACOLA)</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label font-pixel">Item Name *</label>
                                    <input type="text" class="form-control font-body" id="name" name="name" required
                                           value="<?= htmlspecialchars($isEdit ? $item->name : '') ?>">
                                    <div class="form-text">Display name of the item</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label font-pixel">Description</label>
                                <textarea class="form-control font-body" id="description" name="description"
                                          rows="3"><?= htmlspecialchars($isEdit ? $item->description : '') ?></textarea>
                                <div class="form-text">Detailed description of the item</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label font-pixel">Category *</label>
                                    <select class="form-select font-body" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category->id ?>"
                                                    <?= $isEdit && $item->category_id == $category->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label font-pixel">Supplier *</label>
                                    <select class="form-select font-body" id="supplier_id" name="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier->id ?>"
                                                    <?= $isEdit && $item->supplier_id == $supplier->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($supplier->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="stock_quantity" class="form-label font-pixel">Stock Quantity *</label>
                                    <input type="number" class="form-control font-mono" id="stock_quantity" name="stock_quantity"
                                           min="0" step="1" value="<?= htmlspecialchars($isEdit ? $item->stock_quantity : '') ?>">
                                    <div class="form-text">Current quantity in stock (cannot be negative)</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="purchase_price" class="form-label font-pixel">Purchase Price ($)</label>
                                    <input type="number" class="form-control font-mono" id="purchase_price" name="purchase_price"
                                           min="0" step="0.01" value="<?= htmlspecialchars($isEdit ? $item->purchase_price : '') ?>">
                                    <div class="form-text">Cost to acquire one unit (cannot be negative)</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="selling_price" class="form-label font-pixel">Selling Price ($)</label>
                                    <input type="number" class="form-control font-mono" id="selling_price" name="selling_price"
                                           min="0" step="0.01" value="<?= htmlspecialchars($isEdit ? $item->selling_price : '') ?>">
                                    <div class="form-text">Price to sell one unit (cannot be negative)</div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <span class="nes-btn is-small"><?= $isEdit ? 'Update Item' : 'Create Item' ?></span>
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