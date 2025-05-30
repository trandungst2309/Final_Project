<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'motorbike');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kiểm tra xem có yêu cầu xóa tài khoản không
if (isset($_POST['delete_id'])) {
    $customer_id = intval($_POST['delete_id']); // Chuyển đổi thành số nguyên để bảo mật

    // Xóa các bản ghi liên quan trong bảng `cart`
    $delete_cart_sql = "DELETE FROM cart WHERE customer_id = ?";
    $delete_cart_stmt = $conn->prepare($delete_cart_sql);
    if ($delete_cart_stmt) {
        $delete_cart_stmt->bind_param("i", $customer_id);
        $delete_cart_stmt->execute();
        $delete_cart_stmt->close();
    }

    // Xóa tài khoản
    $sql = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("i", $customer_id);

    if ($stmt->execute()) {
        echo "<script>alert('Account deleted successfully!'); window.location.href='manage_account.php';</script>";
    } else {
        echo "<script>alert('Error deleting account: " . ($stmt->error) . "'); window.location.href='manage_account.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
