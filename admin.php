<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: loginnew.php');
    exit;
}
// Code for admin page
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard </title>
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
<style>
    .logo-container {
        text-align: center;
        /* Tùy chỉnh khoảng cách dưới logo */
    }

    .logo-img {
        max-width: 100%;
        /* Đảm bảo logo không vượt quá chiều rộng của phần tử chứa nó */
        height: 200px;
        /* Giữ tỷ lệ khung hình của logo */
        margin: 0 auto;
        /* Căn giữa logo */
        display: block; /* Đảm bảo hình ảnh là block để margin: auto hoạt động */
    }
</style>

<body>
    <div class="theme-loader">
        <div class="loader-track">
            <div class="preloader-wrapper">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">
                    <div class="navbar-logo">
                        <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
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
                                <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-nav-profile">
                                <a href="#!" class="waves-effect waves-light">
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
                                </a>
                                <ul class="show-notification profile-notification">
                                    <li class="waves-effect waves-light">
                                        <a href="profile_admin.php">
                                            <i class="ti-user"></i> Profile
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="logout.php">
                                            <i class="ti-layout-sidebar-left"></i> Logout
                                        </a>
                                    </li>
                                </ul>
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
                                    <img class="img-80 img-radius" src="assets/images/avatar-4.jpg" alt="User-Profile-Image"> <div class="user-details">
                                        <?php
                                        if (isset($_SESSION['customer_name'])) {
                                            echo '<span id="more-details">' . htmlspecialchars($_SESSION['customer_name']) . '<i class="ti-angle-down"></i></span>';
                                        } else {
                                            echo '<span id="more-details">Admin<i class="ti-angle-down"></i></span>';
                                        }
                                        ?>
                                    </div>
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
                                    <a href="#" class="waves-effect waves-dark">
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
                                        <li class="">
                                            <a href="manage_account.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Account Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a href="manage_order.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Order Management</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>
                            </ul>

                            <div class="pcoded-navigation-label" data-i18n="nav.category.forms"></div>
                            <ul class="pcoded-item pcoded-left-item">
                                <li>
                                    <a href="statistic.php" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Statistics management</span>
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
                                        <li class="">
                                            <a href="loginnew.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.alert">Login</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a href="register.php" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Register</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
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
                                                <li class="breadcrumb-item">
                                                    </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pcoded-inner-content">
                                <img src="image/TDicon.png" alt="Logo" class="logo-img">


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