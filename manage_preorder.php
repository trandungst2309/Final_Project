<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$db = $connect->connectToPDO();

// Cập nhật trạng thái preorder và thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['preorder_id'], $_POST['status'])) {
        $preorder_id = $_POST['preorder_id'];
        $status = $_POST['status'];
        $stmt = $db->prepare("UPDATE preorder SET status = :status WHERE preorder_id = :id");
        $stmt->execute([':status' => $status, ':id' => $preorder_id]);
    }

    if (isset($_POST['toggle_paid_id'])) {
        $id = $_POST['toggle_paid_id'];
        $stmt = $db->prepare("UPDATE preorder SET is_deposit_paid = NOT is_deposit_paid WHERE preorder_id = :id");
        $stmt->execute([':id' => $id]);
    }
}

// Lọc đơn theo trạng thái
$filter = $_GET['filter'] ?? '';
$validStatuses = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Waiting Stock', 'Arrived'];
if ($filter && in_array($filter, $validStatuses)) {
    $stmt = $db->prepare("SELECT p.*, c.customer_name, c.email FROM preorder p 
                          JOIN customer c ON p.customer_id = c.customer_id 
                          WHERE p.status = :status ORDER BY p.order_date DESC");
    $stmt->execute([':status' => $filter]);
} else {
    $stmt = $db->query("SELECT p.*, c.customer_name, c.email FROM preorder p 
                        JOIN customer c ON p.customer_id = c.customer_id 
                        ORDER BY p.order_date DESC");
}
$preorders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pre-order Management - TD Motor</title>
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
        main {
            flex-grow: 1;
            overflow-y: auto;
        }
        .table img {
            height: 60px;
            border-radius: 4px;
        }
        .badge-waiting {
            background-color: #0dcaf0;
            color: #000;
        }
        .badge-arrived {
            background-color: #20c997;
            color: #fff;
        }
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
        <span class="text-white me-3"><?= htmlspecialchars($_SESSION['customer_name'] ?? 'Admin') ?></span>
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
            <a href="homepage.php" target="_blank"><i class="bi bi-house-door"></i> Back to TD Website</a>
        </aside>

        <main class="col-md-9 col-lg-10 p-4">
            <h2 class="text-danger mb-4 fw-bold">Pre-order Management</h2>

            <form method="get" class="mb-3">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="filter" class="col-form-label">Filter by Status:</label>
                    </div>
                    <div class="col-auto">
                        <select name="filter" id="filter" class="form-select">
                            <option value="">All</option>
                            <?php foreach ($validStatuses as $status): ?>
                                <option value="<?= $status ?>" <?= $filter === $status ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-danger">Apply</button>
                    </div>
                </div>
            </form>

            <?php if (count($preorders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product Name</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Expected Price</th>
                            <th>Deposit</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($preorders as $order): ?>
                            <tr>
                                <td><?= $order['preorder_id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?><br><small><?= htmlspecialchars($order['email']) ?></small></td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td>
                                    <?php
                                    $img = $order['product_image'];
                                    $path = 'uploads/preorders/' . $img;
                                    if ($img && file_exists($path)) {
                                        echo "<img src='$path' alt='Product Image'>";
                                    } else {
                                        echo "<span class='text-muted'>No image</span>";
                                    }
                                    ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($order['description'])) ?></td>
                                <td>$<?= number_format($order['expected_price'], 2) ?></td>
                                <td>$<?= number_format($order['deposit_amount'], 2) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="toggle_paid_id" value="<?= $order['preorder_id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $order['is_deposit_paid'] ? 'btn-success' : 'btn-warning text-dark' ?>">
                                            <?= $order['is_deposit_paid'] ? 'Paid' : 'Unpaid' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <span class="badge <?= 
                                        $order['status'] === 'Approved' ? 'bg-success' :
                                        ($order['status'] === 'Rejected' ? 'bg-danger' :
                                        ($order['status'] === 'Cancelled' ? 'bg-dark' :
                                        ($order['status'] === 'Waiting Stock' ? 'badge-waiting' :
                                        ($order['status'] === 'Arrived' ? 'badge-arrived' : 'bg-secondary')))) ?>">
                                        <?= $order['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="preorder_id" value="<?= $order['preorder_id'] ?>">
                                        <select name="status" class="form-select form-select-sm mb-2">
                                            <?php foreach ($validStatuses as $status): ?>
                                                <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">No pre-orders found.</div>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
