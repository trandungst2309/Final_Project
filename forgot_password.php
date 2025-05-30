<?php
session_start();
include_once 'connect.php';

$conn = new Connect();
$db_link = $conn->connectToPDO(); 

$err = "";

if (isset($_POST['btnSubmitEmail'])) {
    $email = $_POST['txtEmail'];

    $sql = "SELECT * FROM customer WHERE email = ?";
    $stmt = $db_link->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['step'] = 'new_password';
    } else {
        $err = "Email does not exist in the system.";
    }
}

if (isset($_POST['btnSavePassword']) && isset($_SESSION['step']) && $_SESSION['step'] == 'new_password') {
    $pass1 = $_POST['txtNewPass'];
    $pass2 = $_POST['txtConfirmPass'];

    if (strlen($pass1) <= 5) {
        $err .= "Password must be longer than 5 characters. ";
    }
    if ($pass1 != $pass2) {
        $err .= "Password and confirm password do not match. ";
    }

    if ($err == "") {
        $hashed_pass = password_hash($pass1, PASSWORD_BCRYPT);
        $email = $_SESSION['reset_email'];

        $sql = "UPDATE customer SET pass = ? WHERE email = ?";
        $stmt = $db_link->prepare($sql);
        if ($stmt->execute([$hashed_pass, $email])) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password changed successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href='loginnew.php';
                    });
                });
            </script>";
            unset($_SESSION['reset_email'], $_SESSION['step']);
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/login.css"> <!-- Sử dụng chung login.css -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once 'header.php'; ?>
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <form class="login" method="POST" action="">
                    <h1>Forgot Password</h1>

                    <?php if ($err != ""): ?>
                    <div class="error"><?php echo $err; ?></div>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['step']) || $_SESSION['step'] != 'new_password'): ?>
                    <!-- Step 1: Email input -->
                    <div class="login__field">
                        <input type="email" name="txtEmail" class="login__input" placeholder="Enter your email"
                            required>
                    </div>
                    <button type="submit" name="btnSubmitEmail" class="login__submit">Send</button>
                    <a href="loginnew.php" class="button__back">Back To Login Page</a>
                    <?php else: ?>
                    <!-- Step 2: New password input -->
                    <div class="login__field">
                        <input type="password" name="txtNewPass" class="login__input" placeholder="New Password"
                            required>
                    </div>
                    <div class="login__field">
                        <input type="password" name="txtConfirmPass" class="login__input" placeholder="Confirm Password"
                            required>
                    </div>
                    <button type="submit" name="btnSavePassword" class="login__submit">Save Changes</button>
                    <a href="loginnew.php" class="button__back">Back To Login Page</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Background shapes -->
            <div class="screen__background">
                <!-- <span class="screen__background__shape screen__background__shape1"></span>
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape3"></span>
            <span class="screen__background__shape screen__background__shape4"></span> -->
            </div>
        </div>
    </div>
</body>

</html>