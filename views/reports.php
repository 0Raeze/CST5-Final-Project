<?php require 'partial/header.php'; ?>

<div class="container my-5">
    <h1 class="font-pixel text-center mb-4">Reports & Analytics</h1>
    <?php
    require_once 'public/database.config.php';
    $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Total Asset Value: SUM(stock_quantity * purchase_price)
    $assetResult = $conn->query("SELECT SUM(stock_quantity * purchase_price) AS total_asset_value FROM items");
    $assetRow = $assetResult->fetch_assoc();
    $totalAssetValue = $assetRow['total_asset_value'] ?? 0;
    // Potential Revenue: SUM(stock_quantity * selling_price)
    $revenueResult = $conn->query("SELECT SUM(stock_quantity * selling_price) AS total_potential_revenue FROM items");
    $revenueRow = $revenueResult->fetch_assoc();
    $totalPotentialRevenue = $revenueRow['total_potential_revenue'] ?? 0;
    $conn->close();
    ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="font-pixel">Total Inventory Asset Value</h5>
                    <p class="font-body display-6">$<?= number_format($totalAssetValue, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="font-pixel">Total Potential Revenue</h5>
                    <p class="font-body display-6">$<?= number_format($totalPotentialRevenue, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="font-pixel mb-4">Low Stock Alert (Stock < 10)</h2>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="font-pixel">Item</th>
                    <th class="font-pixel">Stock</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-body">Placeholder item</td>
                    <td class="font-mono">5</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require 'partial/footer.php'; ?>