<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product_details.php">Product Details</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>