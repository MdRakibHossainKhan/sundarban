<?php

function createUser($conn, $username, $email, $password, $privilege = 'customer')
{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, password, privilege) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error appropriately
    }
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $privilege);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function verifyUser($conn, $email, $password)
{
    $query = "SELECT id, password, privilege FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error appropriately
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $hashed_password, $user_privilege);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            return ['user_id' => $user_id, 'privilege' => $user_privilege];
        }
    }
    $stmt->close();
    return false;
}

function isEmailUnique($conn, $email)
{
    $query = "SELECT COUNT(*) FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error appropriately
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count == 0;
}

function isUsernameUnique($conn, $username)
{
    $query = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error appropriately
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count == 0;
}

function getAllUsers($conn)
{
    $query = "SELECT id, username, email, privilege FROM users ORDER BY created_at DESC";
    $result = $conn->query($query);
    $users = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

function updateUserPrivilege($conn, $user_id, $privilege)
{
    // Ensure the privilege is either 'admin' or 'customer'
    if ($privilege !== 'admin' && $privilege !== 'customer') {
        return false; // Invalid privilege value
    }
    $query = "UPDATE users SET privilege = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("si", $privilege, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Note: Deleting users can have cascading effects if orders are linked.
// Consider adding constraints or logic to handle this (e.g., anonymize user, disallow deletion if orders exist).
function deleteUser($conn, $user_id)
{
    // Add checks here if needed (e.g., prevent deleting the last admin)

    // Before deleting the user, you might need to handle related data in other tables
    // (e.g., set user_id to NULL in orders, feedback, carts, or delete them if appropriate)
    // Example: DELETE FROM feedback WHERE user_id = ?; DELETE FROM carts WHERE user_id = ?; etc.
    // For orders, you usually wouldn't delete them, maybe set user_id to NULL or a dummy 'deleted user' ID.

    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>