<?php
session_start();
ob_start();
include_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<link rel="icon" href="image/TDicon.png" type="image/x-icon">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TD Motor</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <?php
// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    // Logout, destroy session và unset variables
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_name']);
    unset($_SESSION['email']);
    unset($_SESSION['role']);
    session_destroy();
    setcookie(session_name(), '', time() - 3600);
    header('Location: loginnew.php'); // Redirect đến trang login
    exit;
}

if (isset($_POST['btnLogin'])) {
    $email = $_POST['txtEmail'];
    $pass = $_POST['txtPass'];
    $err = "";

    // Use PDO for database interaction 
    $conn = new Connect();
    $db_link = $conn->connectToPDO();

    // Fetch the user data
    $sql = "SELECT * FROM customer WHERE email = ?";
    $stmt = $db_link->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt && $stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_password = $row['pass'];
        $role = $row['role']; // Get the user's role

        // Verify the password
        if (password_verify($pass, $stored_password)) {
            // Login successful
            $_SESSION['customer_id'] = $row['customer_id']; 
            $_SESSION['customer_name'] = $row['customer_name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $role; // Store the user's role in the session

            // Redirect based on role
            if ($role === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: homepage.php');
            }
            exit(); // Ensure execution stops after redirect
        } else {
            $err = "Incorrect password.";
        }
    } else {
        $err = "No user found with this email.";
    }
    $_SESSION['login_error'] = $err;
}
?>
    <?php include_once 'header.php'; ?>
    <div class="container" style="padding-top: 150px;">
        <div class="screen">
            <div class="screen__content">
                <?php if (isset($_SESSION['login_error'])) {?>
                <div class="error">
                    <?php echo $_SESSION['login_error'];?>
                </div>
                <?php unset($_SESSION['login_error']);?>
                <!-- Clear the error message -->
                <?php }?>
                <form class="login" method="POST" action="">
                    <h1>Login page</h1>

                    <div class="login__field">
                        <i class="login__icon fas fa-user"></i>
                        <input type="text" class="login__input" name="txtEmail" placeholder="Email" required>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" class="login__input" name="txtPass" placeholder="Password" required>
                    </div>

                    <button class="button login__submit" name="btnLogin">
                        <span class="button__text">Log In Now</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                    <button class="button__back" name="btnBack"
                        onclick="window.location.href='homepage.php'; return false;">
                        <span class="button__text">Back to homepage</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                    <br>
                    <br>
                    <p class="text-center mb-0" style="color: blue;">Don't have an Account? <a href="register.php"
                            class="sign_up">Sign Up</a></p>
                    <br>
                    <p><a href="forgot_password.php" class="forgot_pass">Forgot Password?</a></p>


                </form>
            </div>
            <!-- <div class="screen__background">
            <span class="screen__background__shape screen__background__shape4"></span>
            <span class="screen__background__shape screen__background__shape3"></span>
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape1"></span>
        </div> -->
        </div>
    </div>
</body>

</html>