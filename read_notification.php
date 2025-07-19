<?php
session_start();
require 'connect.php';

if (isset($_GET['id'], $_GET['redirect'], $_SESSION['customer_id'])) {
    $notification_id = (int)$_GET['id'];
    $customer_id = $_SESSION['customer_id'];
    $redirect = $_GET['redirect'];

    $db = (new Connect())->connectToPDO();
    $stmt = $db->prepare("UPDATE notification SET is_read = 1 WHERE notification_id = ? AND customer_id = ?");
    $stmt->execute([$notification_id, $customer_id]);

    header("Location: " . $redirect);
    exit;
}
header("Location: homepage.php");
exit;
