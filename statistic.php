<?php
session_start();
// Khởi tạo session để sử dụng $_SESSION['customer_name'] nếu chưa có
// Bạn nên kiểm tra nếu session đã được bắt đầu ở đầu file hoặc ở một file chung (ví dụ: header.php)
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// Kiểm tra quyền truy cập admin
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Giả định bạn có một file `connect.php` chứa class Connect và phương thức connectToPDO()
// Nếu không, bạn cần tạo file này hoặc thay thế phần kết nối PDO trực tiếp tại đây.
require 'connect.php'; // Đảm bảo đường dẫn đúng đến file connect.php
$connect = new Connect();
$db_link = $connect->connectToPDO(); // Kết nối sử dụng PDO

$error = "";
$selected_month = '';
$selected_year = '';
$current_year = date('Y'); // Lấy năm hiện tại
$current_month = date('n'); // Lấy tháng hiện tại (1-12)

// Khởi tạo các biến thống kê toàn cục
$totalQuantity = 0;
$totalRevenue = 0;
$totalOrders = 0;
$filteredOrders = [];

// Xử lý khi có dữ liệu POST để lọc
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_month = filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT);
    $selected_year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);

    // Kiểm tra tính hợp lệ của tháng và năm
    if ($selected_month === false || $selected_month < 1 || $selected_month > 12) {
        $error = "Invalid month selected.";
        $selected_month = $current_month; // Mặc định về tháng hiện tại nếu không hợp lệ
    }
    if ($selected_year === false || $selected_year < 1900 || $selected_year > (date('Y') + 10)) { // Giới hạn năm cho hợp lý
        $error = "Invalid year selected.";
        $selected_year = $current_year; // Mặc định về năm hiện tại nếu không hợp lệ
    }
} else {
    // Lần đầu tải trang, hiển thị thống kê cho tháng và năm hiện tại
    $selected_month = $current_month;
    $selected_year = $current_year;
}

// Xây dựng điều kiện WHERE cho truy vấn
$whereClause = "";
$params = [];

if (!empty($selected_month) && !empty($selected_year)) {
    $whereClause = "WHERE MONTH(o.order_date) = ? AND YEAR(o.order_date) = ?";
    $params = [$selected_month, $selected_year];
}

// Lấy tổng số sản phẩm bán ra cho tháng/năm được chọn
try {
    $totalQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM `order` o " . $whereClause;
    $stmt = $db_link->prepare($totalQuantityQuery);
    $stmt->execute($params);
    $totalQuantity = $stmt->fetch(PDO::FETCH_ASSOC)['total_quantity'];
    $totalQuantity = $totalQuantity ?? 0; // Đảm bảo là 0 nếu không có dữ liệu
} catch (PDOException $e) {
    $error .= "Error fetching total quantity: " . $e->getMessage();
}

// Lấy tổng doanh thu cho tháng/năm được chọn
try {
    $totalRevenueQuery = "
        SELECT SUM(o.quantity * p.product_price) AS total_revenue
        FROM `order` o
        JOIN `product` p ON o.product_id = p.product_id
        " . $whereClause;
    $stmt = $db_link->prepare($totalRevenueQuery);
    $stmt->execute($params);
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];
    $totalRevenue = $totalRevenue ?? 0; // Đảm bảo là 0 nếu không có dữ liệu
} catch (PDOException $e) {
    $error .= "Error fetching total revenue: " . $e->getMessage();
}

// Lấy tổng số đơn hàng cho tháng/năm được chọn
try {
    $totalOrdersQuery = "SELECT COUNT(order_id) AS total_orders FROM `order` o " . $whereClause;
    $stmt = $db_link->prepare($totalOrdersQuery);
    $stmt->execute($params);
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
    $totalOrders = $totalOrders ?? 0; // Đảm bảo là 0 nếu không có dữ liệu
} catch (PDOException $e) {
    $error .= "Error fetching total orders: " . $e->getMessage();
}

// Truy vấn chi tiết đơn hàng theo tháng/năm được chọn
if (!empty($selected_month) && !empty($selected_year)) {
    try {
        $filteredOrdersQuery = "
            SELECT o.order_id, o.order_date, o.quantity, o.customer_name, o.order_status,
                p.product_name, p.product_price, p.product_img
            FROM `order` o
            JOIN `product` p ON o.product_id = p.product_id
            " . $whereClause . "
            ORDER BY o.order_date DESC
        ";
        $stmt = $db_link->prepare($filteredOrdersQuery);
        $stmt->execute($params);
        $filteredOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error .= "Error fetching filtered orders: " . $e->getMessage();
    }
}

// Ngắt kết nối PDO
$db_link = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistics - TD Motor Admin</title>
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
            display: flex; /* Đảm bảo các cột con trong row flex để sidebar và content giãn hết */
        }
        .sidebar {
            background-color: #343a40; /* Màu đen tối cho sidebar */
        }
        .sidebar a {
            color: white; /* Màu chữ trắng cho sidebar links */
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        main {
            flex-grow: 1;
            overflow-y: auto; /* Cho phép cuộn nếu nội dung quá dài */
        }
        .logo-text {
            color: whitesmoke;
            font-weight: bold;
            font-size: larger;
        }
        .card-header h2, .card-header h5 {
            color: red; /* Tiêu đề card màu đỏ */
            font-weight: bold;
        }
        .card-dashboard {
            background-color: #f8f9fa; /* Nền sáng hơn cho các card thống kê */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Hiệu ứng đổ bóng nhẹ */
            height: 100%; /* Đảm bảo các card có chiều cao bằng nhau trong cùng một hàng */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Căn giữa nội dung theo chiều dọc */
        }
        .card-dashboard h5 {
            color: #343a40;
            margin-bottom: 15px;
        }
        .card-dashboard .display-4 {
            /* Các màu sắc khác nhau để phân biệt */
            color: #28a745; /* green */
            font-weight: bold;
            font-size: 3.5rem; /* Kích thước font mặc định cho display-4 của Bootstrap */
            transition: font-size 0.3s ease-in-out; /* Hiệu ứng chuyển động mượt mà khi đổi font-size */
        }
        .card-dashboard #totalRevenue {
            color: #007bff; /* blue */
        }
        .card-dashboard #totalOrders {
            color: #17a2b8; /* cyan/info */
        }
        .table img {
            height: 60px;
            width: auto;
            object-fit: cover;
            border-radius: 4px;
        }
        .alert {
            margin-top: 15px;
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
                <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Admin'); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row" style="height: calc(100vh - 70px);"> <aside class="col-md-3 col-lg-2 sidebar p-0">
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
                <a href="homepage.php" target="_blank"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                <h2 class="mb-4" style="color: red; font-weight: bold;">Statistics Management</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card card-dashboard">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Quantity Sold</h5>
                                <p class="display-4" id="totalQuantity"><?php echo number_format($totalQuantity, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card card-dashboard">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Revenue</h5>
                                <p class="display-4" id="totalRevenue">$<?php echo rtrim(rtrim(number_format($totalRevenue, 2, '.', ''), '0'), '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card card-dashboard">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Orders</h5>
                                <p class="display-4" id="totalOrders"><?php echo number_format($totalOrders, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Filter Orders by Month and Year</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="month" class="form-label">Select Month:</label>
                                    <select class="form-select" id="month" name="month">
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?= ($selected_month == $i) ? 'selected' : ''; ?>>
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
                                            <option value="<?php echo $y; ?>" <?= ($selected_year == $y) ? 'selected' : ''; ?>>
                                                <?php echo $y; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">View Statistics</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Order Details for
                            <?php
                                echo date('F', mktime(0, 0, 0, $selected_month, 10)) . ", " . $selected_year;
                            ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($filteredOrders)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Customer Name</th>
                                            <th>Product Image</th>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_filtered_revenue_detail = 0; // Tổng doanh thu của các đơn hàng hiển thị trong bảng
                                        foreach ($filteredOrders as $order):
                                            $item_total = $order['quantity'] * $order['product_price'];
                                            $total_filtered_revenue_detail += $item_total;
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                                <td><?= htmlspecialchars($order['order_date']) ?></td>
                                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                <td>
                                                    <?php
                                                        $product_img_path = 'uploads/' . htmlspecialchars($order['product_img']);
                                                        if (!empty($order['product_img']) && file_exists($product_img_path)) {
                                                            echo "<img src='" . $product_img_path . "' alt='Product Image' class='img-thumbnail'>";
                                                        } else {
                                                            echo "<img src='image/default-product.png' alt='Default Image' class='img-thumbnail'>"; // Ảnh mặc định nếu không tìm thấy
                                                        }
                                                    ?>
                                                </td>
                                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                <td>$<?= number_format($order['product_price'], 2) ?></td>
                                                <td><?= htmlspecialchars($order['quantity']) ?></td>
                                                <td>$<?= number_format($item_total, 2) ?></td>
                                                <td><?= htmlspecialchars($order['order_status']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" class="text-end">Total Revenue for this period:</th>
                                            <th colspan="2">$<?= rtrim(rtrim(number_format($total_filtered_revenue_detail, 2, '.', ''), '0'), '.'); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                No orders found for the selected month and year.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function adjustFontSize(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                // Lấy nội dung text và loại bỏ các ký tự không phải số hoặc dấu chấm/phẩy (để tính độ dài số)
                let textContent = element.textContent.replace(/[^0-9,.]/g, '');
                // Đối với Total Revenue có thể có dấu '$', cần loại bỏ nó
                if (elementId === 'totalRevenue') {
                    textContent = textContent.replace('$', '');
                }

                // Nếu bạn đang dùng định dạng số Việt Nam (dấu chấm phân cách nghìn, dấu phẩy thập phân)
                // Cần loại bỏ dấu chấm phân cách nghìn để tính độ dài chính xác của số
                textContent = textContent.replace(/\./g, ''); // Loại bỏ dấu chấm
                textContent = textContent.replace(/,/g, ''); // Loại bỏ dấu phẩy nếu có

                const textLength = textContent.length;

                // Điều chỉnh kích thước font dựa trên độ dài của số
                if (textLength > 6 && textLength <= 9) { // Ví dụ: 7-9 chữ số
                    element.style.fontSize = '2.5rem'; // Giảm kích thước
                } else if (textLength > 9) { // Ví dụ: > 9 chữ số
                    element.style.fontSize = '2rem'; // Giảm nhiều hơn nữa
                } else {
                    element.style.fontSize = '3.5rem'; // Kích thước mặc định (display-4 trong Bootstrap)
                }
            }
        }

        // Gọi hàm điều chỉnh font size cho từng card khi trang tải
        document.addEventListener('DOMContentLoaded', function() {
            adjustFontSize('totalQuantity');
            adjustFontSize('totalRevenue');
            adjustFontSize('totalOrders');
        });
    </script>
</body>
</html>