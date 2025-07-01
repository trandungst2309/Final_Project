<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToMySQL();


$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);
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

            <!-- Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <div class="pcoded-inner-content">
                    <!-- Main-body start -->
                    <div class="container mt-4">
                        <h2 style="color: red; font-weight: bold;">Customer Feedback</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Product Name</th>
                                        <th>Content</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                    // Database connection
                    $dsn = 'mysql:host=localhost;dbname=motorbike';
                    $username = 'root';
                    $password = '';
                    
                    try {
                        $pdo = new PDO($dsn, $username, $password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        // Fetch feedbacks
                        $sql = 'SELECT f.content, p.product_name, c.customer_name, f.feedback_date
                                FROM feedback f
                                JOIN product p ON f.product_id = p.product_id
                                JOIN customer c ON f.customer_id = c.customer_id';
                        $stmt = $pdo->query($sql);
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['content']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['feedback_date']) . '</td>';
                            echo '</tr>';
                        }
                    } catch (PDOException $e) {
                        echo 'Database error: ' . $e->getMessage();
                    }
                    ?>
                                </tbody>
                            </table>
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