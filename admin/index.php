<?php
session_start();
include('../includes/db.php');
include('../functions/auth.php');

// Admin Check: Ensure only admins can access this page
// Redirect to the main login page if not admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to main login page
    exit();
}

?>

<?php include('includes/admin_header.php'); ?>

    <div class="admin-dashboard container"><h2>Admin Dashboard</h2>
        <div class="stats">
            <?php
            if (isset($conn)) {
                $user_count_result = $conn->query("SELECT COUNT(*) FROM users");
                if ($user_count_result) {
                    $user_count = $user_count_result->fetch_row()[0];
                    echo "<p>Total Users: " . htmlspecialchars($user_count) . "</p>";
                } else {
                    echo "<p>Error fetching user count.</p>";
                }
                $pending_order_result = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
                if ($pending_order_result) {
                    $pending_count = $pending_order_result->fetch_row()[0];
                    echo "<p>Pending Orders: " . htmlspecialchars($pending_count) . "</p>";
                } else {
                    echo "<p>Error fetching pending orders count.</p>";
                }

            } else {
                echo "<p>Database connection not available.</p>";
            }
            ?>
        </div>
        <div class="recent-orders">
            <h3>Recent Orders</h3>
            <?php
            // Display a table of recent orders
            // Ensure $conn is available
            if (isset($conn)) {
                $orders_result = $conn->query("SELECT o.id, u.username, o.order_date, o.total_amount, o.status FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 5");
                if ($orders_result && $orders_result->num_rows > 0) {
                    echo "<table>";
                    echo "<thead><tr><th>Order ID</th><th>User</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>";
                    echo "<tbody>";
                    while ($row = $orders_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars(date('Y-m-d H:i', strtotime($row['order_date']))) . "</td>";
                        echo "<td>$" . htmlspecialchars(number_format($row['total_amount'], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars(ucfirst($row['status'])) . "</td>";
                        echo "<td><a href='order_details.php?id=" . htmlspecialchars($row['id']) . "'>View</a></td>"; // Link to details page
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No recent orders.</p>";
                }
            } else {
                echo "<p>Database connection not available.</p>";
            }
            ?>
        </div>
    </div>

<?php
include('includes/admin_footer.php');
?>