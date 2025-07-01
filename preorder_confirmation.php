<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pre-order Confirmation - TD Motor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 100px;
        }
        .container {
            padding: 30px;
        }
        .confirmation-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn-home {
            margin-top: 30px;
            padding: 10px 30px;
            border-radius: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <div class="confirmation-container mt-5">
        <h2>Your Pre-order has been submitted successfully!</h2>
        <p>Thank you for placing a pre-order with TD Motor! We will review your request and contact you soon!</p>
        <a href="homepage.php" class="btn btn-primary btn-home">Back to Homepage</a>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
