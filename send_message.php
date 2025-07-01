<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <!-- SweetAlert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
</body>
</html>

<?php
session_start();
include 'connect.php';

$c = new Connect();
$conn = $c->connectToMySQL();

// Lấy dữ liệu từ form
$name = trim($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');
$email = trim($_SESSION['email'] ?? $_POST['email'] ?? '');
$customer_id = $_SESSION['customer_id'] ?? null;

// Kiểm tra dữ liệu đầu vào từng bước
if (empty($name)) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Missing Name!',
            text: 'Please enter your name!',
        }).then(() => { window.history.back(); });
    </script>";
    exit;
}

if (empty($email)) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Missing Email!',
            text: 'Please enter your email address!',
        }).then(() => { window.history.back(); });
    </script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email!',
            text: 'Please enter a valid email address!',
        }).then(() => { window.history.back(); });
    </script>";
    exit;
}

if (empty($message)) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Empty Message!',
            text: 'Please enter your message!',
        }).then(() => { window.history.back(); });
    </script>";
    exit;
}

// Nếu hợp lệ, thêm vào CSDL
$sql = "INSERT INTO contact_messages (name, email, message, customer_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssi", $name, $email, $message, $customer_id);

if ($stmt->execute()) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Message Sent!',
            text: 'Your message has been successfully sent!',
        }).then(() => { window.location.href = 'homepage.php'; });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Database Error!',
            text: 'Failed to send message. Please try again later!',
        }).then(() => { window.history.back(); });
    </script>";
}

$stmt->close();
$conn->close();
?>
