<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$db = new Connect();
$conn = $db->connectToPDO();

$currentMonth = date('n'); // Lấy tháng hiện tại (1-12)
$currentYear = date('Y');  // Lấy năm hiện tại

// Khởi tạo biến cho tháng và năm được chọn
$selectedMonth = $currentMonth;
$selectedYear = $currentYear;

// Xử lý khi có dữ liệu POST để lọc
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedMonth = filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT);
    $postedYear = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);

    // Kiểm tra tính hợp lệ và gán giá trị
    if ($postedMonth !== false && $postedMonth >= 1 && $postedMonth <= 12) {
        $selectedMonth = $postedMonth;
    }
    if ($postedYear !== false && $postedYear >= 1900 && $postedYear <= (date('Y') + 10)) { // Giới hạn năm cho hợp lý
        $selectedYear = $postedYear;
    }
}

$totalCustomers = 0;
$totalProducts = 0;
$pendingOrders = 0;
$completedOrders = 0;
$newContactMessages = 0;
$totalRevenue = 0;

try {
    // Total Customers (Usually all-time, not filtered by month/year)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_customers FROM customer WHERE role = 'customer'");
    $stmt->execute();
    $totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];

    // Total Products (Usually all-time)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM product");
    $stmt->execute();
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

    // Pending Orders for selected month/year
    $stmt = $conn->prepare("SELECT COUNT(*) AS pending_orders FROM `order` WHERE order_status = 'Pending' AND MONTH(order_date) = ? AND YEAR(order_date) = ?");
    $stmt->execute([$selectedMonth, $selectedYear]);
    $pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['pending_orders'];

    // Completed Orders for selected month/year
    $stmt = $conn->prepare("SELECT COUNT(*) AS completed_orders FROM `order` WHERE order_status = 'Completed' AND MONTH(order_date) = ? AND YEAR(order_date) = ?");
    $stmt->execute([$selectedMonth, $selectedYear]);
    $completedOrders = $stmt->fetch(PDO::FETCH_ASSOC)['completed_orders'];

    // New Contact Messages (can also be filtered by date if needed, but keeping all-time for simplicity)
    $stmt = $conn->prepare("SELECT COUNT(*) AS new_messages FROM contact_messages");
    $stmt->execute();
    $newContactMessages = $stmt->fetch(PDO::FETCH_ASSOC)['new_messages'];

    // Total Revenue for selected month/year (only completed orders)
    $stmt = $conn->prepare("SELECT SUM(o.quantity * p.product_price) AS total_revenue FROM `order` o JOIN product p ON o.product_id = p.product_id WHERE o.order_status = 'Completed' AND MONTH(o.order_date) = ? AND YEAR(o.order_date) = ?");
    $stmt->execute([$selectedMonth, $selectedYear]);
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - TD Motor</title>
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
    .navbar { flex-shrink: 0; }
    .container-fluid { flex-grow: 1; display: flex; flex-direction: column; }
    .row { flex-grow: 1; }
    .sidebar { background-color: #343a40; }
    .sidebar a { color: white; text-decoration: none; display: block; padding: 12px 20px; }
    .sidebar a:hover { background-color: #495057; }
    .table img { height: 60px; }
    main { flex-grow: 1; overflow-y: auto; }
    .logo-img { height: 120px; display: block; margin: 20px auto; }
    .card-dashboard {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }
    .card-dashboard h5 {
        color: #343a40;
        margin-bottom: 15px;
    }
    .card-dashboard .display-4 {
        color: #007bff;
        font-weight: bold;
        font-size: 3.5rem;
        transition: font-size 0.3s ease-in-out;
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
            <a href="manage_preorder.php"><i class="bi bi-calendar-check"></i> Pre-order Management</a>
            <hr class="text-white">
            <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
        </aside>

        <main class="col-md-9 col-lg-10 p-4">
            <h3 class="mb-4">Welcome, Admin 👋</h3>
            <p>This is your admin page. Use the sidebar to manage website content.</p>
            <img src="image/TDlogo.png" alt="TD Motor Logo" class="logo-img rounded-circle shadow">

            <hr class="my-4">

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Filter Statistics</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="admin.php">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="month" class="form-label">Select Month:</label>
                                <select class="form-select" id="month" name="month">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?= ($selectedMonth == $i) ? 'selected' : ''; ?>>
                                            <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="year" class="form-label">Select Year:</label>
                                <select class="form-select" id="year" name="year">
                                    <?php
                                    $start_year = 2020; // Năm bắt đầu cho dropdown
                                    $end_year = date('Y') + 1; // Năm hiện tại + 1
                                    for ($y = $start_year; $y <= $end_year; $y++):
                                    ?>
                                        <option value="<?php echo $y; ?>" <?= ($selectedYear == $y) ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-muted small">Displaying statistics for **<?php echo date('F Y', mktime(0,0,0,$selectedMonth,1,$selectedYear)); ?>**</p>

            <div class="row">
                <div class="col-md-4">
                    <div class="card-dashboard text-center">
                        <h5><i class="bi bi-people"></i> Total Customers</h5>
                        <div class="display-4" id="totalCustomers"><?php echo $totalCustomers; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard text-center">
                        <h5><i class="bi bi-box-seam"></i> Total Products</h5>
                        <div class="display-4" id="totalProducts"><?php echo $totalProducts; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard text-center">
                        <h5><i class="bi bi-hourglass-split"></i> Pending Orders</h5>
                        <div class="display-4" id="pendingOrders"><?php echo $pendingOrders; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard text-center">
                        <h5><i class="bi bi-check-circle"></i> Completed Orders</h5>
                        <div class="display-4" id="completedOrders"><?php echo $completedOrders; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard text-center">
                        <h5><i class="bi bi-envelope"></i> Contact Messages</h5>
                        <div class="display-4" id="newMessages"><?php echo $newContactMessages; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard text-center">
                        <h5><i class="bi bi-currency-dollar"></i> Total Revenue</h5>
                        <div class="display-4" id="totalRevenue">$<?php echo number_format($totalRevenue, 0); ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
    function adjustFontSize(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const length = el.textContent.replace(/[^\d]/g, '').length;
        if (length > 6 && length <= 9) {
            el.style.fontSize = '2.5rem';
        } else if (length > 9) {
            el.style.fontSize = '2rem';
        } else {
            el.style.fontSize = '3.5rem';
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        ['totalCustomers', 'totalProducts', 'pendingOrders', 'completedOrders', 'newMessages', 'totalRevenue'].forEach(adjustFontSize);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>