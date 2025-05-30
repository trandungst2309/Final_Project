<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .confirmation-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .confirmation-header {
            border-bottom: 3px solid green;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: green;
            font-size: 5rem;
            font-weight: bold;
        }
        .btn-primary-custom {
            background-color:green !important;
            border-color: green !important;
        }
        .btn-primary-custom:hover {
            background-color: darkcyan !important;
            border-color: darkcyan !important;
            color: white !important;
        }

        .container{
            max-width: 600px;
            margin-top: 100px;
            padding: 20px;
            font-size: medium;
        }

        .lead {
            font-size: 2rem !important;
            font-weight: bold !important;
            color: #333 !important;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="confirmation-container text-center">
            <div class="confirmation-header">
                <h2>ORDER SUCCESFULLY!</h2>
            </div>
            <p class="lead">Thank you for your purchase!</p>
            <p>Your order has been placed successfully. We appreciate your business and hope you enjoy your purchase. If you have any questions or need further assistance, please don't hesitate to contact us.</p>
            <a href="homepage.php" class="btn btn-primary-custom btn-lg">Back to Home</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
