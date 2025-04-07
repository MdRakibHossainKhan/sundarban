<?php // Ensure session is started if not already done before including nav.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product_details.php">Product Details</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="contact.php">Contact Us</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="profile.php">My Profile</a></li>

            <?php // Check if the logged-in user is an Admin
            if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] === 'admin'): ?>
                <li><a href="admin/index.php">Admin Panel</a></li> <?php endif; ?>

            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>