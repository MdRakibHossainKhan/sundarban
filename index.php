<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/product.php');
include('functions/cart.php'); // If you need cart functions here
?>

    <div class="content">
        <h2>Featured Products</h2>
        <div class="products">
            <?php
            $products = getProducts($conn);
            if ($products) {
                foreach ($products as $product) {
                    echo "<div class='product'>";
                    echo "<h3>" . htmlspecialchars($product['name']) . "</h3>";
                    echo "<p>Price: $" . htmlspecialchars($product['price']) . "</p>";
                    echo "<a href='product.php?id=" . htmlspecialchars($product['id']) . "'>View</a>";
                    echo "<a href='cart.php?action=add&product_id=" . htmlspecialchars($product['id']) . "'>Add to Cart</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </div>

<?php include('includes/footer.php'); ?>