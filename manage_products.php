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

    // Lấy trực tiếp sold_quantity từ bảng product
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
        min-height: 100vh; /* Đảm bảo body đủ cao để chứa nội dung */
        display: flex;
        flex-direction: column;
    }
    .navbar {
        flex-shrink: 0; /* Đảm bảo navbar không co lại */
    }
    .container-fluid {
        flex-grow: 1; /* Đảm bảo container-fluid chiếm hết không gian còn lại */
        display: flex;
        flex-direction: column; /* Đặt flex-direction cho container-fluid */
    }
    .row {
        flex-grow: 1; /* Đảm bảo hàng chiếm hết không gian còn lại */
    }

    .sidebar {
        height: 100%; /* Đặt chiều cao 100% của phần tử cha (row) */
        background-color: #343a40;
        /* Optional: overflow-y: auto; nếu nội dung sidebar có thể dài hơn màn hình */
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
    /* Đảm bảo main content chiếm hết chiều cao còn lại và có thể cuộn */
    main {
        flex-grow: 1;
        overflow-y: auto; /* Cho phép cuộn nếu nội dung quá dài */
    }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <div class="d-flex align-items-center logo">
        <a href="admin.php" class="d-flex align-items-center text-decoration-none">
            <img src="image/TDicon1.png" alt="TD Motor Logo" style="height: 70px;">
            <span class="text-white ms-3 fw-bold fs-5 d-none d-md-inline">TD Motor Admin Page</span>
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
            <a href="manage_preorder.php"><i class="bi bi-calendar-check"></i> Pre-order Management</a>
            <hr class="text-white">
            <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
        </aside>

        <main class="col-md-9 col-lg-10 p-4">
            <h2 class="text-danger fw-bold">Product Management</h2>
            <a href="add_product.php" class="btn btn-info mb-3">Add Product</a>
            <a href="admin.php" class="btn btn-success mb-3">Back to Dashboard</a>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($products)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark text-white">
                            <tr>
                                <th>Product Name</th>
                                <th>Product Type</th>
                                <th>Price</th>
                                <th>Image</th>
                                <th>Stock Quantity</th>
                                <th>Quantity Sold</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $row): ?>
                                <?php
                                    $img_path = 'uploads/' . $row['product_img'];
                                    $img_url = (file_exists($img_path) && !empty($row['product_img'])) ? $img_path : 'image/default-product.png';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['product_type_name']) ?></td>
                                    <td>$<?= number_format($row['product_price']) ?></td>
                                    <td><img src="<?= htmlspecialchars($img_url) ?>" alt="Product Image" style="width:100px; height:auto;"></td>
                                    <td><?= (int)$row['quantity'] ?></td>
                                    <td><?= (int)$row['sold_quantity'] ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="delete_product.php?delete=<?= $row['product_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav>
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
                <div class="alert alert-info">No products found.</div>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
