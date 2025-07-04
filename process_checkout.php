<?php
session_start();
include 'connect.php';

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
        $quantity = 1; // Only one product per order
        $order_status = 'Pending';

        // Khởi tạo kết nối
        $conn = new Connect();
        $db_link = $conn->connectToPDO();

        // Thêm đơn hàng vào bảng `order`
        $query = "INSERT INTO `order` 
            (customer_id, product_id, payment, order_date, quantity, customer_name, order_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db_link->prepare($query);
        $stmt->execute([$customer_id, $product_id, $payment, $order_date, $quantity, $customer_name, $order_status]);

        // KHÔNG giảm tồn kho, KHÔNG tăng sold_quantity ở đây

        // Xoá khỏi giỏ hàng nếu có
        $query = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";
        $stmt = $db_link->prepare($query);
        $stmt->execute([$customer_id, $product_id]);

        // Điều hướng sang trang xác nhận
        header('Location: order_confirmation.php');
        exit();
    } else {
        echo "Please fill in all required information.";
    }
} else {
    header('Location: checkout.php');
    exit();
}
?>
