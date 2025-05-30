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
    <title>Statistic Management</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Admin dashboard with account management features." />
    <meta name="keywords" content="bootstrap, admin template, admin dashboard, management" />
    <meta name="author" content="codedthemes" />
    <!-- Favicon icon -->
    <!-- <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon"> -->
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="assets/css_admin/bootstrap/css/bootstrap.min.css">
    <!-- waves.css -->
    <link rel="stylesheet" href="assets/pages/waves/css_admin/waves.min.css" type="text/css" media="all">
    <!-- themify icon -->
    <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css_admin/font-awesome.min.css">
    <!-- scrollbar.css -->
    <link rel="stylesheet" type="text/css" href="assets/css_admin/jquery.mCustomScrollbar.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="assets/css_admin/style.css">
</head>

<body>
    <div class="pcoded" id="pcoded">
        <div class="pcoded-container navbar-wrapper">
            <!-- Nav Header -->
            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">
                    <div class="navbar-logo">
                        <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
                            <i class="ti-menu"></i>
                        </a>
                        <a class="mobile-options waves-effect waves-light">
                            <i class="ti-more"></i>
                        </a>
                    </div>
                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li>
                                <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                            </li>
                            <li class="header-search">
                                <div class="main-search morphsearch-search">
                                    <div class="input-group">
                                        <span class="input-group-addon search-close"><i class="ti-close"></i></span>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Sidebar -->
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar">
                        <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="main-menu-header">
                                <div class="user-details">
                                </div>
                            </div>
                            <br>
                            <ul class="pcoded-item pcoded-left-item">
                                <li class="active">
                                    <a href="admin.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li class="pcoded-hasmenu">
                                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layout-grid2-alt"></i></span>
                                        <span class="pcoded-mtext" data-i18n="nav.basic-components.main">Components</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                    <ul class="pcoded-submenu">
                                        <li class="">
                                            <a href="manage_products.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Product Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class=" ">
                                            <a href="manage_account.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Account Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class=" ">
                                            <a href="manage_order.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Order Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="pcoded-navigation-label" data-i18n="nav.category.forms">Chart</div>
                            <ul class="pcoded-item pcoded-left-item">
                                <li>
                                    <a href="statistic.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Statistics Management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="manage_contact.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-headphone-alt"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Contact management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="manage_feedback.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-comment-alt"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Feedback management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li class="pcoded-hasmenu">
                                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layout-grid2-alt"></i></span>
                                        <span class="pcoded-mtext" data-i18n="nav.basic-components.main">Pages</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                    <ul class="pcoded-submenu">
                                        <li class=" ">
                                            <a href="loginnew.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Login</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a href="register.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Register</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <div class="pcoded-content">
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">Hello Admin</h5>
                                            <p class="m-b-0">Welcome to the admin page</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="breadcrumb-title">
                                            <li class="breadcrumb-item">
                                                <a href="#"> <i class="fa fa-home"></i> </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Statistics Management</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Total Quantity Sold</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="card-text"><?php echo number_format($totalQuantity); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Total Revenue</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="card-text">$<?php echo number_format($totalRevenue); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Total Orders</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="card-text"><?php echo number_format($totalOrders); ?></p>
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
                                                            <div class="form-group">
                                                                <label for="month">Select Month:</label>
                                                                <select class="form-control" id="month" name="month">
                                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                                        <option value="<?php echo $i; ?>"><?php echo date('F', mktime(0, 0, 0, $i, 10)); ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">View Orders</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Display Orders -->
                                            <div class="col-md-12">
                                                <?php if (!empty($monthOrders)): ?>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>Orders for <?php echo date('F', mktime(0, 0, 0, $selected_month, 10)); ?></h5>
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
                                                                                <td><?php echo $order['order_id']; ?></td>
                                                                                <td><?php echo $order['order_date']; ?></td>
                                                                                <td><?php echo $order['product_name']; ?></td>
                                                                                <td>$<?php echo number_format($order['product_price']); ?></td>
                                                                                <td><?php echo $order['quantity']; ?></td>
                                                                                <td><?php echo $order['order_status']; ?></td>
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

            <!-- Footer -->
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="assets/js/jquery/jquery.min.js"></script>
    <script src="assets/js/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery-ui/jquery-ui.min.js "></script>
    <script type="text/javascript" src="assets/js/popper.js/popper.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap/js/bootstrap.min.js "></script>
    <script type="text/javascript" src="assets/pages/widget/excanvas.js "></script>
    <!-- waves js -->
    <script src="assets/pages/waves/js/waves.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="assets/js/jquery-slimscroll/jquery.slimscroll.js "></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="assets/js/modernizr/modernizr.js "></script>
    <!-- slimscroll js -->
    <script type="text/javascript" src="assets/js/SmoothScroll.js"></script>
    <script src="assets/js/jquery.mCustomScrollbar.concat.min.js "></script>
    <!-- Chart js -->
    <script type="text/javascript" src="assets/js/chart.js/Chart.js"></script>
    <!-- amchart js -->
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="assets/pages/widget/amchart/gauge.js"></script>
    <script src="assets/pages/widget/amchart/serial.js"></script>
    <script src="assets/pages/widget/amchart/light.js"></script>
    <script src="assets/pages/widget/amchart/pie.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <!-- menu js -->
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/vertical-layout.min.js "></script>
    <!-- custom js -->
    <script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.js"></script>
    <script type="text/javascript" src="assets/js/script.js "></script>

</body>

</html>