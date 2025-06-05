<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToMySQL();

$error = "";

// Handling form submission
if (isset($_POST['submit'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_type_id = $_POST['product_type_id'];
    $product_price = $_POST['product_price'];
    $product_img = $_POST['product_img'];
    $producer_id = $_POST['producer_id'];
    $quantity = $_POST['quantity'];

    $sql = "SELECT * FROM product WHERE product_id = '$product_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $sql = "UPDATE product SET 
                    product_name='$product_name', 
                    product_type_id='$product_type_id', 
                    product_price='$product_price', 
                    product_img='$product_img', 
                    quantity='$quantity' 
                WHERE product_id='$product_id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_products.php");
            exit;
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    } else {
        $sql = "INSERT INTO product (product_name, product_type_id, product_price, product_img, quantity) 
                VALUES ('$product_name', '$product_type_id', '$product_price', '$product_img', '$quantity')";
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_products.php");
            exit;
        } else {
            $error = "Error adding product: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management - TD Motor</title>
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
    }
    .sidebar {
        height: 100%;
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
    .table img {
        height: 60px;
    }
    .action-buttons a {
        margin-right: 8px;
    }
    .logo-img {
        height: 120px;
        display: block;
        margin: 20px auto;
    }
    main {
        flex-grow: 1;
        overflow-y: auto;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <div class="d-flex align-items-center logo">
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

    <div class="container-fluid">
        <div class="row">
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
                <hr class="text-white">
                <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                <div class="pcoded-inner-content">
                    <h2 style="color: red; font-weight: bold;">Product Management</h2>
                    <br>
                    <a href="add_product.php" class="btn btn-info mb-3">Add Product</a>
                    <a href="admin.php" class="btn btn-success mb-3">Back to Homepage</a>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php
                    if ($result->num_rows > 0) {
                        echo "<table class='table table-striped'>";
                        echo "<tr><th>Product Name</th><th>Product Type ID</th><th>Product Price</th><th>Product Image</th><th>Quantity</th><th>Action</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            $img_path = 'uploads/' . $row['product_img'];
                            $img_url = file_exists($img_path) ? $img_path : 'path/to/default-image.jpg';
                            echo "<tr>";
                            echo "<td>" . $row['product_name'] . "</td>";
                            echo "<td>" . $row['product_type_id'] . "</td>";
                            echo "<td>" . $row['product_price'] . "</td>";
                            echo "<td><img src='" . $img_url . "' alt='Product Image' style='width:100px; height:auto;'></td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>
                                    <a href='add_product.php?edit=" . $row['product_id'] . "' class='btn btn-primary'>Edit</a>
                                    <a href='delete_product.php?delete=" . $row['product_id'] . "' class='btn btn-danger'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No products found";
                    }
                    $conn->close();
                    ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
