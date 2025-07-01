<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $customer_id = intval($_POST['delete_id']);

    $connect = new Connect();
    $db = $connect->connectToPDO();

    try {
        $stmt = $db->prepare("DELETE FROM customer WHERE customer_id = ?");
        if ($stmt->execute([$customer_id])) {
            $_SESSION['delete_message'] = "Account deleted successfully.";
        } else {
            $_SESSION['delete_message'] = "Failed to delete account.";
        }
    } catch (PDOException $e) {
        $_SESSION['delete_message'] = "Error: " . $e->getMessage();
    }

    $db = null;
}

header("Location: manage_account.php");
exit();
