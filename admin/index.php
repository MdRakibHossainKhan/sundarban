<?php
session_start();
include('../includes/db.php');
include('includes/admin_header.php');
include('functions/auth.php');

// Check if admin is logged in (use verifyUser function)
if (!isset($_SESSION['user_id']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>

    <div class="admin-dashboard">
        <h2>Admin Dashboard</h2>
        <div class="stats">
            <?php
            // Example: Count total users
            $user_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
            echo "<p>Total Users: " . htmlspecialchars($user_count) . "</p>";

            // Add other statistics here (product count, order count, etc.)
            ?>
        </div>
        <div class="recent-orders">
            <h3>Recent Orders</h3>
            <?php
            // Display a table of recent orders
            $orders = $conn->query("SELECT id, order_date, total_amount FROM orders ORDER BY order_date DESC LIMIT 5");
            if ($orders->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Order ID</th><th>Date</th><th>Amount</th></tr>";
                while ($row = $orders->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td>$" . htmlspecialchars(number_format($row['total_amount'], 2)) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No recent orders.</p>";
            }
            ?>
        </div>
        <div class="admin-links">
            <a href="products.php">Manage Products</a>
            <a href="users.php">Manage Users</a>
            <a href="orders.php">Manage Orders</a>
        </div>
    </div>

<?php include('includes/footer.php'); ?>