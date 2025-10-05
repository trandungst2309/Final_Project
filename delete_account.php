<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $current_admin_id = $_SESSION['admin_id'] ?? null; // id admin đang đăng nhập

    $connect = new Connect();
    $db = $connect->connectToPDO();

    try {
        // Lấy thông tin user cần xóa
        $stmt = $db->prepare("SELECT role FROM customer WHERE customer_id = ?");
        $stmt->execute([$delete_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['delete_message'] = "Account not found.";
        } elseif ($user['role'] === 'customer') {
            // Cho phép xóa customer
            $del = $db->prepare("DELETE FROM customer WHERE customer_id = ?");
            if ($del->execute([$delete_id])) {
                $_SESSION['delete_message'] = "Customer account deleted successfully.";
            } else {
                $_SESSION['delete_message'] = "Failed to delete customer account.";
            }
        } elseif ($user['role'] === 'admin') {
            // Kiểm tra không cho xóa chính mình
            if ($delete_id == $current_admin_id) {
                $_SESSION['delete_message'] = "Error: You cannot delete your own admin account.";
            } else {
                // Kiểm tra số lượng admin hiện có
                $countStmt = $db->query("SELECT COUNT(*) FROM customer WHERE role = 'admin'");
                $adminCount = $countStmt->fetchColumn();

                if ($adminCount <= 1) {
                    $_SESSION['delete_message'] = "Error: Cannot delete the last admin account.";
                } else {
                    $_SESSION['delete_message'] = "Error: Deleting other admin accounts is not allowed.";
                }
            }
        } else {
            $_SESSION['delete_message'] = "Error: Unknown account role.";
        }
    } catch (PDOException $e) {
        $_SESSION['delete_message'] = "Error: " . $e->getMessage();
    }

    $db = null;
}

header("Location: manage_account.php");
exit();
