<?php
session_start();
// Include the database connection file (your Connect class)
include 'connect.php'; 

if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Instantiate the Connect class and get the PDO connection
$db = new Connect();
$conn = $db->connectToPDO();

$error = "";

// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $product_type_id = filter_var($_POST['product_type_id'] ?? '', FILTER_VALIDATE_INT);
    $product_description = trim($_POST['product_description'] ?? '');
    $product_price = filter_var($_POST['product_price'] ?? '', FILTER_VALIDATE_FLOAT);
    $producer_id = filter_var($_POST['producer_id'] ?? '', FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT);
    $product_video_url = trim($_POST['product_video_url'] ?? '');

    $product_img = null; // Default to null for new product

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

    // File Upload Handling
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
            // Rename file to avoid collision (e.g., add uniqid)
            $file_name = uniqid() . '_' . $file_name;
            $target_file = $target_dir . $file_name;
        }

        if (empty($error)) {
            if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
                $product_img = $file_name;
            } else {
                $error = "Error uploading image.";
            }
        }
    } else if (empty($_FILES['product_img']['name'])) {
        // You can add a check here if image is mandatory for new products
        // For example: $error = "Product image is required.";
    }


    // If no validation errors, proceed with insertion
    if (empty($error)) {
        try {
            $sql = "INSERT INTO product (product_name, product_type_id, product_description, product_price, product_img, producer_id, quantity, product_video_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $product_name,
                $product_type_id,
                $product_description,
                $product_price,
                $product_img, // Can be null if no image uploaded
                $producer_id,
                $quantity,
                $product_video_url
            ]);

            if ($stmt->rowCount() > 0) {
                // Thêm thông báo thành công vào session
                $_SESSION['success_message'] = "Product '" . htmlspecialchars($product_name) . "' added successfully!";
                header("Location: manage_products.php"); // Redirect to product management page
                exit;
            } else {
                $error = "Failed to add new product.";
            }
        } catch (PDOException $e) {
            $error = "Database error during insertion: " . $e->getMessage();
        }
    }
}

// Fetch product types and producers for the dropdowns
$product_types = [];
try {
    $type_stmt = $conn->query("SELECT product_type_id, product_type_name FROM product_type");
    $product_types = $type_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error .= "<br>Could not fetch product types: " . $e->getMessage();
}

$producers = [];
try {
    $prod_stmt = $conn->query("SELECT producer_id, producer_name FROM producer");
    $producers = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error .= "<br>Could not fetch producers: " . $e->getMessage();
}

// Close the connection (or it will be closed automatically at script end)
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Product - TD Motor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
                <hr class="text-white">
                <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                <h2 style="color: red; font-weight: bold; text-align:center">Add New Product</h2> <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" id="product_name" class="form-control" required value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_type_id" class="form-label">Product Type <span class="text-danger">*</span></label>
                                <select name="product_type_id" id="product_type_id" class="form-control" required>
                                    <option value="">Select Product Type</option>
                                    <?php foreach ($product_types as $type): ?>
                                        <?php $selected = (isset($_POST['product_type_id']) && $_POST['product_type_id'] == $type['product_type_id']) ? 'selected' : ''; ?>
                                        <option value='<?= htmlspecialchars($type['product_type_id']) ?>' <?= $selected ?>><?= htmlspecialchars($type['product_type_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="product_description" class="form-label">Product Description <span class="text-danger">*</span></label>
                        <textarea name="product_description" id="product_description" class="form-control" rows="5" required><?= htmlspecialchars($_POST['product_description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_price" class="form-label">Product Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="product_price" id="product_price" class="form-control" required value="<?= htmlspecialchars($_POST['product_price'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="producer_id" class="form-label">Producer <span class="text-danger">*</span></label>
                                <select name="producer_id" id="producer_id" class="form-control" required>
                                    <option value="">Select Producer</option>
                                    <?php foreach ($producers as $row): ?>
                                        <?php $selected = (isset($_POST['producer_id']) && $_POST['producer_id'] == $row['producer_id']) ? 'selected' : ''; ?>
                                        <option value='<?= htmlspecialchars($row['producer_id']) ?>' <?= $selected ?>><?= htmlspecialchars($row['producer_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="product_img" class="form-label">Product Image</label>
                        <input type="file" name="product_img" id="product_img" class="form-control" accept="image/*">
                        <small class="text-muted">Max 5MB (JPG, JPEG, PNG, GIF).</small>
                        <div id="image-preview" class="mt-2" style="display: none;">
                            <img id="preview-image" src="#" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; height: auto;">
                            <p id="image-file-name" class="text-muted mt-1"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="product_video_url" class="form-label">Product Video URL</label>
                        <input type="url" name="product_video_url" id="product_video_url" class="form-control" value="<?= htmlspecialchars($_POST['product_video_url'] ?? '') ?>">
                        <small class="text-muted">Optional: URL to a product video (e.g., YouTube).</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for image preview
        document.getElementById('product_img').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('image-preview');
            const previewImage = document.getElementById('preview-image');
            const imageFileName = document.getElementById('image-file-name');
            
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imageFileName.textContent = file.name;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                imageFileName.textContent = '';
                previewContainer.style.display = 'none';
            }
        });
    </script>
</body>

</html>