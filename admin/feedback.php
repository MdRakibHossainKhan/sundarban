<?php
session_start();
include('../includes/db.php');
include('../functions/feedback.php'); // Include the new feedback functions

// Admin Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = ""; // For success/error messages

// Handle Deletion (Optional)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_feedback'])) {
    $feedback_id_to_delete = intval($_POST['feedback_id']);
    if (deleteFeedback($conn, $feedback_id_to_delete)) {
        $message = "<p class='success'>Feedback deleted successfully.</p>";
    } else {
        $message = "<p class='error'>Failed to delete feedback.</p>";
    }
}


// Fetch all feedback
$feedback_items = getAllFeedback($conn);
?>

<?php include('includes/admin_header.php'); /* Replace with admin header */ ?>

    <div class="admin-feedback container">
        <h2>View Feedback</h2>

        <?php echo $message; ?>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($feedback_items): ?>
                <?php foreach ($feedback_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td>
                            <?php
                            if ($item['user_id']) {
                                echo htmlspecialchars($item['username'] ?: $item['email']); // Show username or email
                                echo " (ID: " . htmlspecialchars($item['user_id']) . ")";
                            } else {
                                echo "<i>Guest</i>"; // Indicate if feedback was from a non-logged-in user
                            }
                            ?>
                        </td>
                        <td><?php echo nl2br(htmlspecialchars($item['message'])); // Use nl2br to respect line breaks ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($item['created_at']))); ?></td>
                        <td>
                            <form method="post" style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                <input type="hidden" name="feedback_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_feedback" class="remove-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No feedback submitted yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

    </div>

<?php include('includes/admin_footer.php'); // ?>