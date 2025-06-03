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
                <div class="pcoded-inner-content">
                    <!-- Account Management Page Content Start -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 style="color: red; font-weight: bold;">Order Management</h2>
                                <br>
                                <!-- Filter Form -->
                                <form method="post" action="">
                                    <div class="form-group" style="margin: 10px;">
                                        <label for="product_name">Filter by Product Name:</label>
                                        <input type="text" name="product_name" class="form-control" id="product_name"
                                            placeholder="Enter product name" style="margin: 10px;">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </form>

                                <br>
                                <!-- Orders Table -->
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order ID</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Payment</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            // Kết nối cơ sở dữ liệu
                                            $conn = new mysqli('localhost', 'root', '', 'motorbike');

                                            if ($conn->connect_error) {
                                                die("Connection failed: " . $conn->connect_error);
                                            }

                                            // Khởi tạo biến sản phẩm và tạo truy vấn
                                            $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
                                            $sql = "SELECT `order`.order_id, customer.customer_name, product.product_name, `order`.quantity, product.product_price, `order`.payment, `order`.order_status
                                                    FROM `order`
                                                    INNER JOIN customer ON `order`.customer_id = customer.customer_id
                                                    INNER JOIN product ON `order`.product_id = product.product_id";

                                            // Nếu có sản phẩm được nhập, thêm điều kiện WHERE vào truy vấn
                                            if (!empty($product_name)) {
                                                $sql .= " WHERE product.product_name LIKE '%" . $conn->real_escape_string($product_name) . "%'";
                                            }

                                            $result = $conn->query($sql);

                                            if ($result === false) {
                                                echo "<tr><td colspan='8' class='text-center'>Error: " . $conn->error . "</td></tr>";
                                            } elseif ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<th scope='row'>" . $row['order_id'] . "</th>";
                                                    echo "<td>" . $row['customer_name'] . "</td>";
                                                    echo "<td>" . $row['product_name'] . "</td>";
                                                    echo "<td>" . $row['quantity'] . "</td>";
                                                    echo "<td>$ " . $row['product_price'] . "</td>";
                                                    echo "<td>" . $row['payment'] . "</td>";
                                                    echo "<td>";
                                                    echo "<form method='post' action='update_order_status.php'>";
                                                    echo "<input type='hidden' name='order_id' value='" . $row['order_id'] . "'>";
                                                    echo "<select name='order_status' class='form-control'>";
                                                    echo "<option value='Pending'" . ($row['order_status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                                                    echo "<option value='Processing'" . ($row['order_status'] == 'Processing' ? ' selected' : '') . ">Processing</option>";
                                                    echo "<option value='Completed'" . ($row['order_status'] == 'Completed' ? ' selected' : '') . ">Completed</option>";
                                                    echo "<option value='Cancelled'" . ($row['order_status'] == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>";
                                                    echo "</select>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<button type='submit' class='btn btn-success btn-sm'>Update Status</button>";
                                                    echo "</form>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center'>No orders found</td></tr>";
                                            }

                                            $conn->close();
                                            ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bootstrap JS and dependencies -->
                            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js">
                            </script>
                            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js">
                            </script>

                            <script>
                            function updateStatus(orderId) {
                                // Add code to update order status
                                alert('Updating order status for Order ID: ' + orderId);
                            }
                            </script>
                        </div>
                    </div>
                    <!-- Account Management Page Content End -->
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>