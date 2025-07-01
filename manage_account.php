<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToPDO();
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
    }

    .sidebar {
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

    main {
        flex-grow: 1;
        overflow-y: auto;
    }

    .logo-img {
        height: 120px;
        display: block;
        margin: 20px auto;
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
                <?= htmlspecialchars($_SESSION['customer_name'] ?? 'Admin'); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <?php if (isset($_SESSION['role_update_message'])): ?>
    <div id="role-alert"
        class="alert alert-info alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4"
        style="z-index: 1050; min-width: 300px;" role="alert">
        <?= htmlspecialchars($_SESSION['role_update_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
    setTimeout(() => {
        const alertBox = document.getElementById('role-alert');
        if (alertBox) {
            alertBox.classList.remove('show');
            alertBox.classList.add('fade');
        }
    }, 3000);
    </script>
    <?php unset($_SESSION['role_update_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['delete_message'])): ?>
    <div id="delete-alert"
        class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5"
        style="z-index: 1050; min-width: 300px;" role="alert">
        <?= htmlspecialchars($_SESSION['delete_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
    setTimeout(() => {
        const deleteAlert = document.getElementById('delete-alert');
        if (deleteAlert) {
            deleteAlert.classList.remove('show');
            deleteAlert.classList.add('fade');
        }
    }, 3000);
    </script>
    <?php unset($_SESSION['delete_message']); ?>
    <?php endif; ?>


    <div class="container-fluid">
        <div class="row">
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
                <h2 style="color: red; font-weight: bold;">Account Management</h2>
                    <a href="add_producer.php" class="btn btn-info mb-3">Add Producer</a>
                    <a href="admin.php" class="btn btn-success mb-3">Back to Dashboard</a>
                <div class="card">
                    <!-- <div class="card-header text-danger fw-bold">
                        <h2>Account Management</h2>
                    </div> -->
                    
                    <div class="card-body">
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
                            try {
                                $stmt = $conn->prepare("SELECT * FROM customer");
                                $stmt->execute();
                                $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($customers) {
                                    foreach ($customers as $row) {
                                        echo "<tr>";
                                        echo "<td>{$row['customer_id']}</td>";
                                        echo "<td>{$row['customer_name']}</td>";
                                        echo "<td>{$row['email']}</td>";
                                        echo "<td>{$row['phone']}</td>";
                                        echo "<td>{$row['address']}</td>";
                                        echo "<td>
                                                <form action='update_role.php' method='post'>
                                                    <input type='hidden' name='customer_id' value='{$row['customer_id']}'>
                                                    <select name='role' class='form-select form-select-sm' onchange='this.form.submit()'>
                                                        <option value='customer' " . ($row['role'] === 'customer' ? 'selected' : '') . ">Customer</option>
                                                        <option value='admin' " . ($row['role'] === 'admin' ? 'selected' : '') . ">Admin</option>
                                                    </select>
                                                </form>
                                            </td>";

                                        echo "<td>
                                                <form action='delete_account.php' method='post' style='display:inline-block;'>
                                                    <input type='hidden' name='delete_id' value='{$row['customer_id']}'>
                                                    <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                                                </form>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No records found.</td></tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>