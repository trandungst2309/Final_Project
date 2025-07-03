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

// --- START: PHP for Comment Handling ---

$comment_success_message = '';
$comment_error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    if (isset($_SESSION['customer_id']) && $_SESSION['role'] === 'customer') {
        $customer_id = $_SESSION['customer_id'];
        $comment_content = trim($_POST['comment_content']);

        if (!empty($comment_content) && $product_id > 0) {
            try {
                $insert_comment_query = "INSERT INTO `comment` (product_id, customer_id, comment_content) VALUES (?, ?, ?)";
                $stmt_insert_comment = $db_link->prepare($insert_comment_query);
                $stmt_insert_comment->execute([$product_id, $customer_id, $comment_content]);
                $comment_success_message = "Your comment has been added successfully!";
                // Optionally, redirect to clear POST data and prevent re-submission on refresh
                // header("Location: product_detail.php?product_id=" . $product_id);
                // exit();
            } catch (PDOException $e) {
                $comment_error_message = "Error submitting comment: " . $e->getMessage();
            }
        } else {
            $comment_error_message = "Comment cannot be empty.";
        }
    } else {
        $comment_error_message = "You must be logged in as a customer to post a comment.";
    }
}

// Query to retrieve COMMENTS for the product WITH AVATAR (using profile_image column)
// and also select customer_id from comment table for deletion check
$query_comments = "
    SELECT co.*, cu.customer_name, cu.profile_image
    FROM `comment` co
    JOIN customer cu ON co.customer_id = cu.customer_id
    WHERE co.product_id = ?
    ORDER BY co.comment_date DESC
";
$stmt_comments = $db_link->prepare($query_comments);
$stmt_comments->execute([$product_id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// --- END: PHP for Comment Handling ---


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

// Query to retrieve feedback for the product (your existing feedback query)
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
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
        background-color:darkgrey;
        color: #f40000;
        border-color: #bce8f1;
        text-align: center;
        border-radius: 10px;
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
    .feedback-section,
    .comments-section { /* Added comments-section */
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
        /* Separator */
    }

    .product-video-section {
        max-width: 80%;
        /* Ensure video section is responsive */
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

    /* --- NEW COMMENT SECTION STYLES --- */
    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

    .comment-item {
    display: flex;
    align-items: stretch;
    padding: 10px;
    /* border: 1px solid #333; */
    margin-bottom: 15px;
    border-radius: 5px;
    width: 100%;
    box-sizing: border-box;
    overflow: visible;
    min-height: 70px;
    }

    .comment-item:last-child {
        border-bottom: none; /* No bottom border for the last comment if you want a continuous list look */
    }

    .comment-info {
        flex-grow: 1; /* Allows comment content to take available space */
    }

    .comment-author {
        font-weight: bold;
        margin-bottom: 5px;
        display: flex;
        align-items: center; /* Vertically align name and date */
    }

    .comment-date {
        color: #777;
        font-size: small;
        margin-left: 8px; /* Space between name and date */
    }

    .comment-content {
        margin-bottom: 0;
    }
    .comment-actions {
        margin-left: auto; /* Pushes actions to the right */
    }

    .comment-container {
        /* max-height: 500px; Limit height of comment section */
        overflow-y: auto; /* Enable scrolling if content exceeds height */
        margin-top: 20px; /* Space above comment section */
        border: 1px solid #333;
        border-radius: 5px;
    }
    /* --- END NEW COMMENT SECTION STYLES --- */
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

                <?php
                // Kiểm tra xem người dùng đã đăng nhập và có role là "customer" hay không
                if (isset($_SESSION['customer_id']) && $_SESSION['role'] === 'customer') :
                ?>
                    <form method="POST" action="cart.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>">
                        <input type="hidden" name="product_price"
                            value="<?= htmlspecialchars($product['product_price']) ?>">
                        <input type="hidden" name="product_img" value="<?= htmlspecialchars($product['product_img']) ?>">
                        <button type="submit" name="add_to_cart" class="btn-add-to-order-custom">Add to Order</button>
                    </form>
                <?php else : ?>
                    <p class="alert-info">Please <a href="login.php">login</a> with a customer account to add this product to your cart!</p>
                <?php endif; ?>

                <div class="btn-action-group">
                    <button class="btn active" id="btn-description">PRODUCT DESCRIPTION</button>
                    <button class="btn" id="btn-video">VIDEO</button>
                    <button class="btn" id="btn-feedback">FEEDBACK</button>
                    <button class="btn" id="btn-comments">COMMENTS</button>
                </div>
            </div>
        </div>

        <div class="product-description-section" id="product-description-content">
            <h2>Product Description</h2>
            <p><?= htmlspecialchars($product['product_description']) ?></p>
        </div>

        <div class="product-video-section" id="product-video-content" style="display: none;">
            <h2>Cinematic video</h2>
            <br>
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

        <div class="feedback-section" id="product-feedback-content" style="display: none;">
            <h2>Customer Feedback</h2>
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
            <p>No feedback available for this product.</p>
            <?php } ?>
        </div>

        <div class="comments-section" id="product-comments-content" style="display: none;">
            <h2>Product Comments</h2>

            <?php if (!empty($comment_success_message)): ?>
                <div class="alert alert-success mt-3" role="alert">
                    <?= htmlspecialchars($comment_success_message) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($comment_error_message)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?= htmlspecialchars($comment_error_message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['customer_id']) && $_SESSION['role'] === 'customer') : ?>
                <div class="card mt-4 mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Leave a Comment</h5>
                        <form action="product_detail.php?product_id=<?= $product_id ?>" method="POST">
                            <div class="mb-3">
                                <textarea class="form-control" name="comment_content" rows="3" placeholder="Write your comment here..." required></textarea>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                </div>
            <?php else : ?>
                <p class="alert alert-info mt-3">Please <a href="login.php">login</a> as a customer to post a comment.</p>
            <?php endif; ?>

            <h5 class="mt-5">All Comments (<span id="comment-count"><?= count($comments) ?></span>)</h5>
            <?php if (!empty($comments)) { ?>
                <div class="comment-container">
                    <?php foreach ($comments as $comment) { ?>
                        <div class="comment-item" id="comment-<?= htmlspecialchars($comment['comment_id']) ?>">
                            <?php if (!empty($comment['profile_image'])) { ?>
                                <img src="<?= 'uploads/' . htmlspecialchars($comment['profile_image']) ?>" alt="<?= htmlspecialchars($comment['customer_name']) ?>'s Avatar" class="comment-avatar">
                            <?php } else { ?>
                                <img src="image/default-avatar.png" alt="Default Avatar" class="comment-avatar">
                            <?php } ?>
                            <div class="comment-info">
                                <div class="comment-author">
                                    <?= htmlspecialchars($comment['customer_name']) ?>
                                    <small class="comment-date"><?= date('M d, Y H:i', strtotime($comment['comment_date'])) ?></small>
                                </div>
                                <p class="comment-content"><?= nl2br(htmlspecialchars($comment['comment_content'])) ?></p>
                            </div>
                            <div class="comment-actions">
                                <?php if (isset($_SESSION['customer_id']) && $_SESSION['customer_id'] == $comment['customer_id']) { ?>
                                    <button class="btn btn-sm btn-danger delete-comment-btn" data-comment-id="<?= htmlspecialchars($comment['comment_id']) ?>">Delete</button>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="mt-3" id="no-comments-message">No comments yet!</p>
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
    const videoBtn = document.getElementById('btn-video');
    const feedbackBtn = document.getElementById('btn-feedback');
    const commentsBtn = document.getElementById('btn-comments');

    const descriptionContent = document.getElementById('product-description-content');
    const videoContent = document.getElementById('product-video-content');
    const feedbackContent = document.getElementById('product-feedback-content');
    const commentsContent = document.getElementById('product-comments-content');
    const commentCountElement = document.getElementById('comment-count'); // Element to update comment count
    const noCommentsMessage = document.getElementById('no-comments-message'); // Element for no comments message

    function showSection(sectionToShow, activeBtn) {
        // Hide all content sections
        descriptionContent.style.display = 'none';
        videoContent.style.display = 'none';
        feedbackContent.style.display = 'none';
        commentsContent.style.display = 'none';

        // Remove active class from all buttons
        descriptionBtn.classList.remove('active');
        videoBtn.classList.remove('active');
        feedbackBtn.classList.remove('active');
        commentsBtn.classList.remove('active');

        // Show the selected section and activate the button
        sectionToShow.style.display = 'block';
        activeBtn.classList.add('active');
    }

    // Initial state: show description by default
    showSection(descriptionContent, descriptionBtn);

    descriptionBtn.addEventListener('click', function() {
        showSection(descriptionContent, descriptionBtn);
    });

    videoBtn.addEventListener('click', function() {
        showSection(videoContent, videoBtn);
    });

    feedbackBtn.addEventListener('click', function() {
        showSection(feedbackContent, feedbackBtn);
    });

    commentsBtn.addEventListener('click', function() {
        showSection(commentsContent, commentsBtn);
    });

    // --- JavaScript for Delete Comment (AJAX) ---
    // Use event delegation on a parent element for dynamically added buttons
    commentsContent.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-comment-btn')) {
            const commentId = event.target.dataset.commentId;
            const commentItem = event.target.closest('.comment-item'); // Get the parent .comment-item

            if (confirm("Are you sure you want to delete this comment?")) {
                fetch('delete_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'comment_id=' + commentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the comment from the DOM
                        if (commentItem) {
                            commentItem.remove();
                        }

                        // Update the comment count
                        if (commentCountElement) {
                            let currentCount = parseInt(commentCountElement.textContent);
                            commentCountElement.textContent = currentCount - 1;
                        }

                        // Show "No comments yet" message if count becomes 0
                        if (parseInt(commentCountElement.textContent) === 0 && noCommentsMessage) {
                            noCommentsMessage.style.display = 'block';
                        }
                    } else {
                        alert('Error deleting comment: ' + (data.message || 'Unknown error.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while trying to delete the comment.');
                });
            }
        }
    });
});
</script>

</html>