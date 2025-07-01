<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$db_link = $connect->connectToPDO(); // Use PDO consistently

$error = "";
$product = null; // Initialize product variable

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_products.php');
    exit;
}
$product_id = (int)$_GET['id'];

// Fetch product details
try {
    $stmt = $db_link->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        // Product not found, redirect with an error message
        $_SESSION['error_message'] = "Product not found.";
        header('Location: manage_products.php');
        exit;
    }
} catch (PDOException $e) {
    $error = "Database error fetching product: " . $e->getMessage();
}

// Handle POST request for updating the product (logic remains the same as previously improved)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $product_type_id = filter_var($_POST['product_type_id'] ?? '', FILTER_VALIDATE_INT);
    $product_description = trim($_POST['product_description'] ?? '');
    $product_price = filter_var($_POST['product_price'] ?? '', FILTER_VALIDATE_FLOAT);
    $producer_id = filter_var($_POST['producer_id'] ?? '', FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT);
    $product_video_url = trim($_POST['product_video_url'] ?? '');

    $product_img = $product['product_img']; // Keep existing image if no new one uploaded

    // Validate inputs
    if (empty($product_name)) {
        $error = "Product name cannot be empty.";
    } elseif ($product_type_id === false || $product_type_id <= 0) {
        $error = "Invalid product type selected.";
    } elseif (empty($product_description)) {
        $error = "Product description cannot be empty.";
    } elseif ($product_price === false || $product_price < 0) {
        $error = "Invalid product price.";
    } elseif ($producer_id === false || $producer_id <= 0) {
        $error = "Invalid producer selected.";
    } elseif ($quantity === false || $quantity < 0) {
        $error = "Invalid quantity.";
    }

    // File Upload Handling with enhanced security (logic remains the same)
    if (empty($error) && !empty($_FILES['product_img']['name'])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["product_img"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["product_img"]["tmp_name"]);
        if ($check === false) {
            $error = "Uploaded file is not an image.";
        }
        if ($_FILES["product_img"]["size"] > 5 * 1024 * 1024) { // 5MB limit
            $error = "Sorry, your file is too large (max 5MB).";
        }
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        if (file_exists($target_file)) {
            // Rename file to avoid collision (e.g., add timestamp)
            $file_name = uniqid() . '_' . $file_name;
            $target_file = $target_dir . $file_name;
        }

        if (empty($error)) {
            if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
                // Delete old image if it exists and is different from the new one
                if (!empty($product['product_img']) && $product['product_img'] !== $file_name) {
                    $old_img_path = 'uploads/' . $product['product_img'];
                    if (file_exists($old_img_path)) {
                        unlink($old_img_path);
                    }
                }
                $product_img = $file_name;
            } else {
                $error = "Error uploading image.";
            }
        }
    }

    // If no validation errors, proceed with update
    if (empty($error)) {
        try {
            $sql = "UPDATE product SET product_name=?, product_type_id=?, product_description=?, product_price=?, product_img=?, producer_id=?, quantity=?, product_video_url=? WHERE product_id=?";
            $stmt = $db_link->prepare($sql);
            $stmt->execute([
                $product_name,
                $product_type_id,
                $product_description,
                $product_price,
                $product_img,
                $producer_id,
                $quantity,
                $product_video_url,
                $product_id
            ]);

            if ($stmt->rowCount() > 0) {
                // Set success message for manage_products.php
                $_SESSION['success_message'] = "Product '" . htmlspecialchars($product_name) . "' updated successfully!";
                header("Location: manage_products.php"); // Redirect after successful update
                exit;
            } else {
                $error = "No changes were made or update failed.";
            }
        } catch (PDOException $e) {
            $error = "Database error during update: " . $e->getMessage();
        }
    }
    // Re-fetch product data after POST if there was an error, to show current form values
    if (!empty($error)) {
        try {
            $stmt = $db_link->prepare("SELECT * FROM product WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error .= "<br>Error re-fetching product data after failed update: " . $e->getMessage();
        }
    }
}

// Fetch product types and producers for the dropdowns (using PDO)
$product_types = [];
try {
    $type_stmt = $db_link->query("SELECT product_type_id, product_type_name FROM product_type");
    $product_types = $type_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error .= "<br>Could not fetch product types: " . $e->getMessage();
}

$producers = [];
try {
    $prod_stmt = $db_link->query("SELECT producer_id, producer_name FROM producer");
    $producers = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error .= "<br>Could not fetch producers: " . $e->getMessage();
}

// Close the connection
$db_link = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - TD Motor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* CSS được điều chỉnh để đồng bộ với manage_products.php (giao diện gốc) */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            flex-shrink: 0;
        }
        .container-fluid {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .row {
            flex-grow: 1;
            display: flex; 
        }
        .sidebar {
            background-color: #343a40; /* Màu đen tối cho sidebar */
        }
        .sidebar a {
            color: white; /* Màu chữ trắng cho sidebar links */
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        /* Ensure main content takes remaining height and can scroll */
        main {
            flex-grow: 1;
            overflow-y: auto; 
        }
        .logo-text {
            color: whitesmoke; 
            font-weight: bold; 
            font-size: larger;
        }
        /* Form interface improvements */
        .form-label .text-danger {
            margin-left: 5px; /* Adjust spacing for asterisk */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <div class="d-flex align-items-center logo">
            <a href="admin.php" class="d-flex align-items-center text-decoration-none">
                <img src="image/TDicon1.png" alt="TD Motor Logo" style="height: 70px;">
                <span class="logo-text ms-3 d-none d-md-inline">TD Motor Admin Page</span>
            </a>
        </div>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3">
                <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Admin'); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row" style="height: calc(100vh - 56px);">
            <aside class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3 text-white fw-bold border-bottom"><a href="admin.php">Dashboard</a></div>
                <a href="manage_products.php"><i class="bi bi-box"></i> Product Management</a>
                <a href="manage_product_type.php"><i class="bi bi-tags"></i> Product Type Management</a>
                <a href="manage_producer.php"><i class="bi bi-building"></i> Producer Management</a>
                <a href="manage_account.php"><i class="bi bi-person"></i> Account Management</a>
                <a href="manage_order.php"><i class="bi bi-cart"></i> Order Management</a>
                <a href="statistic.php"><i class="bi bi-bar-chart"></i> Statistics</a>
                <a href="manage_contact.php"><i class="bi bi-headset"></i> Contact Management</a>
                <a href="manage_feedback.php"><i class="bi bi-chat-dots"></i> Feedback Management</a>
                <a href="manage_preorder.php"><i class="bi bi-calendar-check"></i> Pre-order Management</a>
                <hr class="text-white">
                <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                <h2 style="color: red; font-weight: bold;text-align:center">Edit Product</h2> <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" id="product_name" class="form-control" required value="<?= htmlspecialchars($product['product_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_type_id" class="form-label">Product Type <span class="text-danger">*</span></label>
                                <select name="product_type_id" id="product_type_id" class="form-control" required>
                                    <?php foreach ($product_types as $type): ?>
                                        <?php $selected = (($product['product_type_id'] ?? '') == $type['product_type_id']) ? 'selected' : ''; ?>
                                        <option value='<?= htmlspecialchars($type['product_type_id']) ?>' <?= $selected ?>><?= htmlspecialchars($type['product_type_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="product_description" class="form-label">Product Description <span class="text-danger">*</span></label>
                        <textarea name="product_description" id="product_description" class="form-control" rows="5" required><?= htmlspecialchars($product['product_description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_price" class="form-label">Product Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="product_price" id="product_price" class="form-control" required value="<?= htmlspecialchars($product['product_price'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="producer_id" class="form-label">Producer <span class="text-danger">*</span></label>
                                <select name="producer_id" id="producer_id" class="form-control" required>
                                    <?php foreach ($producers as $row): ?>
                                        <?php $selected = (($product['producer_id'] ?? '') == $row['producer_id']) ? 'selected' : ''; ?>
                                        <option value='<?= htmlspecialchars($row['producer_id']) ?>' <?= $selected ?>><?= htmlspecialchars($row['producer_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required value="<?= htmlspecialchars($product['quantity'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="product_img" class="form-label">Product Image</label>
                        <input type="file" name="product_img" id="product_img" class="form-control" accept="image/*">
                        <?php 
                            $current_img_path = 'uploads/' . ($product['product_img'] ?? '');
                            $img_display_url = (file_exists($current_img_path) && !empty($product['product_img'])) ? $current_img_path : 'image/default-product.png';
                        ?>
                        <div class="mt-2">
                            <img id="current-image-preview" src="<?= htmlspecialchars($img_display_url) ?>" alt="Product Image" class="img-thumbnail" style="max-width: 120px; height: auto;">
                            <?php if (!empty($product['product_img'])): ?>
                                <small id="current-image-text" class="text-muted ms-2">Current image: <?= htmlspecialchars($product['product_img']) ?>. Upload a new file to replace.</small>
                            <?php else: ?>
                                <small id="current-image-text" class="text-muted ms-2">No image uploaded yet. Upload a new file.</small>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Max 5MB (JPG, JPEG, PNG, GIF).</small>
                    </div>
                    <div class="mb-3">
                        <label for="product_video_url" class="form-label">Product Video URL</label>
                        <input type="url" name="product_video_url" id="product_video_url" class="form-control" value="<?= htmlspecialchars($product['product_video_url'] ?? '') ?>">
                        <?php if (!empty($product['product_video_url'])): ?>
                            <small class="text-muted mt-1 d-block">Current URL: <a href="<?= htmlspecialchars($product['product_video_url']) ?>" target="_blank"><?= htmlspecialchars($product['product_video_url']) ?></a></small>
                        <?php else: ?>
                            <small class="text-muted mt-1 d-block">Optional: URL to a product video (e.g., YouTube).</small>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                </form>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for new image preview when a file is selected
        document.getElementById('product_img').addEventListener('change', function(event) {
            const currentImagePreview = document.getElementById('current-image-preview');
            const currentImageText = document.getElementById('current-image-text');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentImagePreview.src = e.target.result; // Update src to new image
                    currentImageText.textContent = 'New image selected: ' + file.name;
                }
                reader.readAsDataURL(file);
            } else {
                // If no new file selected, revert to original image or default
                <?php
                // Reconstruct the logic for the original image or default
                $original_img_path = 'uploads/' . ($product['product_img'] ?? '');
                $original_img_url = (file_exists($original_img_path) && !empty($product['product_img'])) ? $original_img_path : 'image/default-product.png';
                ?>
                currentImagePreview.src = '<?= htmlspecialchars($original_img_url) ?>';
                currentImageText.textContent = <?php
                    if (!empty($product['product_img'])) {
                        echo "'" . htmlspecialchars($product['product_img']) . ". Upload a new file to replace.'";
                    } else {
                        echo "'No image uploaded yet. Upload a new file.'";
                    }
                ?>;
            }
        });
    </script>
</body>
</html>