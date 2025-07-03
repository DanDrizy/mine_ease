<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No announcement ID provided']);
    exit;
}

$announcement_id = intval($_GET['id']);

// First check if the announcement exists
$check_query = "SELECT id FROM announcements WHERE id = ?";
$check_stmt = $conn->prepare($check_query);

if (!$check_stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$check_stmt->bind_param("i", $announcement_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Announcement not found']);
    exit;
}

$check_stmt->close();

// Delete the announcement
$delete_query = "DELETE FROM announcements WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);

if (!$delete_stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$delete_stmt->bind_param("i", $announcement_id);

if ($delete_stmt->execute()) {
    if ($delete_stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No announcement was deleted']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting announcement: ' . $delete_stmt->error]);
}

$delete_stmt->close();
$conn->close();
?>