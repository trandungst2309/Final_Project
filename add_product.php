<?php
include_once 'connect.php';
$conn = new Connect();
?>

<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - TD Motor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .sidebar {
        height: auto;
        background-color: #343a40;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 12px 20px;
    }

    .sidebar a:hover {
        background-color: #495057;
    }

    .logo-img {
        height: 120px;
        display: block;
        margin: 20px auto;
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <<div class="d-flex align-items-center logo">
            <a href="admin.php" class="d-flex align-items-center text-decoration-none">
                <img src="image/TDicon1.png" alt="TD Motor Logo" style="height: 70px;">
                <span class="logo-text ms-3 d-none d-md-inline" style="color: whitesmoke; font-weight:bold; font-size:larger">TD Motor Admin Page</span>
            </a>            
        </div>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3">
                <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Admin'); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <!-- Layout -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3 text-white fw-bold border-bottom"><a href="admin.php">Dashboard</a></div>
                <a href="manage_products.php"><i class="bi bi-box"></i> Product Management</a>
                <a href="manage_account.php"><i class="bi bi-person"></i> Account Management</a>
                <a href="manage_order.php"><i class="bi bi-cart"></i> Order Management</a>
                <a href="statistic.php"><i class="bi bi-bar-chart"></i> Statistics</a>
                <a href="manage_contact.php"><i class="bi bi-headset"></i> Contact Management</a>
                <a href="manage_feedback.php"><i class="bi bi-chat-dots"></i> Feedback Management</a>
                <hr class="text-white">
                <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <!-- Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <div class="container mt-5">
        <h2 class="text-center"><?php echo isset($_GET['edit']) ? 'Edit Product' : 'Add Product'; ?></h2>
        <?php
        // Check if we are editing a product
        $isEdit = isset($_GET['edit']);
        $product = null;

        if ($isEdit) {
            $product_id = $_GET['edit'];
            // Fetch product details from the database
            $db_link = $conn->connectToPDO();
            $sql = "SELECT * FROM product WHERE product_id = ?";
            $stmt = $db_link->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo "<div class='alert alert-danger'>Product not found!</div>";
                exit();
            }
        }
        ?>
        <form action="#" method="post" enctype="multipart/form-data" class="mt-4">
            <input type="hidden" name="product_id" value="<?php echo $isEdit ? $product['product_id'] : ''; ?>">

            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo $isEdit ? $product['product_name'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="product_type_id">Product Type Name:</label>
                <?php
                $db_link = $conn->connectToMySQL(); // Use the MySQL connection
                $sql = "SELECT product_type_id, product_type_name FROM product_type ORDER BY product_type_id";
                $result = mysqli_query($db_link, $sql); // Use the MySQLi connection here
                echo '<select id="product_type_id" name="product_type_id" class="form-control" required>';
                // while ($row = mysqli_fetch_assoc($result)) {
                //     $selected = $isEdit && $product['product_type_id'] == $row['product_type_id'] ? 'selected' : '';
                //     echo "<option value='{$row['product_type_id']}' $selected>{$row['product_type_id']}</option>";
                // }
                while ($row = mysqli_fetch_assoc($result)) {
                    // Kiểm tra điều kiện chọn nếu là chỉnh sửa
                    $selected = $isEdit && $product['product_type_id'] == $row['product_type_id'] ? 'selected' : '';
                    // Hiển thị tên loại sản phẩm trong tùy chọn
                    echo "<option value='{$row['product_type_id']}' $selected>{$row['product_type_name']}</option>";
                }
                echo '</select>';
                ?>
            </div>

            <div class="form-group">
                <label for="product_description">Product Description:</label>
                <textarea id="product_description" name="product_description" class="form-control" required><?php echo $isEdit ? $product['product_description'] : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="product_price">Product Price:</label>
                <input type="number" id="product_price" name="product_price" step="0.01" class="form-control" value="<?php echo $isEdit ? $product['product_price'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="producer_id">Producer ID:</label>
                <?php
                $db_link = $conn->connectToMySQL(); // Use the MySQL connection
                $sql = "SELECT * FROM producer ORDER BY producer_id";
                $result = mysqli_query($db_link, $sql); // Use the MySQLi connection here

                echo '<select id="producer_id" name="producer_id" class="form-control" required>';
                while ($row = mysqli_fetch_assoc($result)) {
                    $selected = $isEdit && $product['producer_id'] == $row['producer_id'] ? 'selected' : '';
                    echo "<option value='{$row['producer_id']}' $selected>{$row['producer_id']}</option>";
                }
                echo '</select>';
                ?>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" class="form-control" value="<?php echo $isEdit ? $product['quantity'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="product_img">Product Image:</label>
                <input type="file" id="product_img" name="product_img" class="form-control" <?php echo !$isEdit ? 'required' : ''; ?>>
                <?php if ($isEdit && $product['product_img']) { ?>
                    <img src="<?php echo $product['product_img']; ?>" alt="Product Image" style="width:100px; height:auto;">
                <?php } ?>
            </div>

            <div class="form-group">
                <label for="product_video_url">Product Video URL:</label>
                <input type="url" id="product_video_url" name="product_video_url" class="form-control" value="<?php echo $isEdit ? $product['product_video_url'] : ''; ?>">
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Product' : 'Add Product'; ?></button>
            <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <?php

    function redirect($url)
    {
        // header("Location: $url");
        exit();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_id = $_POST['product_id'] ?? null;
        $product_name = $_POST['product_name'];
        $product_type_id = $_POST['product_type_id'];
        $product_description = $_POST['product_description'];
        $product_price = $_POST['product_price'];
        $producer_id = $_POST['producer_id'];
        $quantity = $_POST['quantity'];
        $product_video_url = $_POST['product_video_url'] ?? ''; // Get the video URL

        // Define the product image variable
        $product_img = '';

        // Handle the file upload
        if (!empty($_FILES["product_img"]["name"])) {
            $target_dir = "uploads/";
            $file_name = basename($_FILES["product_img"]["name"]);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
                $product_img = $file_name;
            } else {
                echo "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
                exit();
            }
        }

        // Verify producer_id exists
        $db_link = $conn->connectToPDO();
        $producer_check = $db_link->prepare("SELECT * FROM producer WHERE producer_id = ?");
        $producer_check->execute([$producer_id]);
        $producer_result = $producer_check->fetch(PDO::FETCH_ASSOC);

        if (!$producer_result) {
            echo "<div class='alert alert-danger'>Invalid producer ID.</div>";
            exit();
        }

        // Handle product delete request
        if (isset($_GET['delete'])) {
            $product_id = $_GET['delete'];
            $sql = "DELETE FROM product WHERE product_id = ?";
            $stmt = $db_link->prepare($sql);
            $stmt->execute([$product_id]);

            if ($stmt->rowCount() > 0) {
                echo "<div class='alert alert-success'>Product deleted successfully!</div>";
                redirect("manage_products.php"); // Redirect back to manage_product page
            } else {
                echo "<div class='alert alert-danger'>Error: Unable to delete product or product not found.</div>";
            }
        } else {
            // Update or Insert product
            if ($product_id) {
                // Update product
                if ($product_img) {
                    $sql = "UPDATE product SET product_name = ?, product_type_id = ?, product_description = ?, product_price = ?, product_img = ?, producer_id = ?, quantity = ?, product_video_url = ? WHERE product_id = ?";
                    $stmt = $db_link->prepare($sql);
                    $stmt->execute([$product_name, $product_type_id, $product_description, $product_price, $product_img, $producer_id, $quantity, $product_video_url, $product_id]);
                } else {
                    $sql = "UPDATE product SET product_name = ?, product_type_id = ?, product_description = ?, product_price = ?, producer_id = ?, quantity = ?, product_video_url = ? WHERE product_id = ?";
                    $stmt = $db_link->prepare($sql);
                    $stmt->execute([$product_name, $product_type_id, $product_description, $product_price, $producer_id, $quantity, $product_video_url, $product_id]);
                }
            } else {
                // Insert new product
                $sql = "INSERT INTO product (product_name, product_type_id, product_description, product_price, producer_id, quantity, product_img, product_video_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db_link->prepare($sql);
                $stmt->execute([$product_name, $product_type_id, $product_description, $product_price, $producer_id, $quantity, $product_img, $product_video_url]);
            }

            if ($stmt->rowCount() > 0) {
                echo "<div class='alert alert-success'>" . ($product_id ? "Product updated successfully!" : "Product added successfully!") . "</div>";
                redirect("manage_products.php"); // Redirect back to manage_product page
            } else {
                echo "<div class='alert alert-danger'>Error: Unable to " . ($product_id ? "update" : "add") . " product.</div>";
            }
        }
    }
    ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>