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
    <title>Account Management - TD Motor</title>
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
                <hr class="text-white">
                <a href="homepage.php"><i class="bi bi-house-door"></i> Back to TD Website</a>
            </aside>

            <!-- Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <div class="pcoded-inner-content">
                    <!-- Account Management Page Content Start -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header" style="color: red; font-weight: bold;">
                                <h2>Account Management</h2>
                            </div>
                            <div class="card-block">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Address</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                    // Connect to the database
                                                    $conn = new mysqli("localhost", "root", "", "motorbike");
                                                    if ($conn->connect_error) {
                                                        die("Connection failed: " . $conn->connect_error);
                                                    }

                                                    // Delete customer account
                                                    if (isset($_POST["delete_id"])) {
                                                        $customer_id = intval($_POST["delete_id"]);
                                                        $delete_sql = "DELETE FROM customer WHERE customer_id = ?";
                                                        $delete_stmt = $conn->prepare($delete_sql);
                                                        if ($delete_stmt) {
                                                            if ($delete_stmt->execute([$customer_id])) {
                                                                echo "<script>alert('Account deleted successfully!'); window.location.href='manage_account.php';</script>";
                                                            } else {
                                                                echo "Error: " . htmlspecialchars($delete_stmt->error);
                                                            }
                                                        } else {
                                                            echo "Error preparing statement: " . htmlspecialchars($conn->error);
                                                        }
                                                    }

                                                    // Update role
                                                    if (isset($_POST['customer_id']) && isset($_POST['role'])) {
                                                        $customer_id = $_POST['customer_id'];
                                                        $role = $_POST['role'];
                                                        $update_sql = "UPDATE customer SET role = ? WHERE customer_id = ?";
                                                        $update_stmt = $conn->prepare($update_sql);
                                                        if ($update_stmt) {
                                                            if ($update_stmt->execute([$role, $customer_id])) {
                                                                echo "<script>alert('Role updated successfully!'); window.location.href='manage_account.php';</script>";
                                                            } else {
                                                                echo "Error: " . htmlspecialchars($update_stmt->error);
                                                            }
                                                        } else {
                                                            echo "Error preparing statement: " . htmlspecialchars($conn->error);
                                                        }
                                                    }

                                                    // Fetch and display customer data
                                                    $sql = "SELECT * FROM customer";
                                                    $result = $conn->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row['customer_id'] . "</td>";
                                                            echo "<td>" . $row['customer_name'] . "</td>";
                                                            echo "<td>" . $row['email'] . "</td>";
                                                            echo "<td>" . $row['phone'] . "</td>";
                                                            echo "<td>" . $row['address'] . "</td>";
                                                            echo "<td>";
                                                            echo "<form action='update_role.php' method='post' onsubmit='return confirmRoleChange();'>";
                                                            echo "<input type='hidden' name='customer_id' value='" . $row['customer_id'] . "'>";
                                                            echo "<select name='role' class='role-select' onchange='this.form.submit()'>";
                                                            echo "<option value='customer'" . ($row['role'] == 'customer' ? ' selected' : '') . ">Customer</option>";
                                                            echo "<option value='admin'" . ($row['role'] == 'admin' ? ' selected' : '') . ">Admin</option>";
                                                            echo "</select>";
                                                            echo "</form>";
                                                            echo "</td>";
                                                            echo "<td>
                                                                        <form action='delete_account.php'  method='post' style='display:inline-block; '''>
                                                                            <input type='hidden' name='delete_id' value='" . $row['customer_id'] . "'>
                                                                            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                                                                        </form>
                                                                    </td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'>No records found.</td></tr>";
                                                    }
                                                    $conn->close();
                                                    ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Account Management Page Content End -->
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>