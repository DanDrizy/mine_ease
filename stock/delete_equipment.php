<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // First verify the equipment exists
    $check_sql = "SELECT id FROM equipment WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Equipment not found']);
        exit;
    }
    
    // Delete the equipment
    $sql = "DELETE FROM equipment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Equipment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting equipment: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>