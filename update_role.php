<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'], $_POST['role'])) {
    $customer_id = intval($_POST['customer_id']);
    $role = $_POST['role'];

    // Kết nối database bằng PDO
    $conn = (new Connect())->connectToPDO();

    try {
        $stmt = $conn->prepare("UPDATE customer SET role = :role WHERE customer_id = :customer_id");
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();

        // Thêm thông báo đúng role vừa cập nhật
        $_SESSION['role_update_message'] = "Updated role to $role for user ID $customer_id.";
    } catch (PDOException $e) {
        $_SESSION['role_update_message'] = "Error updating role: " . $e->getMessage();
    }

    // Chuyển hướng lại
    header("Location: manage_account.php");
    exit();
} else {
    // Nếu thiếu dữ liệu
    $_SESSION['role_update_message'] = "Invalid form submission.";
    header("Location: manage_account.php");
    exit();
}
?>
