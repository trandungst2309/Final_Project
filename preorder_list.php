<?php
session_start();
require 'connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$conn = (new Connect())->connectToPDO();

// Lấy danh sách preorder theo customer
$stmt = $conn->prepare("SELECT * FROM preorder WHERE customer_id = :cid ORDER BY order_date DESC");
$stmt->execute([':cid' => $customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Pre-orders - TD Motor</title>
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
        padding-top: 100px;
    }

    .container {
        max-width: 1000px;
        padding: 20px;
    }

    .badge-status {
        padding: 5px 10px;
        font-size: 0.9rem;
        border-radius: 12px;
    }

    .table img {
        height: 60px;
        width: auto;
        object-fit: cover;
        border-radius: 4px;
    }

    .btn-back {
        display:inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #dc3545;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .btn-back:hover {
        background-color: #c82333;
        text-decoration: none;
        color: black;
    }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center text-danger mb-4">My Pre-orders</h2>
        <?php if (count($orders) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Expected Price</th>
                        <th>Preferred Delivery</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
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
                        <td><?= htmlspecialchars($order['preferred_delivery_date']) ?></td>
                        <td>
                            <span class="badge 
                                    <?= $order['status'] === 'Approved' ? 'bg-success' :
                                        ($order['status'] === 'Rejected' ? 'bg-danger' : 'bg-secondary') ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">You haven't submitted any pre-orders yet.</div>
        <?php endif; ?>
        <a href="profile.php" class="btn-back">Back</a>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>