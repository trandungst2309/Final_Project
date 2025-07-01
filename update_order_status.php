<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['order_status'];

    $conn = new Connect();
    $db = $conn->connectToPDO();

    // 1. Lấy trạng thái cũ và product_id của đơn hàng
    $stmt = $db->prepare("SELECT order_status, product_id FROM `order` WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $old_status = $order['order_status'];
        $product_id = $order['product_id'];

        // 2. Nếu trạng thái cũ KHÁC 'Completed' và trạng thái mới LÀ 'Completed'
        if ($old_status !== 'Completed' && $new_status === 'Completed') {
            // Tăng số lượng đã bán
            $update_sold = $db->prepare("UPDATE product SET sold_quantity = sold_quantity + 1 WHERE product_id = :product_id");
            $update_sold->execute([':product_id' => $product_id]);

            // // Giảm tồn kho đúng 1 lần
            // $update_stock = $db->prepare("UPDATE product SET quantity = quantity - 1 WHERE product_id = :product_id AND quantity > 0");
            // $update_stock->execute([':product_id' => $product_id]);
        }

        // 3. Cập nhật trạng thái đơn hàng
        $update_status = $db->prepare("UPDATE `order` SET order_status = :new_status WHERE order_id = :order_id");
        $update_status->execute([
            ':new_status' => $new_status,
            ':order_id' => $order_id
        ]);
    }
}

// 4. Quay về trang quản lý đơn hàng
header("Location: manage_order.php");
exit;
