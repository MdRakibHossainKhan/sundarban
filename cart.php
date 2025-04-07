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
        // Handle quantity updates
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if (is_numeric($quantity) && $quantity > 0) {
                $query = "UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    die("Error preparing cart update: " . $conn->error);
                }
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        header("Location: cart.php"); // Refresh
        exit();
    } elseif (isset($_POST['remove_item'])) {
        $product_id_to_remove = $_POST['product_id'];
        removeFromCart($conn, $user_id, $product_id_to_remove);
        header("Location: cart.php"); // Refresh
        exit();
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