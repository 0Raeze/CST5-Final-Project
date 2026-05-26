<?php
require_once '../../controllers/supplier.php';
require_once '../../public/database.config.php';

// Initialize controller
$controller = new SupplierController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

// Determine if we are editing or adding
$isEdit = false;
$supplier = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $supplier = $controller->readById($id);
    if (!$supplier) {
        // Supplier not found
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
    $contact_person = trim($_POST["contact_person"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // Basic validation
    if (empty($name)) {
        $message = "Supplier name is required";
        $messageType = "danger";
    } else {
        if ($isEdit && $supplier) {
            // Update existing supplier
            if ($controller->update($supplier->id, $name, $contact_person, $phone, $email, $address)) {
                $message = "Supplier updated successfully";
                $messageType = "success";
            } else {
                $message = "Failed to update supplier";
                $messageType = "danger";
            }
        } else {
            // Create new supplier
            $newId = $controller->create($name, $contact_person, $phone, $email, $address);
            if ($newId !== false) {
                $message = "Supplier created successfully";
                $messageType = "success";
                // Clear form for next entry
                $name = "";
                $contact_person = "";
                $phone = "";
                $email = "";
                $address = "";
            } else {
                $message = "Failed to create supplier";
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
    <title><?= $isEdit ? 'Edit' : 'Add New' ?> Supplier - Stardew Valley Inventory</title>
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
            <h1 class="font-pixel"><?= $isEdit ? 'Edit' : 'Add New' ?> Supplier</h1>
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
                                <label for="name" class="form-label font-pixel">Supplier Name *</label>
                                <input type="text" class="form-control font-body" id="name" name="name" required
                                       value="<?= htmlspecialchars($isEdit ? $supplier->name : '') ?>">
                                <div class="form-text">Enter the supplier/company name</div>
                            </div>

                            <div class="mb-3">
                                <label for="contact_person" class="form-label font-pixel">Contact Person</label>
                                <input type="text" class="form-control font-body" id="contact_person" name="contact_person"
                                       value="<?= htmlspecialchars($isEdit ? $supplier->contact_person : '') ?>">
                                <div class="form-text">Primary contact at this supplier</div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label font-pixel">Phone Number</label>
                                <input type="tel" class="form-control font-body" id="phone" name="phone"
                                       value="<?= htmlspecialchars($isEdit ? $supplier->phone : '') ?>">
                                <div class="form-text">Contact phone number</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label font-pixel">Email Address</label>
                                <input type="email" class="form-control font-body" id="email" name="email"
                                       value="<?= htmlspecialchars($isEdit ? $supplier->email : '') ?>">
                                <div class="form-text">Contact email address</div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label font-pixel">Address</label>
                                <textarea class="form-control font-body" id="address" name="address" rows="3"
                                          ><?= htmlspecialchars($isEdit ? $supplier->address : '') ?></textarea>
                                <div class="form-text">Supplier address or location</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <span class="nes-btn is-small"><?= $isEdit ? 'Update Supplier' : 'Create Supplier' ?></span>
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