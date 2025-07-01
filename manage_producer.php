<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToMySQL();

$sql = "SELECT * FROM producer";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Producer Management - TD Motor</title>
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
    <div class="row">
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

        <main class="col-md-9 col-lg-10 p-4">
            <h2 style="color: red; font-weight: bold;">Producer Management</h2>
            <a href="add_producer.php" class="btn btn-info mb-3">Add Producer</a>
            <a href="admin.php" class="btn btn-success mb-3">Back to Dashboard</a>

            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                echo "<table class='table table-striped'>";
                echo "<thead class='table-dark'><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead><tbody>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['producer_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['producer_name']) . "</td>";
                    echo "<td>
                            <a href='edit_producer.php?id=" . $row['producer_id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='delete_producer.php?id=" . $row['producer_id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure?');\">Delete</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-info'>No producers found.</div>";
            }
            mysqli_close($conn);
            ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>