<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/cart.php'); // Includes the modified addToCart
// include('functions/product.php'); // product functions already included via cart.php

$cart_message = ''; // To display feedback

// Ensure user is logged in for cart operations that require it
if (($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) ||
    !isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['user_id'])) {
        // Store intended action/product if possible, then redirect
        // For simplicity, just redirecting to login for now
        header("Location: login.php");
        exit();
    }
}

$user_id = $_SESSION['user_id']; // Assume user is logged in now


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cart'])) {
        // ... (existing code for updating cart - consider adding stock checks here too!) ...
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if (is_numeric($quantity) && $quantity > 0) {
                // TODO: Add stock check before updating quantity
                // Similar logic as in addToCart: check if $quantity <= available stock
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
        // ... (existing code for removing item) ...
        $product_id_to_remove = $_POST['product_id'];
        if (removeFromCart($conn, $user_id, $product_id_to_remove)) {
            $cart_message = "<p class='success'>Item removed successfully.</p>";
        } else {
            $cart_message = "<p class='error'>Failed to remove item.</p>";
        }
        // Don't redirect here if displaying message on the same page

    } elseif (isset($_POST['add_to_cart'])) {
        $product_id_to_add = $_POST['product_id'];
        $quantity_to_add = $_POST['quantity'];

        if (is_numeric($product_id_to_add) && ctype_digit(strval($quantity_to_add)) && $quantity_to_add > 0) {
            // Call the modified addToCart function
            $result = addToCart($conn, $user_id, $product_id_to_add, $quantity_to_add);

            // Set message based on the result
            if ($result['success']) {
                $cart_message = "<p class='success'>" . htmlspecialchars($result['message']) . "</p>";
            } else {
                $cart_message = "<p class='error'>" . htmlspecialchars($result['message']) . "</p>";
            }
            // Redirect back to the cart page (or originating page) after setting message
            // Using session flash message is better, but for simplicity:
            $_SESSION['cart_message'] = $cart_message; // Store message in session
            header("Location: cart.php"); // Redirect to show the message
            exit();

        } else {
            $_SESSION['cart_message'] = "<p class='error'>Invalid product data provided.</p>";
            header("Location: cart.php"); // Redirect back
            exit();
        }
    }
}

// Retrieve cart message from session if redirected
if (isset($_SESSION['cart_message'])) {
    $cart_message = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']); // Clear message after displaying
}


// Fetch cart items and total AFTER potential modifications
$cart_items = getCartItems($conn, $user_id);
$total_price = calculateCartTotal($conn, $user_id);

?>

    <div class="cart-container">
        <h2>Shopping Cart</h2>

        <?php echo $cart_message; ?>

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
                                // Fetch product details again for display consistency
                                $product = getProductById($conn, $item['product_id']);
                                if ($product) {
                                    echo htmlspecialchars($product['name']);
                                    // Optional: Display available stock?
                                    // echo " (Stock: " . htmlspecialchars($product['quantity']) . ")";
                                } else {
                                    echo "Product Not Found";
                                }
                                ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                            <td>
                                <input type="number"
                                       name="quantity[<?php echo htmlspecialchars($item['product_id']); ?>]"
                                       value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1"
                                    <?php if ($product) echo 'max="' . htmlspecialchars($product['quantity']) . '"'; /* Add max attribute based on stock */ ?> >

                            </td>
                            <td>
                                $<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                            <td>
                                <button type="submit" name="remove_item" class="remove-button"
                                        value="<?php echo htmlspecialchars($item['product_id']); ?>">Remove
                                </button>
                                <input type="hidden" name="product_id"
                                       value="<?php echo htmlspecialchars($item['product_id']); ?>">
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
        <p style="margin-top: 20px;"><a href="index.php">&leftarrow; Continue Shopping</a></p>
    </div>

<?php include('includes/footer.php'); ?>