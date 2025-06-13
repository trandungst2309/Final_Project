<?php
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['customer_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'connect.php'; // Đảm bảo đường dẫn đúng đến file connect.php

$db = new Connect();
$conn = $db->connectToPDO(); // Sử dụng phương thức connectToPDO()

if (isset($_GET['delete'])) {
    $product_id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);

    if ($product_id === false || $product_id <= 0) {
        $_SESSION['error_message'] = "Invalid product ID provided.";
        header('Location: manage_products.php');
        exit;
    }

    try {
        // Optional: Delete product image file from server first
        // Get image name before deleting product record
        $stmt_img = $conn->prepare("SELECT product_img FROM product WHERE product_id = ?");
        $stmt_img->execute([$product_id]);
        $product_img = $stmt_img->fetchColumn();

        if ($product_img && file_exists('uploads/' . $product_img)) {
            unlink('uploads/' . $product_img); // Delete the actual file
        }

        // Delete the product record
        $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->execute([$product_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "Product deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Product not found or could not be deleted.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "No product ID specified for deletion.";
}

// Đóng kết nối
$conn = null;

// Chuyển hướng về trang quản lý sản phẩm
header('Location: manage_products.php');
exit;
?>