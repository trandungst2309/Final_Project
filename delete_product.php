<?php
include_once 'connect.php';
$conn = new Connect();
$db_link = $conn->connectToMySQL();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function confirmDelete(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                window.location.href = "delete_product.php?delete=" + productId;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_GET['delete'])) {
            $product_id = $_GET['delete'];
            $sql = "DELETE FROM product WHERE product_id = ?";
            $stmt = $db_link->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<div class='alert alert-success'>Product deleted successfully!</div>";
                header("Location: manage_products.php"); // Redirect back to manage_product page
                exit;
            } else {
                echo "<div class='alert alert-danger'>Error: You need to delete order before delete product or product not found.</div>";
            }
        } else {
            // Display all products
            $sql = "SELECT * FROM product";
            $result = $db_link->query($sql);

            if ($result->num_rows > 0) {
                echo "<h3>All Products:</h3>";
                echo "<table class='table table-striped'>";
                echo "<tr><th>Product Name</th><th>Product Type ID</th><th>Product Price</th><th>Product Image</th><th>Quantity</th><th>Action</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['product_name'] . "</td>";
                    echo "<td>" . $row['product_type_id'] . "</td>";
                    echo "<td>" . $row['product_price'] . "</td>";
                    echo "<td><img src='uploads/" . $row['product_img'] . "' alt='Product Image' style='width:100px; height:auto;'></td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>
                            <a href='add_product.php?edit=" . $row['product_id'] . "' class='btn btn-primary'>Edit</a>
                            <a href='javascript:void(0);' onclick='confirmDelete(" . $row['product_id'] . ")' class='btn btn-danger'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No products found";
            }
        }
        ?>
    </div>
</body>
</html>
