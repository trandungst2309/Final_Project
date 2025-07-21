<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$preorder_id = $_GET['preorder_id'] ?? null;

if (!$preorder_id) {
    echo "<div class='alert alert-danger'>Invalid Pre-order ID.</div>";
    exit;
}

$conn = new Connect();
$db = $conn->connectToPDO();

// Lấy thông tin đơn hàng preorder
$stmt = $db->prepare("SELECT * FROM preorder WHERE preorder_id = ? AND customer_id = ?");
$stmt->execute([$preorder_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='alert alert-danger'>Pre-order not found or access denied.</div>";
    exit;
}

// Lấy thông tin khách hàng từ bảng customer
$stmt_customer = $db->prepare("SELECT * FROM customer WHERE customer_id = ?");
$stmt_customer->execute([$customer_id]);
$customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "<div class='alert alert-danger'>Customer details not found.</div>";
    exit;
}

// Kiểm tra điều kiện hợp lệ để thanh toán cuối cùng
if ($order['status'] !== 'Arrived') {
    echo "<div class='alert alert-warning'>This order is not eligible for final payment yet. The order has not arrived.</div>";
    exit;
}
if ($order['is_deposit_paid'] != 1) { // 0 = not paid, 1 = paid
    echo "<div class='alert alert-warning'>This order is not eligible for final payment yet. The deposit has not been paid.</div>";
    exit;
}

// Nếu submit thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin khách hàng đã chỉnh sửa từ form
    $customer_name_from_form = htmlspecialchars($_POST['firstname'] ?? '');
    $email_from_form = htmlspecialchars($_POST['email'] ?? '');
    $address_from_form = htmlspecialchars($_POST['address'] ?? '');
    $phone_from_form = htmlspecialchars($_POST['phone'] ?? '');
    $payment_method = htmlspecialchars($_POST['payment_method'] ?? '');

    $cardnumber = '';
    $expdate = '';
    $cvv = '';
    if ($payment_method === 'Credit by card') {
        $cardnumber = htmlspecialchars($_POST['cardnumber'] ?? '');
        $expdate = htmlspecialchars($_POST['expdate'] ?? '');
        $cvv = htmlspecialchars($_POST['cvv'] ?? '');
    }

    // Cập nhật thông tin khách hàng vào CSDL
    $update_customer_stmt = $db->prepare("UPDATE customer SET customer_name = ?, email = ?, address = ?, phone = ? WHERE customer_id = ?");
    $update_customer_stmt->execute([
        $customer_name_from_form,
        $email_from_form,
        $address_from_form,
        $phone_from_form,
        $customer_id
    ]);

    // Đây là nơi bạn sẽ tích hợp với cổng thanh toán thực tế.
    // Sau khi thanh toán thực tế thành công, bạn mới cập nhật trạng thái trong CSDL.

    $update = $db->prepare("UPDATE preorder SET final_payment_status = 1 WHERE preorder_id = ?");
    $update->execute([$preorder_id]);

    echo '
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="text-center">
                <div class="mb-4">
                    <!-- SVG Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 10.97a.75.75 0 0 0 1.07 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 6.03 7.97a.75.75 0 1 0-1.06 1.06l2 2z"/>
                    </svg>
                </div>
                <h3 class="text-success">Payment Successful!</h3>
                <p class="mb-4">Thank you! Your remaining payment has been completed.</p>
                <a href="preorder_list.php" class="btn btn-primary"
                style="border-radius: 25px; padding: 8px 20px; font-weight: 500; transition: background-color 0.3s ease;">
                Back to My Pre-orders
                </a>
            </div>
        </div>';
    exit;
}

// Tính số tiền còn lại
$expected_price = $order['expected_price'];
$deposit_amount = $order['deposit_amount'];
$remaining_balance = $expected_price - $deposit_amount;

// Lưu thông tin khách hàng vào biến để điền vào form, sẽ ưu tiên dữ liệu POST nếu có
$display_customer_name = htmlspecialchars($_POST['firstname'] ?? $customer['customer_name']);
$display_email = htmlspecialchars($_POST['email'] ?? $customer['email']);
$display_address = htmlspecialchars($_POST['address'] ?? $customer['address']);
$display_phone = htmlspecialchars($_POST['phone'] ?? $customer['phone']);

// Xác định phương thức thanh toán đã chọn (để giữ trạng thái radio button sau khi submit nếu có lỗi)
$selected_payment_method = $_POST['payment_method'] ?? 'Cash on Delivery';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Final Payment - TD Motor</title>
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* ... (CSS của bạn không thay đổi) ... */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .checkout-header {
            margin-bottom: 20px;
        }

        .product-details {
            margin-bottom: 20px;
        }

        .card {
            border: 2px solid #007bff;
            /* Thay đổi màu viền card */
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
            background-color: forestgreen;
            border-color: forestgreen;
            color: #ffffff;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-checkout:hover {
            background-color: gold;
            border-color: gold;
            color: black;
        }

        .btn-back {
            margin-left: 10px;
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
            background-color: cyan;
            border-color: cyan;
            color: black;
            text-decoration: none;
        }

        #creditCardForm {
            display: none;
        }

        .btn-checkout {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            background-color: forestgreen;
            border-color: forestgreen;
            color: #ffffff;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-checkout:hover {
            background-color: gold;
            border-color: gold;
            color: black;
        }

        .btn-back {
            margin-top: 20px;
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
            background-color: cyan;
            border-color: cyan;
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="checkout-header text-center">
            <h1>Final Payment</h1>
            <p>Complete your remaining payment for the pre-ordered product!</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-4">Pre-ordered Product Details</h4>
                <div class="product-details card text-center p-3">
                    <img src="uploads/preorders/<?= htmlspecialchars($order['product_image']) ?>"
                        alt="<?= htmlspecialchars($order['product_name']) ?>" class="img-fluid rounded mb-3"
                        style="max-height: 300px; object-fit: contain;">
                    <h5 class="card-title"><?= htmlspecialchars($order['product_name']) ?></h5>
                    <p class="card-text">
                        <strong>Expected Price:</strong> <span class="text-primary font-weight-bold"
                            style="font-size: 1.25rem;">$<?= number_format($expected_price) ?></span>
                    </p>
                    <p class="card-text">
                        <strong>Deposit Paid:</strong> <span class="text-success font-weight-bold"
                            style="font-size: 1.25rem;">$<?= number_format($deposit_amount) ?></span>
                    </p>
                    <hr>
                    <p class="card-text">
                        <strong>Remaining Balance:</strong> <span class="text-danger font-weight-bold"
                            style="font-size: 1.5rem;">$<?= number_format($remaining_balance) ?></span>
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <h4 class="mb-4">Your Details and Payment</h4>
                <?php if ($order['final_payment_status'] == 1): ?>
                    <div class="alert alert-info mt-4">
                        You have already completed the final payment for this pre-order.
                    </div>
                    <div class="text-center mt-3">
                        <a href="preorder_list.php" class="btn btn-primary">Back to My Pre-orders</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="fname">Full Name</label>
                            <input type="text" id="fname" name="firstname" class="form-control"
                                value="<?= $display_customer_name ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= $display_email ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="adr">Address</label>
                            <input type="text" id="adr" name="address" class="form-control" value="<?= $display_address ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="<?= $display_phone ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Payment Method</label>
                            <div class="form-check">
                                <input type="radio" id="cod" name="payment_method" value="Cash on Delivery"
                                    class="form-check-input"
                                    <?= ($selected_payment_method == 'Cash on Delivery') ? 'checked' : '' ?>
                                    onchange="toggleCreditCardForm()">
                                <label for="cod" class="form-check-label">Cash on Delivery</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="cc" name="payment_method" value="Credit by card"
                                    class="form-check-input"
                                    <?= ($selected_payment_method == 'Credit by card') ? 'checked' : '' ?>
                                    onchange="toggleCreditCardForm()">
                                <label for="cc" class="form-check-label">By Credit Card</label>
                            </div>
                        </div>

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

                        <button type="submit" class="btn-checkout">Complete Final Payment</button>
                        <a href="preorder_list.php" class="btn-back">Back to My Pre-orders</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleCreditCardForm() {
            var ccForm = document.getElementById('creditCardForm');
            var ccRadio = document.getElementById('cc');
            var cardFields = document.querySelectorAll('#creditCardForm input');

            if (ccRadio.checked) {
                ccForm.style.display = 'block';
                cardFields.forEach(function(field) {
                    field.setAttribute('required', true);
                });
            } else {
                ccForm.style.display = 'none';
                cardFields.forEach(function(field) {
                    field.removeAttribute('required');
                });
            }
        }
        document.addEventListener('DOMContentLoaded', toggleCreditCardForm);
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>