<?php
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/product.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $product = getProductById($conn, $product_id);

    if ($product) {
        ?>
        <div class="product-view">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
            <p>Quantity: <?php echo htmlspecialchars($product['quantity']); ?></p>
        </div>
        <?php
    } else {
        echo "<p>Product not found.</p>";
    }
} else {
    echo "<p>Invalid product ID.</p>";
}

include('includes/footer.php');
?>