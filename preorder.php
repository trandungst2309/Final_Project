<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pre-Order New Motorbike</title>
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 100px;
        }

        .container {
            max-width: 700px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        .container .form-label {
            color: black;
        }

        h2 {
            font-weight: bold;
            color: #dc3545;
        }

        .btn-preorder {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            display: block;
            text-align: center;
        }

        .btn-preorder:hover {
            background-color: dimgrey;
            color: orangered;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Pre-Order New Motorbike</h2>
        <form id="preorderForm" action="process_preorder.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
            <input type="hidden" name="calculated_deposit" id="calculated_deposit">

            <!-- Thông tin sản phẩm -->
            <div class="mb-3">
                <label for="product_name" class="form-label">Motorbike Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="product_name" id="product_name" required>
            </div>

            <div class="mb-3">
                <label for="product_image" class="form-label">Upload Product Image</label>
                <input type="file" class="form-control" name="product_image" id="product_image" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="4"
                    placeholder="Color, type, engine preferences..."></textarea>
            </div>

            <div class="mb-3">
                <label for="expected_price" class="form-label">Expected Price (USD)</label>
                <input type="number" step="0.01" class="form-control" name="expected_price" id="expected_price"
                    required>
            </div>

            <div class="mb-3">
                <label for="preferred_delivery_date" class="form-label">Preferred Delivery Date</label>
                <input type="date" class="form-control" name="preferred_delivery_date" id="preferred_delivery_date"
                    required>
            </div>

            <div class="alert alert-danger">
                <strong>Note:</strong> A minimum deposit of <span id="deposit_display">0 USD</span> is required
                (50% of expected price). If you cancel later, the deposit will be forfeited.
            </div>

            <!-- Thông tin thanh toán ảo -->
            <h5 class="mt-4 mb-3 text-primary">Payment Information</h5>

            <div class="mb-3">
                <label for="card_name" class="form-label">Card Holder Name</label>
                <input type="text" class="form-control" name="card_name" id="card_name" required>
            </div>

            <div class="mb-3">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" class="form-control" name="card_number" id="card_number" pattern="\d{16}" maxlength="16" required
                placeholder="1234 5678 9012 3456">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="card_expiry" class="form-label">Expiry Date (MM/YY)</label>
                    <input type="text" class="form-control" name="card_expiry" id="card_expiry" placeholder="MM/YY"
                        pattern="(0[1-9]|1[0-2])\/\d{2}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="card_cvv" class="form-label">CVV</label>
                    <input type="password" class="form-control" name="card_cvv" id="card_cvv" pattern="\d{3}" maxlength="3" required
                    placeholder="123">
                </div>
            </div>

            <!-- Checkbox đồng ý điều khoản -->
            <div class="form-check mt-4 mb-3">
                <input class="form-check-input" type="checkbox" id="agree_terms" required>
                <label class="form-check-label" for="agree_terms">
                    I agree to the <a href="terms_and_conditions.php">terms and conditions</a>, including the non-refundable deposit policy.
                </label>
            </div>

            <button type="submit" class="btn-preorder">Submit Pre-Order</button>
        </form>
    </div>

    <script>
        const expectedPriceInput = document.getElementById('expected_price');
        const depositDisplay = document.getElementById('deposit_display');
        const depositInput = document.getElementById('calculated_deposit');

        expectedPriceInput.addEventListener('input', function () {
            const price = parseFloat(this.value);
            if (!isNaN(price)) {
                const deposit = price * 0.5;
                depositDisplay.textContent = deposit.toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'USD'
                });
                depositInput.value = deposit.toFixed(2);
            } else {
                depositDisplay.textContent = '0 USD';
                depositInput.value = '';
            }
        });

        // Đảm bảo checkbox được check mới cho phép submit
        const form = document.getElementById('preorderForm');
        const termsCheckbox = document.getElementById('agree_terms');

        form.addEventListener('submit', function (e) {
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert("You must agree to the terms and conditions to submit the preorder.");
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>
