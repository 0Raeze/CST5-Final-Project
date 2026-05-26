<?php
session_start();

// Check if user is logged in (basic check)
if (!isset($_SESSION["user_id"])) {
    header("Location: /index.php");
    exit();
}
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

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="font-pixel">Stardew Valley Inventory Dashboard</h1>
            <div>
                <a href="../category/index.php" class="btn btn-outline-primary me-2"><span class="nes-btn is-small">Categories</span></a>
                <a href="../supplier/index.php" class="btn btn-outline-success me-2"><span class="nes-btn is-small">Suppliers</span></a>
                <a href="../item/index.php" class="btn btn-outline-info me-2"><span class="nes-btn is-small">Items</span></a>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
            <!-- Stats Cards -->
            <div class="col-md-3 mb-4">
                <div class="card bg-light border-0 shadow-sm h-100">
                    <div class="card-body text-center position-relative">
                        <div class="display-6 font-mono text-primary">
                            <?php
                            // Get counts for dashboard stats
                            require_once '../../public/database.config.php';
                            $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
                            $result = $conn->query("SELECT COUNT(*) as count FROM categories");
                            $row = $result->fetch_assoc();
                            echo htmlspecialchars($row['count']);
                            $result->free();
                            $conn->close();
                            ?>
                        </div>
                        <div class="font-body text-muted">Categories</div>
                        <!-- Placeholder for Pierre's sprite -->
                        <div class="position-absolute top-0 start-50 translate-middle-x mt-2" style="width: 24px; height: 24px; background-color: #28a745; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-light border-0 shadow-sm h-100">
                    <div class="card-body text-center position-relative">
                        <div class="display-6 font-mono text-success">
                            <?php
                            require_once '../../public/database.config.php';
                            $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
                            $result = $conn->query("SELECT COUNT(*) as count FROM suppliers");
                            $row = $result->fetch_assoc();
                            echo htmlspecialchars($row['count']);
                            $result->free();
                            $conn->close();
                            ?>
                        </div>
                        <div class="font-body text-muted">Suppliers</div>
                        <!-- Placeholder for Morris's sprite -->
                        <div class="position-absolute top-0 start-50 translate-middle-x mt-2" style="width: 24px; height: 24px; background-color: #007bff; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-light border-0 shadow-sm h-100">
                    <div class="card-body text-center position-relative">
                        <div class="display-6 font-mono text-warning">
                            <?php
                            require_once '../../public/database.config.php';
                            $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
                            $result = $conn->query("SELECT COUNT(*) as count FROM items");
                            $row = $result->fetch_assoc();
                            echo htmlspecialchars($row['count']);
                            $result->free();
                            $conn->close();
                            ?>
                        </div>
                        <div class="font-body text-muted">Inventory Items</div>
                        <!-- Placeholder for Marnie's sprite -->
                        <div class="position-absolute top-0 start-50 translate-middle-x mt-2" style="width: 24px; height: 24px; background-color: #ffc107; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-light border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 font-mono text-info">
                            <?php
                            require_once '../../public/database.config.php';
                            $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
                            $result = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE DATE(transaction_date) = CURDATE()");
                            $row = $result->fetch_assoc();
                            echo htmlspecialchars($row['count']);
                            $result->free();
                            $conn->close();
                            ?>
                        </div>
                        <div class="font-body text-muted">Today's Transactions</div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="font-pixel mb-4">Quick Actions</h2>
                <div class="row g-4">
                    <div class="col-md-3">
                        <a href="../category/form.php" class="card h-100 btn btn-outline-primary d-flex flex-column align-items-center text-center text-decoration-none">
                            <div class="card-body py-4">
                                <span class="nes-btn is-small mb-3 d-block">+</span>
                                <div class="font-pixel">Add Category</div>
                                <small class="font-body text-muted">Create new item categories</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../supplier/form.php" class="card h-100 btn btn-outline-success d-flex flex-column align-items-center text-center text-decoration-none">
                            <div class="card-body py-4">
                                <span class="nes-btn is-small mb-3 d-block">+</span>
                                <div class="font-pixel">Add Supplier</div>
                                <small class="font-body text-muted">Register new suppliers</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="card h-100 btn btn-outline-warning d-flex flex-column align-items-center text-center text-decoration-none">
                            <div class="card-body py-4">
                                <span class="nes-btn is-small mb-3 d-block">+</span>
                                <div class="font-pixel">Add Item</div>
                                <small class="font-body text-muted">Add inventory items</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="card h-100 btn btn-outline-info d-flex flex-column align-items-center text-center text-decoration-none">
                            <div class="card-body py-4">
                                <span class="nes-btn is-small mb-3 d-block">+</span>
                                <div class="font-pixel">Record Transaction</div>
                                <small class="font-body text-muted">Log stock movements</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="font-pixel mb-4">Recent Activity</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="font-pixel">Time</th>
                                <th class="font-pixel">Action</th>
                                <th class="font-pixel">Details</th>
                                <th class="font-pixel">User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-mono">Just now</td>
                                <td><span class="nes-btn is-small bg-success">SEED</span> Suppliers</td>
                                <td class="font-body">Pierre's General Store, JojaCorp, Marnie's Ranch</td>
                                <td class="font-body">System</td>
                            </tr>
                            <tr>
                                <td class="font-mono">Just now</td>
                                <td><span class="nes-btn is-small bg-primary">SEED</span> Categories</td>
                                <td class="font-body">Seeds & Starts, Artisan Goods, Livestock & Feed, Farm Tools</td>
                                <td class="font-body">System</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>

    <?php require '../partial/footer.php'; ?>
</body>
</html>