<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/cart.php');
include('functions/product.php');

// Ensure user is logged in to view the cart (basic security)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = getCartItems($conn, $user_id);
$total_price = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cart'])) {
        // Handle quantity updates (Implement this based on your needs)
    } elseif (isset($_POST['remove_item'])) {
        $product_id_to_remove = $_POST['product_id'];
        removeFromCart($conn, $user_id, $product_id_to_remove);
        header("Location: cart.php"); // Refresh cart
        exit();
    }
}
?>

    <div class="cart-container">
        <h2>Shopping Cart</h2>
        <?php if ($cart_items): ?>
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
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="product_id"
                                       value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                <button type="submit" name="remove_item">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total_price += $item['price'] * $item['quantity']; ?>
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
            <a href="checkout.php">Proceed to Checkout</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

<?php include('includes/footer.php'); ?>