<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 for responsive layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- NES.css for pixel-art accents -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/nes.css/2.3.0/css/nes.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Plus+Jakarta+Sans:wght@400;500;600&family=Courier+Prime&display=swap" rel="stylesheet">
    <!-- Main stylesheet -->
    <link rel="stylesheet" href="/public/styles.css">
    <title>Stardew Valley Inventory System</title>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/views/dashboard/index.php">
                <img src="/public/sprites/icons/stardew_logo.png" alt="Stardew Valley Logo" style="image-rendering: pixelated; height: 32px; margin-right: 8px;">
                <span class="font-pixel">Stardew Valley Inventory</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active font-body" aria-current="page" href="/views/dashboard/index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/views/category/index.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/views/supplier/index.php">Suppliers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/views/item/index.php">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/views/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/views/reports.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/views/settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-body" href="/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">