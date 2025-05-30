<link rel="icon" href="image/TDicon.png" type="image/x-icon">

<?php
session_start();
include 'connect.php';

// Đảm bảo khách hàng đã đăng nhập
if (!isset($_SESSION['customer_id']) || !isset($_GET['product_id'])) {
    header('Location: homepage.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$product_id = $_GET['product_id'];

// Khởi tạo kết nối
$conn = new Connect();
$db_link = $conn->connectToPDO();

// Lấy thông tin khách hàng
$query = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = $db_link->prepare($query);
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Lấy thông tin sản phẩm
$query = "SELECT * FROM product WHERE product_id = ?";
$stmt = $db_link->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Lưu thông tin vào session
$_SESSION['product_id'] = $product['product_id'];
$_SESSION['product_name'] = $product['product_name'];
$_SESSION['product_price'] = $product['product_price'];
$_SESSION['customer_name'] = $customer['customer_name'];
$_SESSION['phone'] = $customer['phone'];
$_SESSION['email'] = $customer['email'];
$_SESSION['address'] = $customer['address'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background-color: #f8f9fa;
    }

    .checkout-header {
        margin-bottom: 20px;
    }

    .btn-primary-custom {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-primary-custom:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-secondary-custom {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }

    .btn-secondary-custom:hover {
        background-color: #5a6268;
        border-color: #4e555b;
    }

    .product-details {
        margin-bottom: 20px;
    }

    .card {
        border: 2px solid #007bff;
    }

    .form-control {
        border-radius: 0.25rem;
        border: 2px solid #ced4da;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
    }

    .form-group label {
        font-weight: bold;
    }

    /* Hide credit card form by default */
    #creditCardForm {
        display: none;
    }
    
    .container {
        max-width: 800px;
        margin-top: 100px;
        font-size: medium;
    }

    .checkout-header h1 {
        font-size: 5rem;
        margin-bottom: 10px;
        font-weight: bold;
        text-align: center;
        color: #333;
    }

    .checkout-header p {
        font-size: 2rem;
        color: red;
        text-align: center;
        font-weight: bold;
    }

    .btn-checkout {
        margin-left: 10px;
        display: inline-block;
        text-decoration: none;
        background-color:forestgreen;
        border-color: forestgreen;
        color: #ffffff;
        border-radius: 25px;
        padding: 8px 20px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-checkout:hover {
        background-color:gold;
        border-color: gold;
        color: black;
        transition: background-color 0.3s ease;
    }

    .btn-back {
        margin-left: 10px;
        display: inline-block;
        text-decoration: none;
        background-color: #007bff;
        border-color: #007bff;
        color: #ffffff;
        border-radius: 25px;
        padding: 8px 20px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-back:hover {
        background-color:cyan;
        border-color: cyan;
        color: black;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="checkout-header text-center">
            <h1>Checkout</h1>
            <p>Please check your details carefully before complete your purchase!</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-4">Product Details</h4>
                <div class="product-details card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($_SESSION['product_name']) ?></h5>
                        <p class="card-text">Price: $<?= number_format($_SESSION['product_price']) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h4 class="mb-4">Customer Details</h4>
                <form action="process_checkout.php" method="POST">
                    <div class="form-group">
                        <label for="fname">Full Name</label>
                        <input type="text" id="fname" name="firstname" class="form-control"
                            value="<?= htmlspecialchars($_SESSION['customer_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($_SESSION['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="adr">Address</label>
                        <input type="text" id="adr" name="address" class="form-control"
                            value="<?= htmlspecialchars($_SESSION['address']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                            value="<?= htmlspecialchars($_SESSION['phone']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="form-check">
                            <input type="radio" id="cod" name="payment_method" value="Cash on Delivery"
                                class="form-check-input" checked onchange="toggleCreditCardForm()">
                            <label for="cod" class="form-check-label">Cash on Delivery</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="cc" name="payment_method" value="Credit by card"
                                class="form-check-input" onchange="toggleCreditCardForm()">
                            <label for="cc" class="form-check-label">By Credit Card</label>
                        </div>
                    </div>

                    <!-- Credit card form -->
                    <div id="creditCardForm">
                        <h5 class="mb-3">Credit Card Information</h5>
                        <div class="form-group">
                            <label for="cardnumber">Card Number</label>
                            <input type="text" id="cardnumber" name="cardnumber" class="form-control"
                                placeholder="1234 5678 9012 3456">
                        </div>
                        <div class="form-group">
                            <label for="expdate">Expiration Date</label>
                            <input type="text" id="expdate" name="expdate" class="form-control" placeholder="MM/YY">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123">
                        </div>
                    </div>

                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>">
                    <input type="hidden" name="product_price"
                        value="<?= htmlspecialchars($product['product_price']) ?>">
                    <button type="submit" class="btn-checkout">Continue to Checkout</button>
                    <a href="cart.php" class="btn-back">Back to Cart</a>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Toggle the visibility of the credit card form and manage the 'required' attributes
    function toggleCreditCardForm() {
        var ccForm = document.getElementById('creditCardForm');
        var ccRadio = document.getElementById('cc');
        var cardFields = document.querySelectorAll('#creditCardForm input');

        if (ccRadio.checked) {
            ccForm.style.display = 'block'; // Show the credit card form
            cardFields.forEach(function(field) {
                field.setAttribute('required', true); // Make credit card fields required
            });
        } else {
            ccForm.style.display = 'none'; // Hide the credit card form
            cardFields.forEach(function(field) {
                field.removeAttribute('required'); // Remove 'required' from credit card fields
            });
        }
    }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>