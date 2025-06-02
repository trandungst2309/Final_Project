<?php
session_start();
include_once 'connect.php';

$conn = new Connect();
$db_link = $conn->connectToPDO();

// Xử lý thay đổi mật khẩu
if (isset($_POST['btnSavePassword'])) {
    $current_pass = $_POST['txtCurrentPass'];
    $new_pass = $_POST['txtNewPass'];
    $confirm_pass = $_POST['txtConfirmPass'];
    $err = "";

    // Kiểm tra mật khẩu hiện tại
    $customer_id = $_SESSION['customer_id'];
    $sql = "SELECT pass FROM customer WHERE customer_id = ?";
    $stmt = $db_link->prepare($sql);
    $stmt->execute([$customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer || !password_verify($current_pass, $customer['pass'])) {
        $err .= "Current password is incorrect. ";
    }

    // Kiểm tra mật khẩu mới
    if (strlen($new_pass) <= 5) {
        $err .= "New password must be longer than 5 characters. ";
    }
    if ($new_pass != $confirm_pass) {
        $err .= "New password and confirm password do not match. ";
    }

    if ($err == "") {
        $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
        $sql = "UPDATE customer SET pass = ? WHERE customer_id = ?";
        $stmt = $db_link->prepare($sql);
        if ($stmt->execute([$hashed_pass, $customer_id])) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Password changed successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            window.location.href='profile.php';
                        });
                    });
                  </script>";
            session_destroy();
        } else {
            $err = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css"> <!-- Đường dẫn login.css -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include_once 'header.php'; ?>
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <form method="POST" action="" class="login">
                    <h1>Change Password</h1>

                    <?php if (isset($err) && $err != ""): ?>
                        <div class="error"><?= $err ?></div>
                    <?php endif; ?>

                    <div class="login__field">
                        <input type="password" name="txtCurrentPass" placeholder="Current Password" class="login__input" required>
                    </div>
                    <div class="login__field">
                        <input type="password" name="txtNewPass" placeholder="New Password" class="login__input" required>
                    </div>
                    <div class="login__field">
                        <input type="password" name="txtConfirmPass" placeholder="Confirm Password" class="login__input" required>
                    </div>

                    <button type="submit" name="btnSavePassword" class="login__submit">Save changes</button>
                    <a href="profile.php"><button type="button" class="button__back">Back to Profile</button></a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
