<?php
require_once '../config.php';

header('Content-Type: application/json');

// Allow both POST with _method=DELETE and actual DELETE requests
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
    $method = 'DELETE';
}

if ($method === 'DELETE') {
    // Get the ID from either POST or php://input
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    
    if (!$id) {
        // Try to parse JSON input if sent
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
    }

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit;
    }

    // Start transaction for safety
    $conn->begin_transaction();

    try {
        // First, get item details for logging/notification
        $sql = "SELECT item_name FROM stock_items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }
        
        $item = $result->fetch_assoc();
        $item_name = $item['item_name'];

        // Then delete the item
        $sql = "DELETE FROM stock_items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Also delete any related records (adjust based on your database schema)
        // Example: $conn->query("DELETE FROM stock_history WHERE item_id = $id");

        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => "Item '$item_name' deleted successfully",
            'item_name' => $item_name
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Error deleting item: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>