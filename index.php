<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/product.php');
include('functions/cart.php');

?>

    <div class="content">
        <h2>Product List</h2>
        <div class="products">
            <?php
            $products = getProducts($conn);
            if ($products) {
                foreach ($products as $product) {
                    echo "<div class='product'>";
                    echo "<h3>" . htmlspecialchars($product['name']) . "</h3>";
                    echo "<p>Price: $" . htmlspecialchars(number_format($product['price'], 2)) . "</p>";
                    echo "<a href='product_details.php?id=" . htmlspecialchars($product['id']) . "'>View</a>";
                    ?>
                    <form method="post" action="cart.php">
                        <input type="number" name="quantity" value="1" min="1">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                    <?php
                    echo "</div>";
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </div>

<?php include('includes/footer.php'); ?>