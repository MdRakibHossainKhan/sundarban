</div>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h3>About Sundarban</h3>
            <p>This is a sample e-commerce website for learning purposes. You can add more detailed information about
                your store here.</p>
        </div>
        <div class="footer-section links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">My Profile</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-section contact">
            <h3>Contact Info</h3>
            <p>Phone: (123) 456-7890</p>
            <p>Email: info@sundarban.com</p>
            <p>Address: 123 Main St, City</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Sundarban. All rights reserved.</p>
    </div>
</footer>
</div> </body>
</html>