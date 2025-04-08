<?php
session_start();
include('../includes/db.php');
include('../functions/order.php');

// Admin Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = ""; // For success/error messages

// --- Order Status Update Handling ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id_to_update = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    if (updateOrderStatus($conn, $order_id_to_update, $new_status)) {
        $message = "<p class='success'>Order status updated successfully.</p>";
    } else {
        $message = "<p class='error'>Failed to update order status.</p>";
    }
}

// --- Fetch All Orders ---
$orders = getAllOrders($conn);
$allowed_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled', 'refunded']; // Define statuses for dropdown

?>

<?php include('includes/admin_header.php'); ?>

    <div class="admin-orders container"><h2>Manage Orders</h2>

        <?php echo $message; // Display feedback messages ?>

        <table>
            <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Details</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($orders): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['username'] ?: ($order['email'] ?: 'User Deleted')); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($order['order_date']))); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status">
                                    <?php foreach ($allowed_statuses as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo ($order['status'] == $status) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($status); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </form>
                        </td>
                        <td>
                            <a href="order_details.php?id=<?php echo $order['id']; ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No orders found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include('includes/admin_footer.php'); // ?>