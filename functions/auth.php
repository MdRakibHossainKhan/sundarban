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
        $stmt->close();
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

?>