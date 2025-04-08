<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/cart.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = getCartItems($conn, $user_id);
$total_price = calculateCartTotal($conn, $user_id);

if (!$cart_items) {
    echo "<p>Your cart is empty. Cannot proceed to checkout.</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];

    // Store order details
    $query = "INSERT INTO orders (user_id, order_date, total_amount, payment_method, status) VALUES (?, NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing order insertion: " . $conn->error);
    }
    $status = 'pending'; // Or 'processing', etc.
    $stmt->bind_param("idss", $user_id, $total_price, $payment_method, $status);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Store order items
    foreach ($cart_items as $item) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing order item insertion: " . $conn->error);
        }
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart after successful order creation
    clearCart($conn, $user_id);

    echo "<p>Order placed successfully! (Dummy payment processed)</p>";
    // You would typically redirect to an order confirmation page here
}

?>

    <div class="checkout-container">
        <h2>Checkout</h2>
        <p>Total Amount: $<?php echo htmlspecialchars(number_format($total_price, 2)); ?></p>
        <form method="post">
            <label>Payment Method:</label>
            <select name="payment_method">
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="cash_on_delivery">Cash on Delivery</option>
            </select>
            <button type="submit">Place Order</button>
        </form>
    </div>

<?php include('includes/footer.php'); ?>