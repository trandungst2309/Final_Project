<?php
include 'connect.php'; // Include the database connection file
session_start();
?>

<?php
include_once 'header.php';
?>

<br><br><br><br><br><br><br>

<?php
// Check if the user is logged in and pass this information to JavaScript
$isLoggedIn = isset($_SESSION['customer_id']) ? 'true' : 'false';
?>

<script>
var isLoggedIn = <?= json_encode($isLoggedIn) ?>; // Pass PHP variable to JavaScript
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['txtSearch'])) {
    include_once("connect.php");

    $nameP = $_POST['txtSearch'];

    $conn = new Connect();
    $db_link = $conn->connectToPDO();

    $sql = "SELECT * FROM product WHERE product_name LIKE ?";
    $stmt = $db_link->prepare($sql);
    $n = "%$nameP%";
    $stmt->execute([$n]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<div class='container'>";
    if (count($results) > 0) {
        echo "<h2 style='font-weight:bold'>Results for " . htmlspecialchars($nameP) . "</h2>";
        echo "<div class='row justify-content-center'>";
        foreach ($results as $r) {
?>
<div class='col-md-4 mb-4' style="padding: 10px;">
    <div class='card' style="text-align: center; padding: 20px;">
        <img src='./uploads/<?= htmlspecialchars($r["product_img"]) ?>'
            alt='<?= htmlspecialchars($r["product_name"]) ?>' class='card-img-top' />
        <div class="card-body">
            <a href="product_detail.php?product_id=<?= htmlspecialchars($r['product_id']) ?>"
                class="text-decoration-none">
                <h5 class="card-title"><?= htmlspecialchars($r['product_name']) ?></h5>
            </a>
            <h6 class="product-price"><span>$</span> <?= number_format(htmlspecialchars($r['product_price'])) ?></h6>
            <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($r['product_id']) ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($r['product_name']) ?>">
                <input type="hidden" name="product_price" value="<?= htmlspecialchars($r['product_price']) ?>">
                <input type="hidden" name="product_img" value="<?= htmlspecialchars($r['product_img']) ?>">
                <button type="submit" name="add_to_cart" class="btn btn-add-to-cart">Add to Order</button>
                <a href='product_detail.php?product_id=<?= htmlspecialchars($r['product_id']) ?>'
                    class='btn btn-view-details'>View Detail</a>
            </form>
        </div>
    </div>
</div>
<?php
        }
        echo "</div>";
    } else {
        echo "<h2 style='color:red; font-weight:bold; text-align:center; padding: 20px'>Opps! There are no results found for '$nameP'</h2>";
    }
    echo "</div>";
}
?>

<?php include_once 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Include FontAwesome for search icon -->

<script>
// JavaScript to hide/show "Add to Order" button based on login status
document.addEventListener('DOMContentLoaded', function() {
    if (isLoggedIn === 'false') {
        // Hide the "Add to Order" button if not logged in
        document.querySelectorAll('.btn-add-to-cart').forEach(function(button) {
            button.style.display = 'none';
        });
    }
});
</script>