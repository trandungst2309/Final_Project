<?php
session_start();
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';
$connect = new Connect();
$conn = $connect->connectToMySQL();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_product_type.php');
    exit;
}

$product_type_id = (int)$_GET['id'];

// Kiểm tra xem có product nào đang tham chiếu đến loại này không
$check = $conn->prepare("SELECT COUNT(*) AS total FROM product WHERE product_type_id = ?");
$check->bind_param("i", $product_type_id);
$check->execute();
$countResult = $check->get_result()->fetch_assoc();

if ($countResult['total'] > 0) {
    // Có sản phẩm tham chiếu, không xóa được
    echo "<script>alert('Cannot delete this product type because it is in use.'); window.location.href='manage_product_type.php';</script>";
    exit;
}

// Tiến hành xóa
$stmt = $conn->prepare("DELETE FROM product_type WHERE product_type_id = ?");
$stmt->bind_param("i", $product_type_id);

if ($stmt->execute()) {
    header("Location: manage_product_type.php");
    exit;
} else {
    echo "<script>alert('Delete failed: {$conn->error}'); window.location.href='manage_product_type.php';</script>";
}
?>
