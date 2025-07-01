<?php
session_start();

// Kiểm tra quyền truy cập admin
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToMySQL();

// Kiểm tra nếu có order_id được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Cập nhật trường is_paid thành 1 cho đơn hàng này
    $sql = "UPDATE `order` SET is_paid = 1 WHERE order_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            $_SESSION['payment_update_message'] = "Payment confirmed successfully.";
        } else {
            $_SESSION['payment_update_message'] = "Failed to confirm payment. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['payment_update_message'] = "Database error. Please contact admin.";
    }
}

// Quay về lại trang quản lý đơn hàng
header("Location: manage_order.php");
exit;
?>
