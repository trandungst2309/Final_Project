<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$db_link = $connect->connectToPDO();

$error = "";

// Pagination logic
$productsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $productsPerPage;

try {
    $countStmt = $db_link->query("SELECT COUNT(*) FROM product");
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $productsPerPage);

    $sql = "
        SELECT 
            p.*, 
            pt.product_type_name
        FROM product p 
        JOIN product_type pt ON p.product_type_id = pt.product_type_id
        ORDER BY p.product_id ASC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $db_link->prepare($sql);
    $stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error fetching products: " . $e->getMessage();
}
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
            display: flex; 
        }
        .sidebar {
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
            width: auto;
            object-fit: cover;
        }
        .action-buttons a {
            margin-right: 8px;
        }
        main {
            flex-grow: 1;
            overflow-y: auto; 
        }
        .logo-text {
            color: whitesmoke; 
            font-weight: bold; 
            font-size: larger;
        }

        .page-item:hover .page-link {
            background-color: #0d6efd; 
            color: white; 
            text-decoration: none;
            transition: background-color 0.3s ease;
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
            <?= htmlspecialchars($_SESSION['customer_name'] ?? 'Admin'); ?>
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
            <h2 style="color: red; font-weight: bold;">Product Management</h2> <br>
            <a href="add_product.php" class="btn btn-info mb-3">Add Product</a>
            <a href="admin.php" class="btn btn-success mb-3">Back to Homepage</a>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($products)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr class="table-dark" style="color: whitesmoke;">
                                <th>Product Name</th>
                                <th>Product Type</th>
                                <th>Product Price</th>
                                <th>Product Image</th>
                                <th>Quantity</th>
                                <th>Quantity Sold</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $row): ?>
                                <?php 
                                    $img_path = 'uploads/' . $row['product_img'];
                                    $img_url = (file_exists($img_path) && !empty($row['product_img'])) ? $img_path : 'image/default-product.png'; 
                                    $total_base_quantity = 10;
                                    $quantity_in_stock = (int)$row['quantity'];
                                    $quantity_sold = max(0, $total_base_quantity - $quantity_in_stock); 
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['product_type_name']) ?></td>
                                    <td><?= htmlspecialchars($row['product_price']) ?></td>
                                    <td><img src="<?= htmlspecialchars($img_url) ?>" alt="Product Image" style="width:100px; height:auto;"></td>
                                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                                    <td><?= htmlspecialchars($quantity_sold) ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?= htmlspecialchars($row['product_id']) ?>" class="btn btn-primary">Edit</a>
                                        <a href="delete_product.php?delete=<?= htmlspecialchars($row['product_id']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info mt-3">No products found.</div>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
