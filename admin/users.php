<?php
session_start();
include('../includes/db.php'); //
// Assuming you create an admin-specific header/nav
// include('includes/admin_header.php');
// include('includes/admin_nav.php');
include('../functions/auth.php'); //

// Admin Check: Ensure only admins can access this page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: ../login.php"); // Redirect non-admins
    exit();
}

$current_user_id = $_SESSION['user_id']; // Get current admin's ID
$message = ""; // For success/error messages

// Handle Form Submissions (Privilege Update / Deletion)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update Privilege
    if (isset($_POST['update_privilege']) && isset($_POST['user_id']) && isset($_POST['privilege'])) {
        $user_id_to_update = intval($_POST['user_id']);
        $new_privilege = $_POST['privilege'];

        // Prevent admin from changing their own privilege here
        if ($user_id_to_update === $current_user_id) {
            $message = "<p class='error'>Cannot change your own privilege.</p>";
        } elseif (updateUserPrivilege($conn, $user_id_to_update, $new_privilege)) {
            $message = "<p class='success'>User privilege updated successfully.</p>";
        } else {
            $message = "<p class='error'>Failed to update user privilege.</p>";
        }
    } // Delete User
    elseif (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
        $user_id_to_delete = intval($_POST['user_id']);

        // Prevent admin from deleting themselves
        if ($user_id_to_delete === $current_user_id) {
            $message = "<p class='error'>Cannot delete your own account.</p>";
        } elseif (deleteUser($conn, $user_id_to_delete)) {
            $message = "<p class='success'>User deleted successfully.</p>";
        } else {
            $message = "<p class='error'>Failed to delete user. Check if user has associated orders or other constraints.</p>";
        }
    }
}

// Fetch all users
$users = getAllUsers($conn);

?>

<?php include('../includes/header.php'); /* Replace with admin header if you have one */ ?>
<?php include('../includes/nav.php'); /* Replace with admin nav if you have one */ ?>

    <div class="admin-users container"><h2>Manage Users</h2>

        <?php echo $message; // Display feedback messages ?>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Privilege</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="privilege" <?php echo ($user['id'] === $current_user_id) ? 'disabled' : ''; ?>>
                                    <option value="customer" <?php echo ($user['privilege'] == 'customer') ? 'selected' : ''; ?>>
                                        Customer
                                    </option>
                                    <option value="admin" <?php echo ($user['privilege'] == 'admin') ? 'selected' : ''; ?>>
                                        Admin
                                    </option>
                                </select>
                                <button type="submit"
                                        name="update_privilege" <?php echo ($user['id'] === $current_user_id) ? 'disabled' : ''; ?>>
                                    Update
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="post" style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user"
                                        class="remove-button" <?php echo ($user['id'] === $current_user_id) ? 'disabled' : ''; ?>>
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include('../includes/footer.php'); // ?>