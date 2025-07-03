<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Announcement ID is required']);
    exit;
}

$announcement_id = (int)$_GET['id'];

try {
    // Get announcement details
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Announcement not found']);
        exit;
    }
    
    $announcement = $result->fetch_assoc();
    
    // Check for forward message and admin response in announcement_forwards table
    $forward_stmt = $conn->prepare("
        SELECT user_comment, admin_response, admin_response_date, status, created_at as forward_date
        FROM announcement_forwards 
        WHERE announcement_id = ? 
        ORDER BY created_at DESC Limit 1
    ");
    $forward_stmt->bind_param("i", $announcement_id);
    $forward_stmt->execute();
    $forward_result = $forward_stmt->get_result();
    
    $user_comment = null;
    $admin_response = null;
    $admin_response_date = null;
    $response_status = null;
    $forward_date = null;
    
    if ($forward_result->num_rows > 0) {
        $forward_data = $forward_result->fetch_assoc();
        $user_comment = $forward_data['user_comment'];
        $admin_response = $forward_data['admin_response'];
        $admin_response_date = $forward_data['admin_response_date'];
        $response_status = $forward_data['status'];
        $forward_date = $forward_data['forward_date'];
    }
    
    // Prepare response data
    $response_data = [
        'id' => $announcement['id'],
        'title' => $announcement['title'],
        'content' => $announcement['content'],
        'type' => $announcement['type'],
        'location' => $announcement['location'],
        'created_at' => $announcement['created_at'],
        'updated_at' => $announcement['updated_at'],
        'user_comment' => $user_comment,
        'forward_date' => $forward_date,
        'admin_response' => $admin_response,
        'admin_response_date' => $admin_response_date,
        'response_status' => $response_status,
        'has_response' => !empty($admin_response),
        'has_forward' => !empty($user_comment)
    ];
    
    echo json_encode($response_data);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>