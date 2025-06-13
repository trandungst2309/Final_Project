<link rel="icon" href="image/TDicon.png" type="image/x-icon">

<?php
session_start();
include_once 'connect.php';
$conn = new Connect();
$db_link = $conn->connectToPDO();

// Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch customer information
$query = "SELECT * FROM customer WHERE customer_id = :customer_id";
$stmt = $db_link->prepare($query);
$stmt->execute([':customer_id' => $customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer not found.";
    exit();
}

// Fetch product information if available
$product_info = null;
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Fetch product information
    $product_query = "SELECT product_name, product_price, product_img
                      FROM product
                      WHERE product_id = :product_id";
    $product_stmt = $db_link->prepare($product_query);
    $product_stmt->execute([':product_id' => $product_id]);
    $product_info = $product_stmt->fetch(PDO::FETCH_ASSOC);
}

?>
<br><br><br><br><br><br><br>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .btn_edit {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 1px 2px;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        transition: background-color 0.3s, transform 0.3s;
        cursor: pointer;
        display: inline-block;
        text-align: center;
        margin-left: 1px;
    }

    .btn_edit:hover {
        background-color: whitesmoke;
        transform: translateY(-2px);
        color: #dc3545;
    }

    .btn_change_pass {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 1px 2px;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        transition: background-color 0.3s, transform 0.3s;
        cursor: pointer;
        display: inline-block;
        text-align: center;
        margin-right: 1px;
    }

    .btn_change_pass:hover {
        background-color: whitesmoke;
        transform: translateY(-2px);
        color: #dc3545;
    }

    .profile-header {
        background: #f8f9fa;
        padding: 2rem 0;
        text-align: center;
        border-bottom: 1px solid #dee2e6;
    }

    .profile-header img {
        border-radius: 50%;
        border: 2px solid #dc3545;
        width: 120px;
        height: 120px;
        object-fit: cover;
    }

    .profile-card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-card .card-header {
        background-color: #dc3545;
        color: white;
    }

    .profile-card .card-body {
        padding: 2rem;
    }

    .profile-card .card-body p {
        margin-bottom: 1rem;
        font-size: medium;
    }
    </style>
</head>

<body>
    <?php include_once 'header.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-4" style="color: red; font-weight: bold;">Account Profile</h1>
        <div class="profile-header">
            <?php if (!empty($customer['profile_image'])): ?>
            <!-- Nếu có ảnh đại diện -->
            <img src="uploads/<?= htmlspecialchars($customer['profile_image']) ?>" alt="Profile Image"
                class="rounded-circle" width="30" height="30">
            <?php else: ?>
            <!-- Nếu không có ảnh, hiển thị icon người dùng -->
            <i class="bi bi-person-circle fs-4 text-white"></i>
            <?php endif; ?>

            <h2 class="mt-3"><?= htmlspecialchars($customer['customer_name']) ?></h2>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card profile-card">
                    <div class="card-header text-center">
                        <h4>Profile Detail</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($customer['address']) ?></p>

                        <!-- Change Password Button -->
                        <button type="button" class="btn_change_pass"
                            onclick="window.location.href='update_password.php'">Change Password</button>

                        <!-- Edit Profile Button -->
                        <button type="button" class="btn_edit" onclick="window.location.href='update_profile.php'">Edit
                            Profile</button>

                        <!-- History Button -->
                        <button type="button" class="btn_edit" onclick="window.location.href='order_detail.php'">Order
                            History</button>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display product information if available -->
        <?php if ($product_info): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card profile-card">
                    <div class="card-header text-center">
                        <h4>Product Information</h4>
                    </div>
                    <div class="card-body">
                        <img src="<?= !empty($product_info['product_img']) ? './uploads/' . htmlspecialchars($product_info['product_img']) : './uploads/products/default-product.png' ?>"
                            class="card-img-top" alt="Product Image">
                        <h5 class="card-title"><?= htmlspecialchars($product_info['product_name']) ?></h5>
                        <p class="card-text">Price: $<?= htmlspecialchars($product_info['product_price']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>


    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                value="<?= htmlspecialchars($customer['customer_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($customer['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?= htmlspecialchars($customer['phone']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="<?= htmlspecialchars($customer['address']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty($alertMessage)): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?= addslashes($alertMessage) ?>',
        timer: 3000,
        showConfirmButton: false
    });
    </script>
    <?php endif; ?>
</body>

</html>