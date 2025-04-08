<?php // Ensure session is started if not already done before including footer.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
</div>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h3>About Sundarban</h3>
            <p>This is a sample e-commerce website for learning purposes.</p>
        </div>
        <div class="footer-section contact">
            <h3>Contact Info</h3>
            <p>Phone: (123) 456-7890</p>
            <p>Email: info@sundarban.com</p>
            <p>Address: 123 Main St, Fredericton</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Sundarban. All rights reserved.</p>
    </div>
</footer>
</div> </body>
</html>