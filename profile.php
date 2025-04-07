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
if (!$stmt) {
    die("Error preparing user retrieval: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

$error_message = "";
$success_message = "";

// Change password functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Validation
    if (empty($old_password) || empty($new_password)) {
        $error_message = "Both old and new passwords are required.";
    } elseif (strlen($new_password) < 8 || !preg_match('/[a-zA-Z]/', $new_password) || !preg_match('/\d/', $new_password)) {
        $error_message = "New password must be at least 8 characters long and contain both letters and numbers.";
    } elseif ($old_password === $new_password) {
        $error_message = "New password cannot be the same as the old password.";
    } else {
        // Verify the old password
        if (verifyUser($conn, $_SESSION['user_email'], $old_password) === $_SESSION['user_id']) {
            // Update the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error preparing password update: " . $conn->error);
            }
            $stmt->bind_param("si", $hashed_password, $user_id);
            if ($stmt->execute()) {
                $success_message = "Password changed successfully.";
            } else {
                $error_message = "Failed to change password. Please try again.";
            }
            $stmt->close();
        } else {
            $error_message = "Invalid old password.";
        }
    }
}

?>

    <div class="profile-container">
        <h2>My Profile</h2>
        <p>Username: <?php echo htmlspecialchars($username); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>

        <h3>Change Password</h3>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
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