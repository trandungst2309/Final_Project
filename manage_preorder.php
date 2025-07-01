<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$db = $connect->connectToPDO();

// Cập nhật trạng thái đơn đặt trước
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preorder_id'], $_POST['status'])) {
    $stmt = $db->prepare("UPDATE preorder SET status = :status WHERE preorder_id = :id");
    $stmt->execute([
        ':status' => $_POST['status'],
        ':id' => $_POST['preorder_id']
    ]);
}

// Lấy danh sách preorder
$stmt = $db->query("SELECT p.*, c.customer_name, c.email FROM preorder p JOIN customer c ON p.customer_id = c.customer_id ORDER BY p.order_date DESC");
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
                                <th>Preferred Delivery</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($preorders as $order): ?>
                            <tr>
                                <td><?= $order['preorder_id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?><br><small><?= htmlspecialchars($order['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td>
                                    <?php
                                $img = $order['product_image'];
                                $path = 'uploads/preorders/' . $img;
                                if ($img && file_exists($path)) {
                                    echo "<img src='$path' alt='img'>";
                                } else {
                                    echo "<span class='text-muted'>No image</span>";
                                }
                                ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($order['description'])) ?></td>
                                <td>$<?= number_format($order['expected_price']) ?></td>
                                <td><?= htmlspecialchars($order['preferred_delivery_date']) ?></td>
                                <td>
                                    <span class="badge 
                                    <?= $order['status'] === 'Approved' ? 'bg-success' : 
                                        ($order['status'] === 'Rejected' ? 'bg-danger' : 'bg-secondary') ?>">
                                        <?= $order['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="preorder_id" value="<?= $order['preorder_id'] ?>">
                                        <select name="status" class="form-select form-select-sm mb-2">
                                            <option value="Pending"
                                                <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved"
                                                <?= $order['status'] === 'Approved' ? 'selected' : '' ?>>Approved
                                            </option>
                                            <option value="Rejected"
                                                <?= $order['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected
                                            </option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center">No pre-orders found.</div>
                <?php endif ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>