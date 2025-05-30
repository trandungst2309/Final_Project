<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'motorbike');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $sql = "UPDATE `order` SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $order_status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order status updated successfully!'); window.location.href='manage_order.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
