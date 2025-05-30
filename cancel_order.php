<?php
session_start();
include 'connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: homepage.php');
    exit();
}

// Check if the order_id is provided
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Initialize database connection
    $conn = new Connect();
    $db_link = $conn->connectToPDO();

    // Update the order status to "canceled"
    $query = "UPDATE `order` SET order_status = 'Cancelled' WHERE order_id = ? AND order_status = 'Pending'";
    $stmt = $db_link->prepare($query);
    $stmt->execute([$order_id]);

    // Redirect back to the order history page
    header('Location: order_detail.php');
    exit();
} else {
    // If order_id is not set, redirect back to the order history page
    header('Location: order_detail.php');
    exit();
}
