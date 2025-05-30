<?php
$conn = new mysqli('localhost', 'root', '', 'motorbike');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['customer_id']) && isset($_POST['role'])) {
    $customer_id = $_POST['customer_id'];
    $role = $_POST['role'];
    $sql = "UPDATE customer SET role = ? WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $role, $customer_id);

    if ($stmt->execute()) {
        echo "Role updated successfully";
    } else {
        echo "Error updating role: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();

header("Location: manage_account.php");
exit();
?>
