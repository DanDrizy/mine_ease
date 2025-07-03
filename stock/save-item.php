<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $type = $_POST['type'];
    $item_name = $conn->real_escape_string(trim($_POST['item_name']));
    $category = $conn->real_escape_string(trim($_POST['category']));
    $quantity = (int)$_POST['quantity'];
    $unit_price = (float)$_POST['unit_price'];
    $location = $conn->real_escape_string(trim($_POST['location']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    $total_value = $quantity * $unit_price;
    
    $sql = "UPDATE stock_items SET 
            item_name = ?, 
            category = ?, 
            quantity = ?, 
            unit_price = ?, 
            total_value = ?, 
            location = ?, 
            status = ?, 
            updated_at = NOW()
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddssi", $item_name, $category, $quantity, $unit_price, $total_value, $location, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating item: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>