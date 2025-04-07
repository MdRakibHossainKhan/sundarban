<?php
session_start();
include('includes/db.php');
include('functions/auth.php');

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validation (you might want more robust validation)
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        if ($user = verifyUser($conn, $email, $password)) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_privilege'] = $user['privilege']; // Store privilege
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}

include('includes/header.php');
?>

    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>

<?php include('includes/footer.php'); ?>