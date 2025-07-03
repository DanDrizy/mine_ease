<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $item_name = $conn->real_escape_string(trim($_POST['item_name']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $serial_number = $conn->real_escape_string(trim($_POST['serial_number']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    $department_id = (int)$_POST['department_id'];
    $notes = $conn->real_escape_string(trim($_POST['notes']));
    
    $sql = "UPDATE equipment SET 
            item_name = ?,
            description = ?,
            serial_number = ?,
            location = ?,
            department_id = ?,
            notes = ?,
            last_updated = NOW()
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisi", $item_name, $description, $serial_number, $location, $department_id, $notes, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Equipment updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating equipment: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>