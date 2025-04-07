<?php

function getCartItems($conn, $user_id) {
    $query = "SELECT c.id, c.product_id, c.quantity, p.name, p.price 
              FROM carts c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function addToCart($conn, $user_id, $product_id, $quantity) {
    // Check if the item is already in the cart
    $query = "SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($cart_id, $existing_quantity);
        $stmt->fetch();
        $stmt->close();
        $new_quantity = $existing_quantity + $quantity;
        $query = "UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $result = $stmt->execute();
    } else {
        // Item doesn't exist, insert
        $query = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $result = $stmt->execute();
    }

    $stmt->close();
    return $result;
}

function removeFromCart($conn, $user_id, $product_id) {
    $query = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}

function clearCart($conn, $user_id) {
    $query = "DELETE FROM carts WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

function calculateCartTotal($conn, $user_id) {
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
    return $row['total_price'] ?? 0;
}

?>