<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/cart.php');
include('functions/product.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = getCartItems($conn, $user_id);
$total_price = calculateCartTotal($conn, $user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cart'])) {
        // ... existing code for updating cart ...
    } elseif (isset($_POST['remove_item'])) {
        // ... existing code for removing item ...
    } elseif (isset($_POST['add_to_cart'])) { // <<< ADD THIS BLOCK
        $product_id_to_add = $_POST['product_id'];
        $quantity_to_add = $_POST['quantity'];
        // Basic validation (you might want more robust validation)
        if (is_numeric($product_id_to_add) && is_numeric($quantity_to_add) && $quantity_to_add > 0) {
            if (!isset($_SESSION['user_id'])) { // Ensure user is logged in
                header("Location: login.php"); // Redirect to login if not logged in
                exit();
            }
            $user_id = $_SESSION['user_id'];
            // Call the function from functions/cart.php
            addToCart($conn, $user_id, $product_id_to_add, $quantity_to_add);
            // Redirect back to the cart page (or maybe the previous page)
            header("Location: cart.php");
            exit();
        } else {
            // Handle invalid input if necessary
            echo "Invalid product data."; // Or redirect with an error message
            exit();
        }
    }
}

?>

    <div class="cart-container">
        <h2>Shopping Cart</h2>
        <?php if ($cart_items): ?>
            <form method="post">
                <table>
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php
                                $product = getProductById($conn, $item['product_id']);
                                if ($product) {
                                    echo htmlspecialchars($product['name']);
                                } else {
                                    echo "Product Not Found"; // Handle missing product
                                }
                                ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo htmlspecialchars($item['product_id']); ?>]"
                                       value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                            </td>
                            <td>$<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                            <td>
                                <button type="submit" name="remove_item" class="remove-button">Remove</button>
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" align="right"><strong>Total:</strong></td>
                        <td>$<?php echo htmlspecialchars(number_format($total_price, 2)); ?></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
                <button type="submit" name="update_cart">Update Cart</button>
                <a href="checkout.php">Proceed to Checkout</a>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

<?php include('includes/footer.php'); ?>