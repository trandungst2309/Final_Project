<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$preorder_id = $_GET['preorder_id'] ?? null;

if (!$preorder_id) {
    echo "<div class='alert alert-danger'>Invalid Pre-order ID.</div>";
    exit;
}

$conn = new Connect();
$db = $conn->connectToPDO();

// Lấy thông tin đơn hàng
$stmt = $db->prepare("SELECT * FROM preorder WHERE preorder_id = ? AND customer_id = ?");
$stmt->execute([$preorder_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='alert alert-danger'>Pre-order not found or access denied.</div>";
    exit;
}

// Kiểm tra điều kiện hợp lệ để thanh toán
if ($order['status'] !== 'Arrived' || !$order['is_deposit_paid']) {
    echo "<div class='alert alert-warning'>This order is not eligible for final payment yet.</div>";
    exit;
}

// Nếu submit thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update = $db->prepare("UPDATE preorder SET final_payment_status = 1 WHERE preorder_id = ?");
    $update->execute([$preorder_id]);

    echo "<div class='alert alert-success text-center mt-4'>Thank you! Your remaining payment has been completed.</div>";
    echo '<div class="text-center mt-3"><a href="preorder_list.php" class="btn btn-primary">Back to My Pre-orders</a></div>';
    exit;
}

// Tính số tiền còn lại
$expected = $order['expected_price'];
$deposit = $order['deposit_amount'];
$remaining = $expected - $deposit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Final Payment - TD Motor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<!-- <style>
    .card {
        max-width: 600px;
        margin-top: 50px;
        padding: 100px;
        border-radius: 10px;
    }
</style> -->
<body class="bg-light">
    <?php include 'header.php'; ?>
    <div class="container py-5">
        <div class="card shadow-sm mx-auto" style="max-width: 500px; border-radius: 12px; padding: 100px;">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Complete Final Payment</h4>
            </div>
            <div class="card-body">
                <p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
                <p><strong>Expected Price:</strong> $<?= number_format($expected, 2) ?></p>
                <p><strong>Deposit Paid:</strong> $<?= number_format($deposit, 2) ?></p>
                <hr>
                <p><strong>Remaining Balance:</strong> <span class="text-danger fw-bold">$<?= number_format($remaining, 2) ?></span></p>

                <?php if ($order['final_payment_status']): ?>
                    <div class="alert alert-info mt-4">You have already completed the final payment.</div>
                <?php else: ?>
                    <form method="POST">
                        <button type="submit" class="btn btn-success w-100">Pay Now</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
<?php include 'footer.php'; ?>
</html>
