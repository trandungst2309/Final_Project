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
    header('Location: manage_producer.php');
    exit;
}

$producer_id = (int)$_GET['id'];

// Kiểm tra xem producer có đang được tham chiếu trong bảng product không
$check = $conn->prepare("SELECT COUNT(*) AS total FROM product WHERE producer_id = ?");
$check->bind_param("i", $producer_id);
$check->execute();
$countResult = $check->get_result()->fetch_assoc();

if ($countResult['total'] > 0) {
    echo "<script>alert('Cannot delete this producer because it is in use.'); window.location.href='manage_producer.php';</script>";
    exit;
}

$stmt = $conn->prepare("DELETE FROM producer WHERE producer_id = ?");
$stmt->bind_param("i", $producer_id);

if ($stmt->execute()) {
    header("Location: manage_producer.php");
    exit;
} else {
    echo "<script>alert('Delete failed: {$conn->error}'); window.location.href='manage_producer.php';</script>";
}
?>