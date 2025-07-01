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
        /* background-color: #f8f9fa; */
        padding-top: 100px;
    }

    .container {
        max-width: 700px;
        /* background: white; */
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
        background-color:dimgrey;
        color: orangered;
    }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Pre-Order New Motorbike</h2>
        <form action="process_preorder.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="customer_id" value="<?= $customer_id ?>">

            <div class="mb-3">
                <label for="product_name" class="form-label">Motorbike Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="product_name" id="product_name" required>
            </div>

            <div class="mb-3">
                <label for="product_image" class="form-label">Upload Product Image (Optional)</label>
                <input type="file" class="form-control" name="product_image" id="product_image" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description (Optional)</label>
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

            <button type="submit" class="btn-preorder">Submit Pre-Order</button>
        </form>

    </div>
</body>
<?php include 'footer.php'; ?>

</html>