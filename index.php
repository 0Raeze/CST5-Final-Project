<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stardew Valley Farm Co-op Inventory System</title>
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
    <!-- Community Board Style Hero Section -->
    <section class="min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center">
                            <!-- Community Board Banner -->
                            <div class="mb-4">
                                <div class="d-inline-block bg-warning text-dark px-4 py-2" style="border: 3px solid #333; border-radius: 8px;">
                                    <h1 class="font-pixel mb-2" style="margin: 0;">Stardew Valley</h1>
                                    <p class="font-pixel mb-0" style="margin: 0; font-size: 1.2rem;">Farm Co-op Inventory System</p>
                                </div>
                            </div>

                            <p class="lead font-body mb-4">
                                Manage your farm's inventory with ease! Track seeds, artisan goods, livestock supplies,
                                and farm tools. Perfect for Stardew Valley farmers who want to keep their co-op
                                operations running smoothly.
                            </p>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="/login.php" class="btn btn-primary px-4 py-3">
                                    <span class="nes-btn is-primary">Enter Dashboard / Login</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features py-5">
        <div class="container">
            <h2 class="font-pixel text-center mb-5">Why Choose Our Inventory System?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon mb-3">
                                <span class="nes-btn is-small bg-success">📊</span>
                            </div>
                            <h3 class="font-pixel h5">Comprehensive Tracking</h3>
                            <p class="font-body text-muted">
                                Track everything from seeds to finished products, supplies to livestock feed.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon mb-3">
                                <span class="nes-btn is-small bg-info">🔍</span>
                            </div>
                            <h3 class="font-pixel h5">Smart Search & Filter</h3>
                            <p class="font-body text-muted">
                                Find items instantly with dynamic search and category filtering.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon mb-3">
                                <span class="nes-btn is-small bg-warning">⚠️</span>
                            </div>
                            <h3 class="font-pixel h5">Low Stock Alerts</h3>
                            <p class="font-body text-muted">
                                Get notified when items need restocking with our reporting system.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta bg-primary text-white py-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="font-pixel">Ready to organize your farm?</h2>
                    <p class="font-body">
                        Join hundreds of Stardew Valley farmers who trust our inventory system
                        to keep their co-op operations efficient and profitable.
                    </p>
                    <a href="/login.php" class="btn btn-light btn-lg mt-4">
                        <span class="nes-btn is-small">Start Managing Inventory</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php require 'views/partial/footer.php'; ?>
</body>
</html>