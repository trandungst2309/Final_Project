<!DOCTYPE html>
<html lang="en">
<link rel="icon" href="image/TDicon.png" type="image/x-icon">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<style>
    .btn-back{
        background-color:forestgreen;
        color: white;
        border: none;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    /* .btn-back:hover {
        background-color: darkgreen !important;
        color: #333 !important;
        text-decoration: none !important;
        transition: background-color 0.3s ease;
    } */
</style>

<body>

    <?php
session_start();
include_once 'connect.php';

// Code for register page
$err = ""; // Declare the variable outside to ensure it's available later
if (isset($_POST['btnRegister'])) {
    $fullname = $_POST['txtFullname'];
    $email = $_POST['txtEmail'];
    $pass1 = $_POST['txtPass1'];
    $pass2 = $_POST['txtPass2'];
    $phone = $_POST['txtPhone'];
    $address = $_POST['txtAddress'];
    $role = 'customer';
    
    if (strlen($pass1) <= 5) {
        $err .= "Password must be greater than 5 characters. ";
    }

    if ($pass1 != $pass2) {
        $err .= "Password and confirm password must be the same. ";
    }

    // Error display moved to a variable
    if ($err == "") {
        // Use PDO for database interaction
        $conn = new Connect();
        $db_link = $conn->connectToPDO();
        $pass = password_hash($pass1, PASSWORD_BCRYPT);
        $sql = "SELECT * FROM customer WHERE email = ?";
        $stmt = $db_link->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 0) {
            $insert_sql = "INSERT INTO customer (customer_name, email, pass, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')";
            $stmt = $db_link->prepare($insert_sql);
            if ($stmt->execute([$fullname, $email, $pass, $phone, $address])) {
                echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
                exit(); // Ensure script termination after redirection
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
            }
        } else {
            $err .= "Email already exists.";
        }
    }
}
?>
    <?php include_once 'header.php'; ?>
    <div class="container"">
    <div class=" screen">
        <div class="screen__content">
            <form class="login" method="POST" action="">
                <!-- Error messages displayed here -->
                <?php
                if ($err != "") {
                    echo "<ul class='error'>$err</ul>";
                }
                ?>

                <h1>Register Page</h1>
                <div class="login__field">
                    <i class="login__icon fas fa-user"></i>
                    <input type="text" class="login__input" name="txtFullname" id="txtFullname" placeholder="Full Name"
                        required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-envelope"></i>
                    <input type="text" class="login__input" name="txtEmail" id="txtEmail" placeholder="Your Email"
                        required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-lock"></i>
                    <input type="password" class="login__input" name="txtPass1" id="txtPass1" placeholder="Password"
                        required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-lock"></i>
                    <input type="password" class="login__input" name="txtPass2" id="txtPass2"
                        placeholder="Confirm Password" required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-phone"></i>
                    <input type="text" class="login__input" name="txtPhone" id="txtPhone" placeholder="Your Phone"
                        required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-home"></i>
                    <input type="text" class="login__input" name="txtAddress" id="txtAddress" placeholder="Your Address"
                        required>
                </div>

                <button class="button login__submit" name="btnRegister" id="signup">
                    <span class="button__text">Register Now</span>
                    <i class="button__icon fas fa-chevron-right"></i>
                </button>

                <!-- New Back to Login link -->
                <a href="login.php" class="button__back">
                    <span class="button__text">Back to Login Page</span>
                </a>

                <br>
                <br>
            </form>
        </div>
    </div>
    </div>
</body>

</html>