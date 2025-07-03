<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $requestId = (int)$_POST['id'];
    
    // Verify the request exists and belongs to the current user
    $sql = "SELECT id FROM mining_requests WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit;
    }
    
    // Delete the request
    $sql = "DELETE FROM mining_requests WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting request: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>