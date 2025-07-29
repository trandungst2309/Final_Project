<?php
session_start();
include_once 'connect.php';
$conn = new Connect();
$db_link = $conn->connectToPDO();

// Kiểm tra trạng thái đăng nhập
$is_logged_in = isset($_SESSION['customer_id']) && $_SESSION['role'] === 'customer';

// Khởi tạo các biến mặc định để tránh lỗi Undefined variable
$products = [];
$bestsellers = [];
$total_products = 0; // Mặc định là 0 sản phẩm
$total_pages = 1;    // Mặc định 1 trang để tránh chia cho 0 hoặc lỗi hiển thị phân trang

// Kiểm tra nếu kết nối DB thành công
if ($db_link) {
    // Cài đặt phân trang
    $products_per_page = 9; // Số sản phẩm hiển thị trên mỗi trang
    $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $products_per_page;

    // Lấy tổng số sản phẩm để tính tổng số trang
    $total_products_query = "SELECT COUNT(*) FROM product";
    $stmt_total = $db_link->prepare($total_products_query);
    $stmt_total->execute();
    $total_products = $stmt_total->fetchColumn();

    // Tính tổng số trang, đảm bảo không chia cho 0
    if ($total_products > 0) {
        $total_pages = ceil($total_products / $products_per_page);
    } else {
        $total_pages = 1; // Nếu không có sản phẩm, vẫn là 1 trang (để tránh lỗi)
    }

    // Lấy sản phẩm cho trang hiện tại
    // Đảm bảo offset không âm
    $offset = max(0, $offset);
    $query = "SELECT * FROM product LIMIT :limit OFFSET :offset";
    $stmt = $db_link->prepare($query);
    $stmt->bindParam(':limit', $products_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách sản phẩm bán chạy dựa trên tổng số lượng từ bảng 'order'
    try {
        $stmt_bestsellers = $db_link->prepare("
            SELECT
                p.*,
                SUM(o.quantity) AS total_sold_quantity
            FROM
                product p
            JOIN
                `order` o ON p.product_id = o.product_id
            GROUP BY
                p.product_id
            ORDER BY
                total_sold_quantity DESC
            LIMIT 10
        ");
        $stmt_bestsellers->execute();
        $bestsellers = $stmt_bestsellers->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Ghi log lỗi thay vì hiển thị trực tiếp trên trang sản phẩm công khai
        error_log("Bestseller query failed: " . $e->getMessage());
        // Hiển thị thông báo lỗi thân thiện (chỉ trong môi trường dev)
        // echo "<p class='text-danger text-center'>Error loading bestsellers: " . $e->getMessage() . "</p>";
        $bestsellers = []; // Đảm bảo mảng rỗng để không bị lỗi undefined
    }

} else {
    // Xử lý trường hợp kết nối DB thất bại, có thể hiển thị thông báo lỗi hoặc log
    // echo "<p class='text-danger text-center'>Database connection failed. Please try again later.</p>";
    // Các biến $products, $bestsellers, $total_products, $total_pages đã được khởi tạo mặc định là rỗng/0/1
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TD Motor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">


    <style>
    /* Custom button styles */
    .button {
        display: inline-block;
        margin-top: 1rem;
        border-radius: 5rem;
        background: whitesmoke;
        color: #f40000;
        padding: .9rem 3.5rem;
        cursor: pointer;
        font-size: 2rem;
        text-decoration: none;
        font-weight: bold;
    }

    .button:hover {
        background: #333;
        color: #f40000;
    }

    @keyframes gradient {
        0% {
            color: #ff6f61;
        }

        50% {
            color: #6b5b95;
        }

        100% {
            color: #88d8b0;
        }
    }

    .animated-text {
        font-size: 2rem;
        font-weight: bold;
        animation: gradient 5s ease infinite;
        background: linear-gradient(90deg, #f40000, #1E1E24, #333);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    body h3 {
        font-family: 'Roboto', sans-serif;

    }

    .carousel-item img {
        height: 400px;
        /* Thay đổi chiều cao hình ảnh nếu cần */
        object-fit: cover;
        /* Đảm bảo hình ảnh không bị biến dạng */
    }

    .carousel-caption {
        background-color: rgba(0, 0, 0, 0.5);
        /* Nền mờ cho chú thích */
        color: white;
        /* Màu chữ */
    }

    .banner-title {
        font-family: 'Roboto', sans-serif;
        /* Font chữ tinh tế */
        font-size: 2.5rem;
        /* Kích thước chữ lớn hơn */
        font-weight: bold;
        /* Đậm chữ */
        color: #333;
        /* Màu chữ chính */
        text-align: center;
        /* Căn giữa */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        /* Hiệu ứng bóng chữ nhẹ */
        background: linear-gradient(to right, #ff7e5f, #feb47b);
        /* Gradient màu nền */
        -webkit-background-clip: text;
        /* Cắt nền theo chữ */
        -webkit-text-fill-color: transparent;
        /* Làm chữ trong suốt để nhìn thấy gradient */
        padding: 10px 0;
        /* Padding trên và dưới */
        border-radius: 5px;
        /* Bo góc cho tiêu đề */
        border: 2px solid #feb47b;
        /* Border màu tương phản */
    }

    #home {
        display: flex;
        align-items: center;
        min-height: 100vh;
        background: url(/image/hondacbr.jpg) no-repeat;
        /*Thay đổi hình nền head*/
        background-size: cover;
        background-position: center;
    }

    .text-danger {
        color: red;
        font-weight: bold;
        text-align: center;
    }

    .banner-title {
        font-family: 'Roboto', sans-serif;
        font-size: 2.5rem;
        font-weight: bold;
        color: red;
        /* Change to red */
        text-align: center;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        padding: 10px 0;
        border-radius: 5px;
        border: 2px solid red;
        /* Remove gradient and background-clip for solid color */
    }

    .slide-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 500px;
        padding: 0 40px;
    }

    .slide-image {
        width: 50%;
        height: 100%;
    }

    .slide-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .caption-group {
        width: 50%;
        padding: 20px;
    }

    .caption.title {
        font-size: 32px;
        color: #000;
    }

    .caption.subtitle {
        font-size: 18px;
        margin-top: 10px;
    }

    .caption.button-radius {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background: #ff4500;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .bx-wrapper .bx-pager.bx-default-pager a {
        background: grey;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        display: inline-block;
        margin: 0 5px;
        opacity: 0.5;
        transition: opacity 0.3s;
    }

    .bx-wrapper .bx-pager.bx-default-pager a.active,
    .bx-wrapper .bx-pager.bx-default-pager a:focus {
        opacity: 1;
        background: darkgrey;
    }

    /* --- Responsive Adjustments for Slider --- */
    @media (max-width: 768px) {
        .slide-container {
            flex-direction: column;
            /* Stack image and text vertically */
            height: 400px;
            /* Allow height to adjust based on content */
            padding: 20px;
            /* Adjust padding for smaller screens */
        }

        .slide-image {
            width: 100%;
            /* Image takes full width */
            height: 250px;
            /* Fixed height for image on mobile, adjust as needed */
            margin-bottom: 20px;
            /* Space between image and text */
        }

        .slide-image img {
            object-fit: contain;
            /* Ensure the whole image is visible, might add white space */
        }

        .caption-group {
            width: 100%;
            /* Text takes full width */
            padding: 0;
            /* Remove horizontal padding */
            text-align: center;
            /* Center align text */
        }

        .caption.title {
            font-size: 24px;
            /* Reduce font size for title */
            word-wrap: break-word;
            /* Allow long words to break */
            white-space: normal;
            /* Allow text to wrap naturally */
        }

        .caption.subtitle {
            font-size: 16px;
            /* Reduce font size for subtitle */
        }

        /* Adjust BXSlider pager position if needed for mobile */
        .bx-wrapper .bx-pager {
            position: static;
            /* Remove absolute positioning if it causes issues */
            margin-top: 20px;
            /* Add some space above the pager */
        }
    }

    @media (max-width: 480px) {
        .caption.title {
            font-size: 20px;
            /* Further reduce font size for very small screens */
        }

        .caption.subtitle {
            font-size: 14px;
            /* Further reduce font size for very small screens */
        }
    }

    /* Custom styles for product card buttons */
    .btn-add-to-cart,
    .btn-view-details {
        padding: 8px 15px;
        font-size: 14px;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-add-to-cart {
        background-color: #dc3545;
        /* Red */
        color: white;
        border: 1px solid #dc3545;
    }

    .btn-add-to-cart:hover {
        background-color: #c82333;
        color: white;
    }

    .btn-view-details {
        background-color: #007bff;
        /* Blue */
        color: white;
        border: 1px solid #007bff;
    }

    .btn-view-details:hover {
        background-color: #0056b3;
        color: white;
    }

    .product-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #333;
    }

    .product-price {
        font-size: 1.1rem;
        color: #e44d26;
        /* Orange-red for price */
        font-weight: bold;
    }

    /* Custom styles for pagination */
    .pagination .page-item .page-link {
        color: #dc3545;
        /* Màu chữ mặc định cho link */
        background-color: #fff;
        border: 1px solid #dee2e6;
        margin: 0 3px;
        /* Khoảng cách giữa các nút */
        border-radius: .25rem;
        /* Bo góc nhẹ */
        transition: all 0.3s ease;
    }

    .pagination .page-item .page-link:hover {
        color: #fff;
        /* Màu chữ khi hover */
        background-color: #dc3545;
        /* Màu nền khi hover (đỏ) */
        border-color: #dc3545;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        /* Thêm bóng nhẹ */
    }

    .pagination .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #f40000;
        /* Màu đỏ đậm cho trang hiện tại */
        border-color: #f40000;
        font-weight: bold;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        /* Bóng mạnh hơn cho trang active */
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        /* Màu xám cho nút disabled */
        pointer-events: none;
        /* Vô hiệu hóa click */
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    /* Optional: Adjust pagination size */
    .pagination-lg .page-link {
        padding: .75rem 1.5rem;
        font-size: 1.25rem;
    }
    .pagination-sm .page-link {
        padding: .25rem .5rem;
        font-size: .875rem;
    }

    /* Additional styling for the bestsellers slider */
    .bestseller-slide-item {
        text-align: center;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin: 0 10px; /* Space between items in the slider */
        height: 100%; /* Ensure all cards have same height */
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Distribute space vertically */
    }

    .bestseller-slide-item img {
        max-height: 180px;
        object-fit: contain;
        margin-bottom: 15px;
        width: 100%; /* Ensure image takes full width of its container */
    }

    /* Styling for product name in bestseller slider */
    .bestseller-slide-item .card-title {
        font-weight: bold;
        color: blue;
        font-size: 1.1rem; /* Adjust font size if needed */
    }

    /* Center align pager dots */
    .bx-pager {
        text-align: center;
        padding-top: 10px;
    }

    .bx-wrapper{
        max-width: 100% !important; /* Ensure slider does not exceed container width */
    }

    /* Red color for Best-Selling Motorbikes title */
    .bestseller-title {
        color: red;
        font-weight: bold; /* Make sure it's bold */
    }

    /* CSS đã thêm ở lần trước để tăng z-index */
    .bestseller-slide-item .btn-primary {
        position: relative;
        z-index: 100;
    }

    </style>
</head>

<body>
    <?php include_once 'header.php'; ?>
    <br><br>
    <section class="home" id="home">
        <div class="content">
            <h3 class="animated-text" style="padding-top: 4rem;">TD Motor — where your journey begins</h3>
            <span>Welcome to TD Motor – where you can find the top-of-the-line motorcycles at the most
                competitive prices. Here, you can easily find your dream motorcycle in our diverse
                collection, from powerful sports cars to other trendy models. TD Motor is committed
                to bringing you a great shopping experience with professional consulting services
                and reputable warranty policies.</span>
            <p class="animated-text">Ignite your passion</p>
            <a href="#product" id="shopNowButton" class="button">RIDE NOW</a>
        </div>
    </section>

    <div class="block-slider block-slider4 my-5">
        <h2 class="text-center mb-4" style="color: red;">Newest Products</h2>
        <ul id="bxslider-home4">
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/CBR1000RR-R.png" alt="Slide">
                    </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">Honda CBR 1000RR-R
                                <strong>Fireblade</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">2025</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/CBR10th6.png" alt="Slide">
                    </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">Honda CBR 1000RR-R <strong>10th
                                        Anniversary</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">Honda CBR 10th
                            Anniversary Edition</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/RC213V" alt="Slide"> </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">Honda RC213V
                                <strong>Repsol</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">Honda Repsol Racing</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/YamahaR1M" alt="Slide"> </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">Yamaha R1M <strong>Carbon</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">2025</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/BMWS1000RR" alt="Slide"> </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">BMW S1000RR <strong>M Performance</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">2025</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/DucatiV4" alt="Slide"> </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">Ducati Superleggera <strong>V4S</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">2025</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="uploads/KawasakiH2" alt="Slide"> </div>
                    <div class="caption-group">
                        <h2 class="caption title">
                            <span class="primary" style="color: blue;">Kawasaki Ninja H2R <strong>Carbon</strong></span>
                        </h2>
                        <h4 class="caption subtitle" style="color:green;">2025</h4>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <section id="product" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4" style="color: red;">List of Products</h2>
            <div class="row">
                <?php if (empty($products)) : ?>
                    <p class="text-center">No products found.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm">
                                <img style="width:auto; height: 300px;" src="<?= ('./uploads/') . htmlspecialchars($product['product_img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>" />
                                <div class="card-body text-center">
                                    <h5 class="card-title product-title"><?= ($product['product_name']) ?></h5>
                                    <p class="card-text product-price">Price: $<?= number_format($product['product_price'], 0, ',', '.') ?>
                                    </p>
                                    <form method="POST" action="cart.php" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?= ($product['product_id']) ?>">
                                        <input type="hidden" name="product_name" value="<?= ($product['product_name']) ?>">
                                        <input type="hidden" name="product_price" value="<?= ($product['product_price']) ?>">
                                        <input type="hidden" name="product_img" value="<?= ($product['product_img']) ?>">

                                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart" data-logged-in="<?= $is_logged_in ? 'true' : 'false' ?>">Add to Order</button>

                                        <a href="product_detail.php?product_id=<?= ($product['product_id']) ?>" class="btn btn-view-details">View Detail</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1) : ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= max(1, $current_page - 1) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?= ($i === $current_page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= min($total_pages, $current_page + 1) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </section>

    <div class="container mt-5">
        <h3 class="text-center mb-4 bestseller-title">🔥 Best-Selling Motorbikes</h3>
        <ul id="bestseller-slider">
            <?php if (empty($bestsellers)) : ?>
                <li>
                    <p class="text-center">No best-selling products found.</p>
                </li>
            <?php else : ?>
                <?php foreach ($bestsellers as $product) : ?>
                    <li>
                        <div class="bestseller-slide-item">
                            <img src="<?= './uploads/' . htmlspecialchars($product['product_img']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                            <h5 class="card-title"><strong><?= htmlspecialchars($product['product_name']) ?></strong></h5>
                            <p class="card-text text-muted">$<?= number_format($product['product_price']) ?></p>
                            <a href="product_detail.php?product_id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check all buttons with data-logged-in attribute
            document.querySelectorAll('button[data-logged-in]').forEach(function(button) {
                if (button.getAttribute('data-logged-in') === 'false') {
                    button.style.display = 'none'; // Hide the button if not logged in
                }
            });
        });

        $(document).ready(function() {
            var slider = $('#bxslider-home4').bxSlider({
                auto: true,
                pause: 5000,
                speed: 1000,
                mode: 'horizontal',
                slideMargin: 10,
                touchEnabled: true,
                easing: 'ease-in-out',

                // Khi người dùng điều khiển slide, reset lại thời gian autoplay
                onSlideBefore: function($slideElement, oldIndex, newIndex) {
                    slider.stopAuto(); // Dừng tự động
                    slider.startAuto(); // Khởi động lại (reset thời gian)
                }
            });

            // Initialize Bestseller Slider
            var bestsellerSlider = $('#bestseller-slider').bxSlider({
                auto: true,
                pause: 4000, // Slightly faster autoplay for bestsellers
                speed: 800,
                minSlides: 1,
                maxSlides: 3, // Show 3 bestsellers at a time
                slideWidth: 300, // Adjust based on your card width
                slideMargin: 20,
                pager: true, // Show pagination dots
                controls: true, // Show next/prev buttons
                moveSlides: 1, // Move 1 slide at a time
                adaptiveHeight: true, // Adjust slider height based on current slide
                keyPress: false,
                // ----- ĐIỀU CHỈNH TẠI ĐÂY THEO GIẢI PHÁP 2 -----
                touchEnabled: false,      // Vô hiệu hóa hoàn toàn các sự kiện chạm/kéo của slider.
                                         // Đây là tùy chọn quan trọng nhất để thử.
                preventDefaultSwipeX: false, // Ngăn BXSlider chặn hành vi mặc định của trình duyệt cho kéo ngang
                // ----- HẾT ĐIỀU CHỈNH -----
                onSliderLoad: function() {
                    // Adjust slide width and margin on load to ensure proper display
                    // This might be needed if initial rendering is off
                    $('#bestseller-slider .bestseller-slide-item').css({
                        'width': 'auto', // Reset width to allow flexbox/bxslider to calculate
                        'margin-left': 'auto',
                        'margin-right': 'auto'
                    });
                }
            });

            // Giữ nguyên đoạn mã này, nó rất quan trọng để ngăn sự kiện click lan truyền lên slider chính
            $('#bestseller-slider').on('click', 'a.btn-primary', function(e) {
                e.stopPropagation(); // Ngăn chặn sự kiện click lan truyền lên slider
            });
        });
    </script>
</body>

</html>