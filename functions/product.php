<?php

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

function getProductById($conn, $id)
{
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function addProduct($conn, $name, $description, $price, $quantity)
{
    $query = "INSERT INTO products (name, description, price, quantity) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("ssdi", $name, $description, $price, $quantity);
    return $stmt->execute();
}

function updateProduct($conn, $id, $name, $description, $price, $quantity)
{
    $query = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("ssdii", $name, $description, $price, $quantity, $id);
    return $stmt->execute();
}

function deleteProduct($conn, $id)
{
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Or handle the error
    }
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

?>