<?php
include 'connect.php'; // Include the database connection file
session_start();

// Initialize the PDO connection
$conn = new Connect();
$db_link = $conn->connectToPDO();

// Get the product ID from the URL parameter
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Query to retrieve the product details, including product_type_id
$query = "SELECT p.*, pt.product_type_name
          FROM product p
          LEFT JOIN product_type pt ON p.product_type_id = pt.product_type_id
          WHERE p.product_id = ?";
$stmt = $db_link->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Query to retrieve the product images (keeping your existing logic)
$product_images = [];
if ($product && !empty($product['product_img'])) {
    $product_images[] = ['product_img' => $product['product_img']];
    // If you have a separate table for multiple images per product, you'd fetch from there:
    // $query_gallery = "SELECT image_path FROM product_gallery WHERE product_id = ?";
    // $stmt_gallery = $db_link->prepare($query_gallery);
    // $stmt_gallery->execute([$product_id]);
    // $product_images = $stmt_gallery->fetchAll(PDO::FETCH_ASSOC);
}

// Query to retrieve the product video URL
$query_video = "SELECT product_video_url FROM product WHERE product_id = ?";
$stmt_video = $db_link->prepare($query_video);
$stmt_video->execute([$product_id]);
$product_video_url = $stmt_video->fetchColumn();

// Convert video URL to embed format if it's a YouTube URL
if (!empty($product_video_url) && strpos($product_video_url, 'watch?v=') !== false) {
    $product_video_url = str_replace('watch?v=', 'embed/', $product_video_url);
}

// Query to retrieve feedback for the product
$query_feedback = "
    SELECT f.*, c.customer_name
    FROM feedback f
    JOIN customer c ON f.customer_id = c.customer_id
    WHERE f.product_id = ?
    ORDER BY f.feedback_id DESC
";
$stmt_feedback = $db_link->prepare($query_feedback);
$stmt_feedback->execute([$product_id]);
$feedbacks = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product details | TD Motor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/ajax/libs/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/detail.css">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">

    <style>
    /* General styles, keeping existing ones */
    p {
        font-size: 20px;
        color: #333;
        font-weight: 200px;
    }

    h2 {
        font-size: 30px;
    }

    .alert-info {
        background-color:#bce8f1;
        color: #f40000;
        border-color: #bce8f1;
        text-align: center;
    }

    /* New/Modified styles to match the reference image */
    .product-main-section {
        display: flex;
        flex-wrap: wrap;
        /* Allows columns to wrap on smaller screens */
        margin-top: 30px;
        /* Space from header */
    }

    .product-image-column {
        flex: 0 0 50%;
        /* Takes 50% width on desktop */
        max-width: 50%;
        padding-right: 30px;
        /* Space between image and details */
        text-align: center;
        /* Center the image */
    }

    .product-info-column {
        flex: 0 0 50%;
        /* Takes 50% width on desktop */
        max-width: 50%;
        padding-left: 30px;
        /* Space between image and details */
    }

    .product-main-image img {
        max-width: 100%;
        height: auto;
        display: block;
        /* Remove extra space below image */
        margin: 0 auto;
        /* Center image horizontally */
    }

    .product-thumbnails {
        display: flex;
        justify-content: center;
        /* Center thumbnails */
        gap: 10px;
        /* Space between thumbnails */
        margin-top: 20px;
    }

    .product-thumbnail img {
        width: 80px;
        /* Fixed width for thumbnails */
        height: 80px;
        /* Fixed height for thumbnails */
        object-fit: cover;
        /* Cover the area, may crop */
        border: 1px solid #ddd;
        cursor: pointer;
        transition: border-color 0.3s;
    }

    .product-thumbnail img:hover,
    .product-thumbnail img.active {
        border-color: #f40000;
        /* Highlight active thumbnail */
    }

    /* Changed style for product info lines */
    .product-detail-line {
        /* New class for Brand, Type, Status, Hotline */
        font-size: 16px;
        color: #666;
        margin-bottom: 5px;
    }

    .product-detail-line span {
        font-weight: bold;
        color: #333;
    }

    .product-detail-line.brand span {
        color: #f40000;
        /* Red color for brand */
    }

    .product-detail-line.status span {
        color: <?=($product && $product['quantity'] > 0) ? 'green': 'red'?>;
        /* Dynamic color for status */
    }


    .product-name {
        font-size: 32px;
        /* Larger font size for product name */
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .product-price {
        font-size: 28px;
        /* Larger font size for price */
        font-weight: bold;
        color: #f40000;
        /* Red color for price */
        margin-bottom: 20px;
    }

    .social-buttons {
        margin-top: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        /* Space between buttons */
    }

    /* New style for Add to Order button */
    .btn-add-to-order-custom {
        background-color: #007bff;
        /* Blue */
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 15px 30px;
        /* Tăng padding để nút lớn hơn */
        font-size: 15px;
        /* Tăng kích thước chữ */
        transition: background-color 0.3s ease;
        margin-bottom: 20px;
        /* Space below button */
        /* display: block; */
        /* Uncomment this if you want the button to take full width on desktop too */
        /* width: auto; */
        /* Reset width if you want it to be content-based */
    }

    .btn-add-to-order-custom:hover {
        background-color: red;
        /* Darker blue on hover */
        color: whitesmoke;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-add-to-order-custom {
            width: 100%;
            /* Make button full width on mobile */
            padding: 12px 0;
            /* Điều chỉnh padding cho mobile */
            font-size: 16px;
            /* Điều chỉnh kích thước chữ cho mobile */
        }
    }

    .btn-action-group {
        display: flex;
        gap: 10px;
        /* Space between action buttons */
        margin-top: 30px;
        flex-wrap: wrap;
        /* Allow buttons to wrap */
    }

    .btn-action-group .btn {
        background-color: #eee;
        color: #333;
        border: 1px solid #ccc;
        padding: 10px 20px;
        border-radius: 5px;
        text-transform: uppercase;
        font-weight: bold;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-action-group .btn.active,
    .btn-action-group .btn:hover {
        background-color: #f40000;
        /* Red active/hover */
        color: #fff;
        border-color: #f40000;
    }

    /* Video and Feedback sections */
    .product-description-section,
    .product-video-section,
    .feedback-section {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
        /* Separator */
    }

    .embed-responsive {
        position: relative;
        display: block;
        width: 100%;
        padding: 0;
        overflow: hidden;
    }

    .embed-responsive::before {
        content: "";
        display: block;
        padding-top: 56.25%;
        /* 16:9 aspect ratio */
    }

    .embed-responsive iframe {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {

        /* Adjust for smaller desktops/tablets */
        .product-image-column,
        .product-info-column {
            flex: 0 0 100%;
            /* Stack columns */
            max-width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }

        .product-image-column {
            margin-bottom: 30px;
            /* Space between image and info */
        }

        .product-info-column {
            text-align: center;
            /* Center product info on mobile */
        }

        .social-buttons,
        .btn-action-group {
            justify-content: center;
            /* Center align buttons */
        }
    }

    @media (max-width: 768px) {
        .product-name {
            font-size: 28px;
        }

        .product-price {
            font-size: 24px;
        }

        p {
            /* For product description */
            font-size: 16px;
        }

        h2 {
            /* For section titles */
            font-size: 24px;
        }

        .product-thumbnails {
            flex-wrap: wrap;
            /* Allow thumbnails to wrap */
        }

        .btn-add-to-order-custom {
            width: 100%;
            /* Make button full width on mobile */
        }
    }

    @media (max-width: 576px) {
        .product-main-image img {
            max-height: 250px;
            /* Limit height of main image on very small screens */
            width: auto;
            /* Allow width to adjust */
        }

        .product-thumbnail img {
            width: 60px;
            height: 60px;
        }

        .btn-action-group .btn {
            width: 100%;
            /* Make action buttons full width */
            margin-bottom: 10px;
            /* Space between full-width buttons */
        }
    }

    /* CSS for separating body from header */
    body {
        padding-top: 100px;
        /* Adjust this value based on your header's height */
    }

    @media (max-width: 992px) {
        body {
            padding-top: 80px;
            /* Smaller padding for tablets */
        }
    }

    @media (max-width: 768px) {
        body {
            padding-top: 70px;
            /* Even smaller padding for mobile */
        }
    }
    </style>
</head>

<body>
    <?php include_once 'header.php'; ?>

    <div class="container">
        <?php if ($product) { ?>
        <div class="product-main-section">
            <div class="product-image-column">
                <div class="product-main-image">
                    <img src="<?= 'uploads/' . htmlspecialchars($product['product_img']) ?>"
                        alt="<?= htmlspecialchars($product['product_name']) ?>">
                </div>
                <div class="product-thumbnails">
                    <?php foreach ($product_images as $key => $image) { ?>
                    <div class="product-thumbnail">
                        <img src="<?= 'uploads/' . htmlspecialchars($image['product_img']) ?>"
                            alt="Thumbnail <?= $key + 1 ?>" class="<?= $key === 0 ? 'active' : '' ?>"
                            onclick="changeMainImage('<?= 'uploads/' . htmlspecialchars($image['product_img']) ?>', this)">
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="product-info-column">                
                <h1 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h1>
                <div class="product-detail-line brand">Brand:
                    <span><?= htmlspecialchars($product['product_type_name'] ?? 'N/A') ?></span>
                </div>

                <div class="product-rating-display" style="color: yellow;">
                    <i class="fas fa-star"></i> <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div> <strong class="rating-text">(4.5/5)</strong>

                <div class="product-detail-line status">Status: <span>
                        <?php
                    if ($product['quantity'] > 0) {
                        echo '(' . htmlspecialchars($product['quantity']) . ') In Stock';
                    } else {
                        echo 'Out of Stock';
                    }
                    ?>
                    </span></div>
                <div class="product-price"><?= number_format($product['product_price']) ?>$</div>

                <?php if (isset($_SESSION['customer_id'])) : ?>
                <form method="POST" action="cart.php" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>">
                    <input type="hidden" name="product_price"
                        value="<?= htmlspecialchars($product['product_price']) ?>">
                    <input type="hidden" name="product_img" value="<?= htmlspecialchars($product['product_img']) ?>">
                    <button type="submit" name="add_to_cart" class="btn-add-to-order-custom">Add to Order</button>
                </form>
                <?php else : ?>
                <p class="alert-info">Please <a href="loginnew.php">login</a> to add this product to your cart.</p>
                <?php endif; ?>

                <div class="btn-action-group">
                    <button class="btn active" id="btn-description">PRODUCT DESCRIPTION</button>
                    <button class="btn" id="btn-installation">VIDEO</button>
                    <button class="btn" id="btn-comment">FEEDBACK</button>
                </div>
            </div>
        </div>

        <div class="product-description-section" id="product-description-content">
            <h2>Product Description</h2>
            <p><?= htmlspecialchars($product['product_description']) ?></p>
        </div>

        <div class="product-video-section" id="product-video-content">
            <h2>Cinematic video</h2>
            <?php if (!empty($product_video_url)) { ?>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe src="<?= htmlspecialchars($product_video_url) ?>" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen class="embed-responsive-item"></iframe>
            </div>
            <?php } else { ?>
            <p class="mt-4">No video available.</p>
            <?php } ?>
        </div>

        <div class="feedback-section" id="product-comment-content">
            <h2>Feedback</h2>
            <?php if (!empty($feedbacks)) { ?>
            <div class="list-group">
                <?php foreach ($feedbacks as $feedback) { ?>
                <div class="list-group-item">
                    <p class="mb-1"><?= nl2br(htmlspecialchars($feedback['content'])) ?></p>
                    <small>By Customer Name: <?= htmlspecialchars($feedback['customer_name']) ?></small>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <p>No feedback.</p>
            <?php } ?>
        </div>

        <?php } else { ?>
        <p>Product details not found.</p>
        <?php } ?>
    </div>
    <br><br>

</body>
<br><br><br>
<?php include_once 'footer.php'; ?>

<script>
// JavaScript to change main image when thumbnail is clicked
function changeMainImage(src, clickedThumbnail) {
    document.querySelector('.product-main-image img').src = src;

    // Remove active class from all thumbnails
    document.querySelectorAll('.product-thumbnail img').forEach(img => {
        img.classList.remove('active');
    });
    // Add active class to the clicked thumbnail
    clickedThumbnail.classList.add('active');
}

// JavaScript for tab-like behavior for action buttons
document.addEventListener('DOMContentLoaded', function() {
    const descriptionBtn = document.getElementById('btn-description');
    const installationBtn = document.getElementById('btn-installation');
    const commentBtn = document.getElementById('btn-comment');

    const descriptionContent = document.getElementById('product-description-content');
    const installationContent = document.getElementById(
        'product-video-content'); // Assuming video is installation content
    const commentContent = document.getElementById('product-comment-content');

    function showSection(sectionToShow, activeBtn) {
        // Hide all content sections
        descriptionContent.style.display = 'none';
        installationContent.style.display = 'none';
        commentContent.style.display = 'none';

        // Remove active class from all buttons
        descriptionBtn.classList.remove('active');
        installationBtn.classList.remove('active');
        commentBtn.classList.remove('active');

        // Show the selected section and activate the button
        sectionToShow.style.display = 'block';
        activeBtn.classList.add('active');
    }

    // Initial state: show description by default
    showSection(descriptionContent, descriptionBtn);

    descriptionBtn.addEventListener('click', function() {
        showSection(descriptionContent, descriptionBtn);
    });

    installationBtn.addEventListener('click', function() {
        // If "Installation Guide" means the video, show the video section
        showSection(installationContent, installationBtn);
    });

    commentBtn.addEventListener('click', function() {
        showSection(commentContent, commentBtn);
    });
});
</script>

</html>