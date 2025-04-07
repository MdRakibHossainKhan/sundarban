<?php
session_start();
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/auth.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
$query = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Change password functionality (as in previous example)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    // ... (Password change logic - see previous example)
}

?>

    <div class="profile-container">
        <h2>My Profile</h2>
        <p>Username: <?php echo htmlspecialchars($username); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>

        <h3>Change Password</h3>
        <?php if (isset($error_message)) echo "<p class='error'><?php echo $error_message; ?></p>"; ?>
        <?php if (isset($success_message)) echo "<p class='success'><?php echo $success_message; ?></p>"; ?>
        <form method="post">
            <div>
                <label for="old_password">Old Password:</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>

<?php include('includes/footer.php'); ?>