<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $product_name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $expected_price = $_POST['expected_price'] ?? 0;
    $preferred_delivery_date = $_POST['preferred_delivery_date'] ?? null;

    // Validate required fields
    if (!$customer_id || !$product_name || !$expected_price || !$preferred_delivery_date) {
        die('Missing required information.');
    }

    // Handle image upload if provided
    $product_image = null;

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['product_image']['tmp_name'];
        $fileName = basename($_FILES['product_image']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($fileExt), $allowedExt)) {
            // Create upload folder if not exists
            $uploadDir = 'uploads/preorders/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = uniqid('preorder_', true) . '.' . $fileExt;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                $product_image = $newFileName;
            } else {
                die('Failed to upload image.');
            }
        } else {
            die('Unsupported image format.');
        }
    }

    // Insert into database
    try {
        $conn = new Connect();
        $db = $conn->connectToPDO();

        $query = "INSERT INTO preorder 
                    (customer_id, product_name, description, product_image, expected_price, preferred_delivery_date)
                  VALUES 
                    (:customer_id, :product_name, :description, :product_image, :expected_price, :preferred_delivery_date)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':customer_id' => $customer_id,
            ':product_name' => $product_name,
            ':description' => $description,
            ':product_image' => $product_image,
            ':expected_price' => $expected_price,
            ':preferred_delivery_date' => $preferred_delivery_date
        ]);

        header("Location: preorder_confirmation.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: preorder.php");
    exit;
}
?>
