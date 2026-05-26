<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Stardew Valley Inventory</title>
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
    <!-- Error Section -->
    <section class="min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card text-center shadow-lg border-0">
                        <div class="card-body py-5">
                            <div class="display-1 font-pixel text-muted mb-4">404</div>
                            <h1 class="font-pixel mb-4">Page Not Found</h1>
                            <p class="font-body lead mb-4">
                                Looks like you took a wrong turn on the farm!<br>
                                The page you're looking for doesn't exist or has been moved.
                            </p>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="/index.php" class="btn btn-outline-primary px-4 py-2">
                                    <span class="nes-btn is-small">Return to Home</span>
                                </a>
                                <a href="/login.php" class="btn btn-outline-secondary px-4 py-2">
                                    <span class="nes-btn is-small">Go to Login</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require 'views/partial/footer.php'; ?>
</body>
</html>