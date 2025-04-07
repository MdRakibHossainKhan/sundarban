<?php

$con = mysqli_connect("localhost", "root", "", "sundarban");

if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Function to get the user's IP address (for basic cart association)
function getIp()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $ip;
}

// Function to create a new user
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

// Function to verify user credentials
function verifyUser($conn, $email, $password)
{
    $query = "SELECT id, password, privilege FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
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

// Function to check if an email is unique
function isEmailUnique($conn, $email)
{
    $query = "SELECT COUNT(*) FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count == 0;
}

// Function to check if a username is unique
function isUsernameUnique($conn, $username)
{
    $query = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count == 0;
}

// Function to get all products
function getProducts($conn)
{
    $query = "SELECT * FROM products";
    $result = $conn->query($query);
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Function to get a single product by ID
function getProductById($conn, $id)
{
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to add a product to the cart
function addToCart($conn, $user_id, $product_id, $quantity)
{
    // Check if the item is already in the cart
    $query = "SELECT id FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Item exists, update quantity
        $query = "UPDATE carts SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $result = $stmt->execute();
    } else {
        // Item doesn't exist, insert
        $query = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $result = $stmt->execute();
    }

    $stmt->close();
    return $result;
}

// Function to remove a product from the cart
function removeFromCart($conn, $user_id, $product_id)
{
    $query = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}

// Function to clear the entire cart for a user
function clearCart($conn, $user_id)
{
    $query = "DELETE FROM carts WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

// Function to calculate the total price of items in the cart
function calculateCartTotal($conn, $user_id)
{
    $query = "SELECT SUM(c.quantity * p.price) AS total_price
              FROM carts c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return 0; // Or handle the error
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['total_price'] ?? 0; // Use null coalescing to avoid errors if total is NULL
}

?>