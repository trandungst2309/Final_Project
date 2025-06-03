<?php
// session_start();
// ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorbike</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"
        integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1MA2VgR/t7JO3B1" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<style>
header {
    background-color: #1E1E24 !important;
    color: whitesmoke !important;
    border-bottom: 2px solid #e51515;
}

.icons:hover {
    color: red !important;
}

.bi-person-circle {
    color: whitesmoke !important;
}

.bi-person-circle:hover {
    color: red !important;
}

.bi-cart-check {
    color: whitesmoke !important;
}

.bi-cart-check:hover {
    color: red !important;
}

.bi-box-arrow-right {
    color: whitesmoke !important;
}

.bi-box-arrow-right:hover {
    color: red !important;
}

.bi-box-arrow-in-left {
    color: whitesmoke !important;
}

.bi-box-arrow-in-left:hover {
    color: red !important;
}

.search-container {
    position: relative;
    /* Position relative to allow absolute positioning of the button */
    display: flex;
    /* Use flexbox for alignment */
    align-items: center;
    /* Center items vertically */
    width: auto;
    /* Set a fixed width for the search container */
}


.search-input {
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 0px 20px;
    font-size: 16px;
    width: 50%;
    box-sizing: border-box;
}

.search-button {
    border: none;
    /* Remove the border */
    background: none;
    /* Remove the background */
    padding: 0;
    /* Remove padding */
    margin-left: -45px;
    /* Adjust as needed to align with input */
    cursor: pointer;
    /* Change cursor to pointer */
    display: flex;
    /* Use flex to center the icon if needed */
    align-items: center;
    /* Center the icon vertically */
}

.search-icon {
    width: 20px;
    /* Adjust the size as needed */
    height: 20px;
    /* Adjust the size as needed */
    display: block;
    /* Remove extra space around the image */
}

.login-image {
    width: 30px;
    /* Chỉnh kích thước hình ảnh */
    height: auto;
    cursor: pointer;
    /* Hiển thị con trỏ khi di chuột lên ảnh */
}

.login-image {
    width: 30px;
    /* Chỉnh kích thước hình ảnh */
    height: auto;
    cursor: pointer;
    /* Hiển thị con trỏ khi di chuột lên ảnh */
}

.nav-link {
    color: whitesmoke !important;
    font-size: 20px !important;
    margin-right: 20px !important;
}

.nav-link:hover {
    color: red !important;
    transition: 0.3s;
}

.nav-link-active {
    color: red !important;

}

.nav-link-active:hover {
    color: whitesmoke !important;
    transition: 0.3s;
}

.logo-text {
    font-size: 32px;
    font-weight: bold;
    color: whitesmoke;
    margin: 0;
    white-space: nowrap;
}
</style>

<body>
    <header>
        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <div class="d-flex align-items-center logo">
            <a href="homepage.php" class="d-flex align-items-center text-decoration-none">
                <img src="image/TDicon1.png" alt="TD Motor Logo" style="height: 70px;">
                <span class="logo-text ms-3 d-none d-md-inline">TD Motorshop</span>
            </a>
        </div>

        <nav class="navbar">
            <a href="homepage.php" class="nav-link-active">Home</a>
            <a href="homepage.php#product" class="nav-link">Products</a>
            <a href="contact.php" class="nav-link">Contact</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin.php" class="nav-link-active">Admin</a>
            <?php endif; ?>

            <form method="POST" action="search.php">
                <div class="search-container">
                    <input type="text" name="txtSearch" placeholder="Search..." class="search-input" required>
                    <button type="submit" class="search-button">
                        <img src="image/searchicon.png" alt="Search Icon" class="search-icon">
                    </button>
                </div>
            </form>
        </nav>
        <div class="icons">
            <?php
            if (isset($_SESSION['customer_id']) && isset($_SESSION['customer_name'])) {
                echo "<span class='me-2'>Hi, " . htmlspecialchars($_SESSION['customer_name']) . "</span>";

                if (!class_exists('Connect')) {
                    include_once 'connect.php';
                }
                $conn = new Connect();
                $db_link = $conn->connectToPDO();
                $sql = "SELECT profile_image FROM customer WHERE customer_id = ?";
                $stmt = $db_link->prepare($sql);
                $stmt->execute([$_SESSION['customer_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && !empty($user['profile_image'])) {
                    // Hiển thị ảnh nếu có
                    echo '<a href="profile.php"><img src="uploads/' . htmlspecialchars($user['profile_image']) . '" class="rounded-circle me-2" width="30" height="30" alt="Profile"></a>';
                } else {
                    // Icon nếu không có ảnh
                    echo '<a href="profile.php" class="bi bi-person-circle me-2 fs-3"></a>';
                }

                echo '<a href="cart.php" class="bi bi-cart-check me-3 fs-3"></a>';
                echo '<a href="logout.php" class="bi bi-box-arrow-right fs-3"></a>';

            } else {
                echo '<span class="me-2">Hi, Guest</span>';
                echo '<a href="login.php" class="bi bi-box-arrow-in-left fs-3"></a>';
            }

        ?>
        </div>
    </header>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-mQ93NGK02aC2TdE5lI1eV+Lv5F0O1iZsHb1z4LlY8Zy1pD+5T5En1RfuWDaP8f3p" crossorigin="anonymous">
    </script> -->