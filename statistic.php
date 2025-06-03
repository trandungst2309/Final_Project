<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motorbike";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Hàm để thực thi câu lệnh SQL và xử lý lỗi
function executeQuery($conn, $query)
{
    $result = $conn->query($query);
    if ($result === false) {
        die("Lỗi SQL: " . $conn->error);
    }
    return $result;
}

// Lấy tổng số sản phẩm bán ra
$totalQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM `order`";
$totalQuantityResult = executeQuery($conn, $totalQuantityQuery);
$totalQuantity = $totalQuantityResult->fetch_assoc()['total_quantity'];

// Lấy tổng doanh thu
$totalRevenueQuery = "SELECT SUM(o.quantity * p.product_price) AS total_revenue
FROM `order` o
JOIN `product` p ON o.product_id = p.product_id;
";
$totalRevenueResult = executeQuery($conn, $totalRevenueQuery);
$totalRevenue = $totalRevenueResult->fetch_assoc()['total_revenue'];

// Lấy tổng số đơn hàng
$totalOrdersQuery = "SELECT COUNT(order_id) AS total_orders FROM `order`";
$totalOrdersResult = executeQuery($conn, $totalOrdersQuery);
$totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'];

// Xử lý khi chọn tháng
$monthOrders = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_month = intval($_POST['month']); // Chuyển đổi thành số nguyên để bảo mật

    // Truy vấn đơn hàng theo tháng
    // Truy vấn đơn hàng theo tháng
    $monthOrdersQuery = "
SELECT o.order_id, o.order_date, o.quantity, o.customer_name, o.order_status, 
       p.product_name, p.product_price, p.product_img
FROM `order` o
JOIN `product` p ON o.product_id = p.product_id
WHERE MONTH(o.order_date) = $selected_month
";

    $monthOrdersResult = executeQuery($conn, $monthOrdersQuery);

    while ($row = $monthOrdersResult->fetch_assoc()) {
        $monthOrders[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Feedback Management - TD Motor</title>
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
    </style>
</head>

<body>
    <!-- Navbar -->
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
                <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header" style="color: red; font-weight: bold;">
                                                    <h2>Statistics Management</h2>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Total Quantity Sold</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="card-text">
                                                                        <?php echo number_format($totalQuantity); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Total Revenue</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="card-text">
                                                                        $<?php echo number_format($totalRevenue); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Total Orders</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="card-text">
                                                                        <?php echo number_format($totalOrders); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Filter by Month -->
                                                        <div class="col-md-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5>Filter Orders by Month</h5>
                                                                </div>
                                                                <div class="card-block">
                                                                    <form method="POST" action="">
                                                                        <div class="form-group" style="margin: 15px;">
                                                                            <label for="month">Select Month:</label>
                                                                            <select class="form-control" id="month"
                                                                                name="month">
                                                                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                                                                <option value="<?php echo $i; ?>">
                                                                                    <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                                                                                </option>
                                                                                <?php endfor; ?>
                                                                            </select>
                                                                        </div>
                                                                        <button type="submit"
                                                                            class="btn btn-primary" style="margin-bottom: 10px; margin-left: 10px">View Orders</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Display Orders -->
                                                        <div class="col-md-12">
                                                            <?php if (!empty($monthOrders)): ?>
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5>Orders for
                                                                        <?php echo date('F', mktime(0, 0, 0, $selected_month, 10)); ?>
                                                                    </h5>
                                                                </div>
                                                                <div class="card-block">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Order ID</th>
                                                                                    <th>Date</th>
                                                                                    <th>Product Name</th>
                                                                                    <th>Price</th>
                                                                                    <th>Quantity</th>
                                                                                    <th>Status</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($monthOrders as $order): ?>
                                                                                <tr>
                                                                                    <td><?php echo $order['order_id']; ?>
                                                                                    </td>
                                                                                    <td><?php echo $order['order_date']; ?>
                                                                                    </td>
                                                                                    <td><?php echo $order['product_name']; ?>
                                                                                    </td>
                                                                                    <td>$<?php echo number_format($order['product_price']); ?>
                                                                                    </td>
                                                                                    <td><?php echo $order['quantity']; ?>
                                                                                    </td>
                                                                                    <td><?php echo $order['order_status']; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                                                            <div class="alert alert-warning" role="alert">
                                                                No orders found for this month.
                                                            </div>
                                                            <?php endif; ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
    </main>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>