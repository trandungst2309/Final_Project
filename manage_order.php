<!DOCTYPE html>
<html lang="en">
<!-- save -->

<head>
    <title>Admin Dashboard - Order Management</title>
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
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="loader-track">
            <div class="preloader-wrapper">
                <!-- Spinner Layers -->
                <!-- ... same spinner layers ... -->
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">
                    <div class="navbar-logo">
                        <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#">
                            <i class="ti-menu"></i>
                        </a>
                        <div class="mobile-search waves-effect waves-light">
                            <div class="header-search">
                                <div class="main-search morphsearch-search">
                                    <div class="input-group">
                                        <span class="input-group-addon search-close"><i class="ti-close"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter Keyword">
                                        <span class="input-group-addon search-btn"><i class="ti-search"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="mobile-options waves-effect waves-light">
                            <i class="ti-more"></i>
                        </a>
                    </div>
                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li>
                                <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a>
                                </div>
                            </li>

                            <li>
                                <a href="#" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar">
                        <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="">
                                <div class="main-menu-header">
                                    <!-- <img class="img-80" src="assets/images/fire logo-Photoroom.png" alt="User-Profile-Image"> -->
                                    <!-- <div class="user-details">
                                        <span id="more-details">John Doe<i class=""></i></span>
                                    </div> -->
                                </div>
                            </div>
                            <div class="p-15 p-b-0">
                                <form class="form-material">
                                    <div class="form-group form-primary">
                                    </div>
                                </form>
                            </div>
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
                                        <span class="pcoded-mtext"
                                            data-i18n="nav.basic-components.main">Components</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                    <ul class="pcoded-submenu">
                                        <li class=" ">
                                            <a href="manage_products.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext"
                                                    data-i18n="nav.basic-components.alert">Product Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class=" ">
                                            <a href="manage_account.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext"
                                                    data-i18n="nav.basic-components.breadcrumbs">Account
                                                    Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class=" ">
                                            <a href="manage_order.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Order
                                                    Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                            <ul class="pcoded-item pcoded-left-item">
                                <li>
                                    <a href="statistic.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Statistics
                                            management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="manage_contact.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-headphone-alt"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Contact
                                            management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="manage_feedback.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-comment-alt"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Feedback
                                            management</span>
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
                                                <span class="pcoded-mtext"
                                                    data-i18n="nav.basic-components.alert">Login</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class=" ">
                                            <a href="register.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext"
                                                    data-i18n="nav.basic-components.breadcrumbs">Register</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <div class="pcoded-content">
                        <!-- Page-header start -->
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
                        <!-- Page-header end -->
                        <div class="pcoded-inner-content">
                            <!-- Account Management Page Content Start -->
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Order Management</h5>
                                        <!-- Filter Form -->
                                        <form method="post" action="">
                                            <div class="form-group">
                                                <label for="product_name">Filter by Product Name:</label>
                                                <input type="text" name="product_name" class="form-control"
                                                    id="product_name" placeholder="Enter product name">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </form>

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
                                    <script
                                        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js">
                                    </script>
                                    <script
                                        src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

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


    <script>
    function confirmRoleChange() {
        return confirm('Are you sure you want to change the role?');
    }
    </script>

</body>

</html>