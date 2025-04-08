<?php
// Ensure session is started, needed for potential checks later if required
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="admin-navigation" style="background-color: #444; padding: 10px 0; margin-bottom: 20px;">
    <ul style="list-style: none; padding: 0; margin: 0; display: flex; justify-content: center;">
        <li style="margin: 0 15px;"><a
                    style="color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px;"
                    href="index.php">Dashboard</a></li>
        <li style="margin: 0 15px;"><a
                    style="color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px;"
                    href="products.php">Manage Products</a></li>
        <li style="margin: 0 15px;"><a
                    style="color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px;"
                    href="orders.php">Manage Orders</a></li>
        <li style="margin: 0 15px;"><a
                    style="color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px;"
                    href="users.php">Manage Users</a></li>
        <li style="margin: 0 15px;"><a
                    style="color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px;"
                    href="feedback.php">View Feedback</a></li>
    </ul>
</nav>
<style>
    .admin-navigation a:hover {
        background-color: #555;
    }
</style>