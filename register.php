<?php
session_start();
include('includes/db.php');
include('functions/auth.php');

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    // Get the selected privilege from the form
    $privilege = isset($_POST['privilege']) && $_POST['privilege'] === 'admin' ? 'admin' : 'customer'; // Default to customer for safety

    // Validation (keep existing validation)
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password)) {
        $error_message = "Password must be at least 8 characters long and contain both letters and numbers.";
    } elseif (!isEmailUnique($conn, $email)) { //
        $error_message = "Email already exists.";
    } elseif (!isUsernameUnique($conn, $username)) { //
        $error_message = "Username already exists.";
    } else {
        // Pass the selected privilege to createUser
        if (createUser($conn, $username, $email, $password, $privilege)) { //
            // Maybe redirect based on role or just to login
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Registration failed. Please try again.";
        }
    }
}

include('includes/header.php');
?>

    <div class="register-container">
        <h2>Register</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="privilege">Register as:</label>
                <select name="privilege" id="privilege" required>
                    <option value="customer" selected>Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

<?php include('includes/footer.php'); ?>