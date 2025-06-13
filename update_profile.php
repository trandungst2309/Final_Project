<?php
session_start();
include_once 'connect.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$conn = new Connect();
$db_link = $conn->connectToPDO();

$query = "SELECT * FROM customer WHERE customer_id = :customer_id";
$stmt = $db_link->prepare($query);
$stmt->execute([':customer_id' => $customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer not found.";
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $errors[] = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    $profile_image = $customer['profile_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_type = mime_content_type($file_tmp);

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, GIF files are allowed for profile image.";
        } else {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $customer_id . '_' . time() . '.' . $ext;
            $destination = __DIR__ . '/uploads/' . $new_filename;

            if (move_uploaded_file($file_tmp, $destination)) {
                if ($profile_image && file_exists(__DIR__ . '/uploads/' . $profile_image) && $profile_image !== 'avatars/default-avatar.png') {
                    unlink(__DIR__ . '/uploads/' . $profile_image);
                }
                $profile_image = $new_filename;
            } else {
                $errors[] = "Failed to upload profile image.";
            }
        }
    }

    if (empty($errors)) {
        $update_query = "UPDATE customer 
                         SET customer_name = :name, email = :email, phone = :phone, address = :address, profile_image = :profile_image 
                         WHERE customer_id = :customer_id";
        $update_stmt = $db_link->prepare($update_query);
        $result = $update_stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':profile_image' => $profile_image,
            ':customer_id' => $customer_id
        ]);

        if ($result) {
            $_SESSION['alertMessage'] = "Profile updated successfully!";
            header('Location: profile.php');
            exit();
        } else {
            $errors[] = "Failed to update profile in database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once 'header.php'; ?>

    <div class="container my-5" style="padding-top: 150px;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="mb-4" style="color: red; font-weight:bold">Update Profile</h2>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form action="update_profile.php" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="mb-3 text-center">
                        <img id="preview-image" src="<?= !empty($customer['profile_image']) && file_exists('./uploads/' . $customer['profile_image']) 
                            ? './uploads/' . htmlspecialchars($customer['profile_image']) 
                            : './uploads/default.png' ?>" alt="Profile Image"
                            class="rounded-circle border border-danger border-3 object-fit-cover"
                            style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required
                            value="<?= htmlspecialchars($customer['customer_name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required
                            value="<?= htmlspecialchars($customer['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required
                            value="<?= htmlspecialchars($customer['phone']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required
                            value="<?= htmlspecialchars($customer['address']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image"
                            accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-danger">Save Changes</button>
                    <a href="profile.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script>
    const fileInput = document.getElementById('profile_image');
    const previewImage = document.getElementById('preview-image');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            previewImage.src = URL.createObjectURL(file);
        }
    });
    </script>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>