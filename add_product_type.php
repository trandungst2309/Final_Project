<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php'; // Chắc chắn rằng file Connect.php của bạn được include

$connect = new Connect(); // Tạo một thể hiện của lớp Connect
$conn = $connect->connectToMySQL(); // Sử dụng phương thức connectToMySQL để lấy đối tượng mysqli

$error = ''; // Khởi tạo biến lỗi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_type_name = trim($_POST['product_type_name']);

    if (!empty($product_type_name)) {
        // Sử dụng prepared statement với MySQLi
        $stmt = $conn->prepare("INSERT INTO product_type (product_type_name) VALUES (?)");
        if ($stmt) {
            $stmt->bind_param("s", $product_type_name);
            if ($stmt->execute()) {
                header("Location: manage_product_type.php");
                exit;
            } else {
                $error = "Insert failed: " . $stmt->error; // Lấy lỗi từ statement
            }
            $stmt->close(); // Đóng statement
        } else {
            $error = "Failed to prepare statement: " . $conn->error;
        }
    } else {
        $error = "Product type name is required.";
    }
}
$conn->close(); // Đóng kết nối MySQLi khi hoàn thành
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product Type - TD Motor</title>
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
    .card-custom {
        max-width: 600px;
        margin: 50px auto; /* Center the card horizontally and add top margin */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <div class="d-flex align-items-center logo">
            <a href="admin.php" class="d-flex align-items-center text-decoration-none">
                <img src="image/TDicon1.png" alt="TD Motor Logo" style="height: 70px;">
                <span class="logo-text ms-3 d-none d-md-inline"
                    style="color: whitesmoke; font-weight:bold; font-size:larger">TD Motor Admin Page</span>
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
            <aside class="col-md-3 col-lg-2 sidebar p-0" style="height:auto">
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
                <h3 class="mb-4">Add New Product Type</h3>
                <p>Use this form to add a new product type to your inventory.</p>

                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Product Type Details</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="product_type_name" class="form-label">Product Type Name</label>
                                <input type="text" class="form-control" id="product_type_name" name="product_type_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product Type</button>
                            <a href="manage_product_type.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>