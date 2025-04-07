<?php

function getAllFeedback($conn)
{
    // Fetch feedback, joining with users table to get username/email if available
    $query = "SELECT f.id, f.user_id, u.username, u.email, f.message, f.created_at
              FROM feedback f
              LEFT JOIN users u ON f.user_id = u.id
              ORDER BY f.created_at DESC";
    $result = $conn->query($query);
    $feedback_items = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $feedback_items[] = $row;
        }
    }
    return $feedback_items;
}

// Optional: Add a function to delete feedback if needed
function deleteFeedback($conn, $feedback_id)
{
    $query = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("i", $feedback_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>