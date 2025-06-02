<!DOCTYPE html>
<html lang="en">
<!-- save -->

<head>
    <title>Manage Products </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Admin dashboard with account management features." />
    <meta name="keywords" content="bootstrap, admin template, admin dashboard, management" />
    <meta name="author" content="codedthemes" />
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css_admin/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/pages/waves/css_admin/waves.min.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css_admin/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css_admin/jquery.mCustomScrollbar.css">
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
                        <a href="admin.php" class="logo-container">
                            <img src="image/TDicon1.png" alt="Logo" class="logo-img" style="height: 100px;">
                        </a>
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
                                <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-nav-profile">
                                <!-- <a href="#!" class="waves-effect waves-light">
                                    <?php
                                    // Bắt đầu session nếu chưa được khởi tạo
                                    if (session_status() == PHP_SESSION_NONE) {
                                        session_start();
                                    }
                                    if (isset($_SESSION['customer_name'])) {
                                        echo '<img class="img-radius" src="assets/images/avatar-4.jpg" alt="User-Profile-Image">'; // Ảnh đại diện mặc định
                                        echo '<span>' . htmlspecialchars($_SESSION['customer_name']) . '</span>';
                                    } else {
                                        echo '<img class="img-radius" src="assets/images/avatar-4.jpg" alt="User-Profile-Image">';
                                        echo '<span>Admin</span>';
                                    }
                                    ?>
                                    <i class="ti-angle-down"></i>
                                </a> -->
                                <ul class="show-notification profile-notification">
                                    <li class="waves-effect waves-light">
                                        <a href="profile.php">
                                            <i class="ti-user"></i> Profile
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="logout.php">
                                            <i class="ti-layout-sidebar-left"></i> Logout
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
                            <!-- <div class="">
                                <div class="main-menu-header">
                                    <img class="img-80" src="assets/images/fire logo-Photoroom.png" alt="User-Profile-Image">
                                    <div class="user-details">
                                        <span id="more-details">John Doe<i class=""></i></span>
                                    </div>
                                </div>
                            </div> -->
                            <div class="p-15 p-b-0">
                                <form class="form-material">
                                    <div class="form-group form-primary">
                                    </div>
                                </form>
                            </div>
                            <ul class="pcoded-item pcoded-left-item">
                                <li class="active">
                                    <a href="admin.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-home"></i></span>
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
                                        <li class="">
                                            <a href="homepage.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">TD
                                                    Website</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                            <ul class="pcoded-item pcoded-left-item">
                                <li>
                                    <a href="statistic.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layers"></i></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Statistics
                                            management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="manage_contact.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-headphone-alt"></i></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Contact
                                            management</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="manage_feedback.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-comment-alt"></i></span>
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
                                    <!-- <div class="col-md-4">
                                        <ul class="breadcrumb-title">
                                            <li class="breadcrumb-item">
                                                <a href="#"> <i class="fa fa-home"></i> </a>
                                            </li>
                                        </ul>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <!-- Page-header end -->
                        <div class="pcoded-inner-content">
                            <br>
                            <a href="add_product.php" class="btn btn-info mb-3">Add Product</a>
                            <a href="admin.php" class="btn btn-success mb-3">Back to Homepage</a>

                            <?php
                            // Database connection
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "motorbike";

                            // Create connection
                            $conn = new mysqli($servername, $username, $password, $dbname);

                            // Check connection
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // Handling form submission
                            if (isset($_POST['submit'])) {
                                $product_id = $_POST['product_id'];
                                $product_name = $_POST['product_name'];
                                $product_type_id = $_POST['product_type_id'];
                                // $product_description = $_POST['product_description'];
                                $product_price = $_POST['product_price'];
                                $product_img = $_POST['product_img'];
                                $producer_id = $_POST['producer_id'];
                                $quantity = $_POST['quantity'];

                                // Check if the product already exists
                                $sql = "SELECT * FROM product WHERE product_id = '$product_id'";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // If the product exists, update it
                                    $sql = "UPDATE product SET 
                                                        product_name='$product_name', 
                                                        product_type_id='$product_type_id', 
                                                        product_price='$product_price', 
                                                        product_img='$product_img', 
                                                        quantity='$quantity' 
                                                        WHERE product_id='$product_id'";
                                    if ($conn->query($sql) === TRUE) {
                                        echo "Product updated successfully";
                                    } else {
                                        echo "Error updating product: " . $conn->error;
                                    }
                                } else {
                                    // If the product does not exist, insert it
                                    $sql = "INSERT INTO product (product_name, product_type_id, product_price, product_img, quantity) VALUES ( '$product_name', '$product_type_id', '$product_price', '$product_img', '$quantity')";
                                    if ($conn->query($sql) === TRUE) {
                                        echo "Product added successfully";
                                    } else {
                                        echo "Error adding product:" . $conn->error;
                                    }
                                }
                            }
                            // Display all products
                            $sql = "SELECT * FROM product";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                echo "<table class='table table-striped'>";
                                echo "<tr><th>Product Name</th><th>Product Type ID</th><th>Product Price</th><th>Product Image</th><th>Quantity</th><th>Action</th></tr>";
                                while ($row = $result->fetch_assoc()) {
                                    // Construct the image path
                                    $img_path = 'uploads/' . $row['product_img'];
                                    // Check if the image file exists
                                    if (file_exists($img_path)) {
                                        $img_url = $img_path;
                                    } else {
                                        $img_url = 'path/to/default-image.jpg'; // Path to a default image if product image not found
                                    }
                                    echo "<tr>";
                                    echo "<td>" . $row['product_name'] . "</td>";
                                    echo "<td>" . $row['product_type_id'] . "</td>";
                                    echo "<td>" . $row['product_price'] . "</td>";
                                    echo "<td><img src='" . $img_url . "' alt='Product Image' style='width:100px; height:auto;'></td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    echo "<td>
                                            <a href='add_product.php?edit=" . $row['product_id'] . "' class='btn btn-primary'>Edit</a>
                                            <a href='delete_product.php?delete=" . $row['product_id'] . "' class='btn btn-danger'>Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                            } else {
                                echo "No products found";
                            }

                            $conn->close();
                            ?>
                        </div>
                        <!-- [ Main Content ] end -->
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
    <script src="assets/pages/waves/js/waves.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery-slimscroll/jquery.slimscroll.js "></script>
    <script type="text/javascript" src="assets/js/modernizr/modernizr.js "></script>
    <script type="text/javascript" src="assets/js/SmoothScroll.js"></script>
    <script src="assets/js/jquery.mCustomScrollbar.concat.min.js "></script>
    <script type="text/javascript" src="assets/js/chart.js/Chart.js"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="assets/pages/widget/amchart/gauge.js"></script>
    <script src="assets/pages/widget/amchart/serial.js"></script>
    <script src="assets/pages/widget/amchart/light.js"></script>
    <script src="assets/pages/widget/amchart/pie.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/vertical-layout.min.js "></script>
    <script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.js"></script>
    <script type="text/javascript" src="assets/js/script.js "></script>

    <script type="text/javascript">
    // Sử dụng window.onload để đảm bảo tất cả tài nguyên đã được tải
    $(window).on('load', function() {
        console.log('Admin page fully loaded, attempting to hide preloader.'); // Thêm log để debug
        $('.theme-loader').fadeOut('slow', function() {
            $(this).remove(); // Xóa phần tử preloader khỏi DOM sau khi ẩn
        });
    });

    // Nếu cách trên không hoạt động, có thể thử document.ready với setTimeout ngắn
    // $(document).ready(function() {
    //     setTimeout(function() {
    //         console.log('Admin page ready, hiding preloader with a slight delay.');
    //         $('.theme-loader').fadeOut('slow', function() {
    //             $(this).remove();
    //         });
    //     }, 300); // 300ms delay
    // });
    </script>
</body>

</html>