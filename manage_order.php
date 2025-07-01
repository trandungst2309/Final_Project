<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToMySQL();

// Pagination
$orders_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $orders_per_page;

$product_name = $_POST['product_name'] ?? '';
$filter_condition = '';
if (!empty($product_name)) {
    $escaped = $conn->real_escape_string($product_name);
    $filter_condition = " WHERE p.product_name LIKE '%$escaped%'";
}

$sql_count = "SELECT COUNT(*) AS total FROM `order` o INNER JOIN product p ON o.product_id = p.product_id $filter_condition";
$count_result = $conn->query($sql_count);
$total_orders = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $orders_per_page);

$sql = "SELECT o.order_id, IFNULL(c.customer_name, 'Unknown') AS customer_name,
               p.product_name, o.quantity, p.product_price, o.payment, o.order_status, o.is_paid
        FROM `order` o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        INNER JOIN product p ON o.product_id = p.product_id
        $filter_condition
        LIMIT $orders_per_page OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management - TD Motor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; }
        .container-fluid { flex-grow: 1; display: flex; flex-direction: column; }
        .row { flex-grow: 1; }
        .sidebar { background-color: #343a40; }
        .sidebar a { color: white; display: block; padding: 12px 20px; text-decoration: none; }
        .sidebar a:hover { background-color: #495057; }
        main { flex-grow: 1; overflow-y: auto; }
        .table img { height: 60px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <div class="d-flex align-items-center logo">
        <a href="admin.php" class="d-flex align-items-center text-decoration-none">
            <img src="image/TDicon1.png" alt="TD Motor Logo" style="height: 70px;">
            <span class="logo-text ms-3 d-none d-md-inline text-white fw-bold fs-5">TD Motor Admin Page</span>
        </a>
    </div>
    <div class="ms-auto d-flex align-items-center">
        <span class="text-white me-3"><?= htmlspecialchars($_SESSION['customer_name'] ?? 'Admin'); ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
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
            <div class="card">
                <div class="card-header">
                    <h2 class="text-danger fw-bold">Order Management</h2>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-4">
                        <label for="product_name" class="form-label">Filter by Product Name:</label>
                        <input type="text" name="product_name" class="form-control mb-2" placeholder="Enter product name">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Is Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['order_id'] ?></td>
                                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                                        <td><?= $row['quantity'] ?></td>
                                        <td>$<?= number_format($row['product_price']) ?></td>
                                        <td><?= $row['payment'] ?></td>
                                        <td>
                                            <form method="post" action="update_order_status.php" class="d-flex">
                                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                                <select name="order_status" class="form-select me-2">
                                                    <option value="Pending" <?= $row['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Processing" <?= $row['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="Completed" <?= $row['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="Cancelled" <?= $row['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" class="btn btn-success btn-sm">Update</button>
                                            </form>
                                        </td>
                                        <td>
                                            <?php if ($row['is_paid']): ?>
                                                <span class="badge bg-success">Paid</span>
                                            <?php else: ?>
                                                <form method="post" action="confirm_payment.php">
                                                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm">Confirm Payment</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="8" class="text-center">No orders found</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>