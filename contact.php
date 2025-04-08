<?php
include('includes/db.php');
include('includes/header.php');
include('includes/nav.php');
include('functions/auth.php');

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $message = trim($_POST['message']); // Trim whitespace

    // Validation
    if (empty($message)) {
        $error_message = "Message is required.";
    } else {
        $query = "INSERT INTO feedback (user_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing feedback insertion: " . $conn->error);
        }
        $stmt->bind_param("is", $user_id, $message);
        if ($stmt->execute()) {
            $success_message = "Feedback submitted successfully.";
        } else {
            $error_message = "Failed to submit feedback. Please try again.";
        }
        $stmt->close();
    }
}

?>

    <div class="contact-container">
        <h2>Contact Us</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <form method="post">
            <div>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="4" cols="30" required></textarea>
            </div>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>

<?php include('includes/footer.php'); ?>