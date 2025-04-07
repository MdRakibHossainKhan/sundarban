<?php

function getAllOrders($conn)
{
    // Fetch orders, joining with users table to get username/email
    $query = "SELECT o.id, o.user_id, u.username, u.email, o.order_date, o.total_amount, o.payment_method, o.status
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.id
              ORDER BY o.order_date DESC";
    $result = $conn->query($query);
    $orders = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

function getOrderDetails($conn, $order_id)
{
    // Fetch main order details
    $query_order = "SELECT o.id, o.user_id, u.username, u.email, o.order_date, o.total_amount, o.payment_method, o.status
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?";
    $stmt_order = $conn->prepare($query_order);
    if (!$stmt_order) return false;
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $order_result = $stmt_order->get_result();
    $order = $order_result->fetch_assoc();
    $stmt_order->close();

    if (!$order) return false; // Order not found

    // Fetch order items
    $query_items = "SELECT oi.product_id, p.name AS product_name, oi.quantity, oi.price
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
    $stmt_items = $conn->prepare($query_items);
    if (!$stmt_items) return $order; // Return order details even if items fail
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    $items = $items_result->fetch_all(MYSQLI_ASSOC);
    $stmt_items->close();

    $order['items'] = $items; // Add items to the order array
    return $order;
}


function updateOrderStatus($conn, $order_id, $status)
{
    // Optional: Add validation for allowed status values
    $allowed_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'];
    if (!in_array($status, $allowed_statuses)) {
        return false; // Invalid status
    }

    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("si", $status, $order_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>