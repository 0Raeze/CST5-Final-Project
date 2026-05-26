<?php
session_start();

// Check if user is logged in (basic check)
if (!isset($_SESSION["user_id"])) {
    header("Location: /index.php");
    exit();
}

// Get dashboard stats
require_once '../../public/database.config.php';
$conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get counts for dashboard stats
$categoryResult = $conn->query("SELECT COUNT(*) as count FROM categories");
$categoryRow = $categoryResult->fetch_assoc();
$categoryCount = $categoryRow['count'] ?? 0;
$categoryResult->free();

$supplierResult = $conn->query("SELECT COUNT(*) as count FROM suppliers");
$supplierRow = $supplierResult->fetch_assoc();
$supplierCount = $supplierRow['count'] ?? 0;
$supplierResult->free();

$itemResult = $conn->query("SELECT COUNT(*) as count FROM items");
$itemRow = $itemResult->fetch_assoc();
$itemCount = $itemRow['count'] ?? 0;
$itemResult->free();

$transactionResult = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE DATE(transaction_date) = CURDATE()");
$transactionRow = $transactionResult->fetch_assoc();
$todayTransactions = $transactionRow['count'] ?? 0;
$transactionResult->free();

// Get total asset value and potential revenue
$assetResult = $conn->query("SELECT SUM(stock_quantity * purchase_price) as total_asset_value FROM items");
$assetRow = $assetResult->fetch_assoc();
$totalAssetValue = $assetRow['total_asset_value'] ?? 0;
$assetResult->free();

$revenueResult = $conn->query("SELECT SUM(stock_quantity * selling_price) as total_potential_revenue FROM items");
$revenueRow = $revenueResult->fetch_assoc();
$totalPotentialRevenue = $revenueRow['total_potential_revenue'] ?? 0;
$revenueResult->free();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stardew Valley Inventory</title>
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

    <!-- Changed to container-fluid for full-width layout -->
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="font-pixel">Stardew Valley Farm Overview</h1>
        </div>

        <!-- Farm Overview Grid -->
        <div class="row g-3">
            <!-- Asset Overview Card -->
            <div class="col-12 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div style="border: 4px solid black; padding: 4px; background-color: #4caf50; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; image-rendering: pixelated;">🌱</div>
                            <div>
                                <h5 class="font-pixel mb-1">Total Farm Value</h5>
                                <p class="text-muted mb-0">Based on purchase costs</p>
                            </div>
                        </div>
                        <div class="h4 font-mono mb-2">$<?= number_format($totalAssetValue, 2) ?></div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                75% of potential
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Potential Card -->
            <div class="col-12 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div style="border: 4px solid black; padding: 4px; background-color: #2196f3; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; image-rendering: pixelated;">💰</div>
                            <div>
                                <h5 class="font-pixel mb-1">Revenue Potential</h5>
                                <p class="text-muted mb-0">Based on selling prices</p>
                            </div>
                        </div>
                        <div class="h4 font-mono mb-2">$<?= number_format($totalPotentialRevenue, 2) ?></div>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                60% margin
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Diversity Card -->
            <div class="col-12 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div style="border: 4px solid black; padding: 4px; background-color: #ff9800; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; image-rendering: pixelated;">📦</div>
                            <div>
                                <h5 class="font-pixel mb-1">Inventory Diversity</h5>
                                <p class="text-muted mb-0">Unique items in stock</p>
                            </div>
                        </div>
                        <div class="h4 font-mono mb-2"><?= number_format($itemCount) ?></div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="font-body"><?= $categoryCount ?> Categories</span>
                            <span class="font-body"><?= $supplierCount ?> Suppliers</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Today Card -->
            <div class="col-12 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div style="border: 4px solid black; padding: 4px; background-color: #f44336; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; image-rendering: pixelated;">⚡</div>
                            <div>
                                <h5 class="font-pixel mb-1">Today's Activity</h5>
                                <p class="text-muted mb-0">Transactions processed</p>
                            </div>
                        </div>
                        <div class="h4 font-mono mb-2"><?= number_format($todayTransactions) ?></div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                40% of daily goal
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Farm Activity -->
        <div class="row mb-4">
            <div class="col-12 col-md-8">
                <h2 class="font-pixel mb-3">Recent Farm Activity</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="font-pixel">Time</th>
                                <th class="font-pixel">Action</th>
                                <th class="font-pixel">Details</th>
                                <th class="font-pixel">Actor</tr>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-mono">Just now</td>
                                <td><span class="nes-container is-small bg-success">🌱 SEED</span> Suppliers</td>
                                <td class="font-body">Pierre's General Store, JojaCorp, Marnie's Ranch</td>
                                <td class="font-body">System</td>
                            </tr>
                            <tr>
                                <td class="font-mono">Just now</td>
                                <td><span class="nes-container is-small bg-primary">🏷️ SEED</span> Categories</td>
                                <td class="font-body">Seeds & Starts, Artisan Goods, Livestock & Feed, Farm Tools</td>
                                <td class="font-body">System</td>
                            </tr>
                            <tr>
                                <td class="font-mono">5 min ago</td>
                                <td><span class="nes-container is-small bg-warning">📥 ADDED</span> 50x Parsnip Seeds</td>
                                <td class="font-body">Inventory updated</td>
                                <td class="font-body">You</td>
                            </tr>
                            <tr>
                                <td class="font-mono">12 min ago</td>
                                <td><span class="nes-container is-small bg-info">💰 SALE</span> 5x Goat Cheese</td>
                                <td class="font-body">Revenue: $12.50</td>
                                <td class="font-body">System</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Farm Actions -->
            <div class="col-12 col-md-4">
                <h2 class="font-pixel mb-3">Quick Farm Actions</h2>
                <div class="d-flex flex-column gap-3">
                    <a href="../category/form.php" class="card h-100 btn btn-outline-primary d-flex flex-column align-items-center text-center text-decoration-none border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-center mb-2">
                                <span class="nes-container is-small bg-success">+</span>
                            </div>
                            <div class="font-pixel">Add Category</div>
                            <small class="font-body text-muted">Create new item categories</small>
                        </div>
                    </a>
                    <a href="../supplier/form.php" class="card h-100 btn btn-outline-success d-flex flex-column align-items-center text-center text-decoration-none border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-center mb-2">
                                <span class="nes-container is-small bg-success">+</span>
                            </div>
                            <div class="font-pixel">Add Supplier</div>
                            <small class="font-body text-muted">Register new suppliers</small>
                        </div>
                    </a>
                    <a href="../item/form.php" class="card h-100 btn btn-outline-warning d-flex flex-column align-items-center text-center text-decoration-none border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-center mb-2">
                                <span class="nes-container is-small bg-success">+</span>
                            </div>
                            <div class="font-pixel">Add Item</div>
                            <small class="font-body text-muted">Track new inventory</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php require '../partial/footer.php'; ?>
</body>
</html>