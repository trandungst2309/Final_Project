<?php
session_start();
include 'connect.php';

// Gọi PHPMailer
require 'send_mail.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($_SESSION['customer_id'], $_POST['product_id'], $_POST['product_name'],
        $_POST['product_price'], $_POST['firstname'], $_POST['email'], $_POST['address'],
        $_POST['phone'], $_POST['payment_method'])
    ) {
        $customer_id = $_SESSION['customer_id'];
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $customer_name = $_POST['firstname'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $payment = $_POST['payment_method'];
        $order_date = date('Y-m-d H:i:s');
        $quantity = 1;
        $order_status = 'Pending';

        $conn = new Connect();
        $db_link = $conn->connectToPDO();

        // Thêm đơn hàng
        $query = "INSERT INTO `order` 
            (customer_id, product_id, payment, order_date, quantity, customer_name, order_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db_link->prepare($query);
        $stmt->execute([$customer_id, $product_id, $payment, $order_date, $quantity, $customer_name, $order_status]);

        // Xóa khỏi giỏ hàng nếu có
        $query = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";
        $stmt = $db_link->prepare($query);
        $stmt->execute([$customer_id, $product_id]);

        // Gửi email xác nhận
        $subject = 'Order Confirmation - TD Motor';
        $body = "
            <h3>Dear $customer_name,</h3>
            <p>Thank you for your order at <strong>TD Motor</strong>.</p>
            <p>
                <strong>Product:</strong> $product_name<br>
                <strong>Price:</strong> $" . number_format($product_price) . "<br>
                <strong>Payment Method:</strong> $payment<br>
                <strong>Shipping Address:</strong> $address
            </p>
            <p>We will process and deliver your order as soon as possible.</p>
            <br><p>Best regards,<br><strong>TD Motor</strong></p>
        ";
        sendMail($email, $customer_name, $subject, $body);

        // Điều hướng
        header('Location: order_confirmation.php');
        exit();
    } else {
        echo "Please fill in all required information.";
    }
} else {
    header('Location: checkout.php');
    exit();
}

if ($_POST['payment_method'] === "Credit by card") {
    $cardnumber = preg_replace('/\s+/', '', $_POST['cardnumber']);
    $expdate = $_POST['expdate'];
    $cvv = $_POST['cvv'];

    if (!preg_match('/^\d{16}$/', $cardnumber)) {
        die("Invalid card number!");
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expdate)) {
        die("Invalid expiration date format!");
    } else {
        list($mm, $yy) = explode("/", $expdate);
        $expYear = (int)("20".$yy);
        $expMonth = (int)$mm;
        if (mktime(0,0,0,$expMonth+1,0,$expYear) < time()) {
            die("Card is expired!");
        }
    }

    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        die("Invalid CVV!");
    }
}

?>
