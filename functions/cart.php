<?php

// Include product functions to get stock levels
include_once('product.php'); // Use include_once to prevent re-declaration issues

function getCartItems($conn, $user_id) {
    // ... (existing code) ...
    $query = "SELECT c.id, c.product_id, c.quantity, p.name, p.price
              FROM carts c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Error preparing cart item retrieval: " . $conn->error); // Log error
        return false;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $items;
}

function addToCart($conn, $user_id, $product_id, $quantity_to_add) {
    // --- START STOCK CHECK ---
    // Get available product stock
    $product = getProductById($conn, $product_id);
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found.'];
    }
    $available_stock = $product['quantity'];

    // Get current quantity in cart for this product
    $current_cart_quantity = 0;
    $query_cart = "SELECT quantity FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt_cart = $conn->prepare($query_cart);
    if (!$stmt_cart) {
        error_log("Error preparing cart quantity check: " . $conn->error);
        return ['success' => false, 'message' => 'Error checking cart.'];
    }
    $stmt_cart->bind_param("ii", $user_id, $product_id);
    $stmt_cart->execute();
    $stmt_cart->bind_result($existing_quantity);
    if ($stmt_cart->fetch()) {
        $current_cart_quantity = $existing_quantity;
    }
    $stmt_cart->close();

    // Calculate total desired quantity
    $total_desired_quantity = $current_cart_quantity + $quantity_to_add;

    // Check if desired quantity exceeds available stock
    if ($total_desired_quantity > $available_stock) {
        $can_add = $available_stock - $current_cart_quantity;
        $message = "Cannot add " . htmlspecialchars($quantity_to_add) . " item(s). Only " . htmlspecialchars($available_stock) . " available in stock";
        if ($can_add > 0) {
            $message .= " (you already have " . htmlspecialchars($current_cart_quantity) . " in cart, can add " . htmlspecialchars($can_add) . " more).";
        } else {
            $message .= " (you already have " . htmlspecialchars($current_cart_quantity) . " in cart).";
        }
        return ['success' => false, 'message' => $message];
    }
    // --- END STOCK CHECK ---


    // Proceed with adding/updating cart if stock is sufficient
    $final_result = false; // Track overall success

    // Check if the item is already in the cart (reuse $current_cart_quantity logic)
    if ($current_cart_quantity > 0) {
        // Item exists, update quantity
        $new_quantity = $total_desired_quantity; // Already calculated
        $query = "UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing cart update: " . $conn->error);
            return ['success' => false, 'message' => 'Database error during cart update.'];
        }
        $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $final_result = $stmt->execute();
        $stmt->close();
    } else {
        // Item doesn't exist, insert
        $query = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing cart insert: " . $conn->error);
            return ['success' => false, 'message' => 'Database error during cart insert.'];
        }
        // Use the quantity being added ($quantity_to_add) for the insert
        $stmt->bind_param("iii", $user_id, $quantity_to_add, $product_id);
        $final_result = $stmt->execute();
        $stmt->close();
    }

    if ($final_result) {
        return ['success' => true, 'message' => 'Product added to cart.'];
    } else {
        error_log("Failed to execute cart update/insert for user $user_id, product $product_id");
        return ['success' => false, 'message' => 'Failed to update cart.'];
    }
}


function removeFromCart($conn, $user_id, $product_id) {
    // ... (existing code) ...
    $query = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Error preparing cart delete: " . $conn->error);
        return false;
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function clearCart($conn, $user_id) {
    // ... (existing code) ...
    $query = "DELETE FROM carts WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Error preparing clear cart: " . $conn->error);
        return false;
    }
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function calculateCartTotal($conn, $user_id) {
    // ... (existing code) ...
    $query = "SELECT SUM(c.quantity * p.price) AS total_price
              FROM carts c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Error preparing cart total calculation: " . $conn->error);
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