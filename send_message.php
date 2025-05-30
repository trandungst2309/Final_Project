<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <!-- Thêm SweetAlert vào <head> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    
</body>
</html>

<?php
session_start();
include 'connect.php';

$c = new Connect();
$conn = $c->connectToMySQL();

// Lấy dữ liệu từ form
$name = trim($_POST['name']);
$message = trim($_POST['message']);

// Kiểm tra nếu người dùng đã đăng nhập hay chưa
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : trim($_POST['email']);

// Kiểm tra nếu các trường đã điền đầy đủ
if (!empty($name) && !empty($email) && !empty($message)) {
    // Kiểm tra định dạng email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Chuẩn bị truy vấn SQL để thêm tin nhắn
        $sql = "INSERT INTO contact_messages (name, email, message, customer_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssi", $name, $email, $message, $customer_id);

        // Thực hiện truy vấn
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        title: 'Success!',
                        text: 'Message sent successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'homepage.php';
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error: " . $stmt->error . "',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'homepage.php';
                    });
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid email format.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'homepage.php';
                });
              </script>";
    }
} else {
    echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Please Register to send messages.!',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'homepage.php';
            });
          </script>";
}

$conn->close();
?>
