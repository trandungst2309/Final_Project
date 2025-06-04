<?php
session_start();
// Include the database connection file (your Connect class)
include 'connect.php'; 

if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Instantiate the Connect class and get the PDO connection
$db = new Connect(); // Create an instance of your Connect class
$conn = $db->connectToPDO(); // Get the PDO connection object

// Initialize variables for dashboard data
$totalCustomers = 0;
$totalProducts = 0;
$pendingOrders = 0;
$completedOrders = 0;
$newContactMessages = 0;
$totalRevenue = 0;

// Fetch data for the dashboard
try {
    // Total Customers
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_customers FROM customer WHERE role = 'customer'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCustomers = $result['total_customers'];

    // Total Products
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM product");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalProducts = $result['total_products'];

    // Pending Orders
    $stmt = $conn->prepare("SELECT COUNT(*) AS pending_orders FROM `order` WHERE order_status = 'Pending'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pendingOrders = $result['pending_orders'];

    // Completed Orders
    $stmt = $conn->prepare("SELECT COUNT(*) AS completed_orders FROM `order` WHERE order_status = 'Completed'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $completedOrders = $result['completed_orders'];

    // New Contact Messages (e.g., messages from today or not yet responded to, for simplicity, we count all new messages)
    $stmt = $conn->prepare("SELECT COUNT(*) AS new_messages FROM contact_messages");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newContactMessages = $result['new_messages'];

    // Total Revenue from Completed Orders
    $stmt = $conn->prepare("SELECT SUM(o.quantity * p.product_price) AS total_revenue FROM `order` o JOIN product p ON o.product_id = p.product_id WHERE o.order_status = 'Completed'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRevenue = $result['total_revenue'] ?? 0;
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Close the connection
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

    .logo-img {
        height: 120px;
        display: block;
        margin: 20px auto;
    }
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
        <div class="row" style="height: calc(100vh - 56px);">
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
                <hr class="text-white">
                <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                <h3 class="mb-4">Welcome, Admin 👋</h3>
                <p>This is your admin page. Use the sidebar to manage website content.</p>
                <img src="image/TDlogo.png" alt="TD Motor Logo" class="logo-img rounded-circle shadow">

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5><i class="bi bi-people"></i> Total Customers</h5>
                            <div class="display-4"><?php echo $totalCustomers; ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5><i class="bi bi-box-seam"></i> Total Products</h5>
                            <div class="display-4"><?php echo $totalProducts; ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5><i class="bi bi-hourglass-split"></i> Pending Orders</h5>
                            <div class="display-4"><?php echo $pendingOrders; ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5><i class="bi bi-check-circle"></i> Completed Orders</h5>
                            <div class="display-4"><?php echo $completedOrders; ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5><i class="bi bi-envelope"></i> New Contact Messages</h5>
                            <div class="display-4"><?php echo $newContactMessages; ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5><i class="bi bi-currency-dollar"></i> Total Revenue</h5>
                            <div class="display-4">$<?php echo number_format($totalRevenue, 2); ?></div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>