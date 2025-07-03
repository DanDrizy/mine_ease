<?php
// delete_user.php - Handles user deletion

// Database connection
require_once '../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check access permissions (you may want to add more checks here)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
}

// Get user ID and type from request
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userType = isset($_GET['type']) ? $_GET['type'] : '';

// Validation
if ($userId <= 0 || !in_array($userType, ['admin', 'stakeholder', 'hr', 'employee'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid user data']);
    exit;
}

// Don't allow deleting yourself
if ($userId === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // First delete related records if needed
    if ($userType === 'hr') {
        $deleteHrSql = "DELETE FROM hr_details WHERE user_id = ?";
        $deleteHrStmt = $conn->prepare($deleteHrSql);
        $deleteHrStmt->bind_param("i", $userId);
        $deleteHrStmt->execute();
        $deleteHrStmt->close();
    }
    
    // Now delete the user
    $deleteUserSql = "DELETE FROM users WHERE id = ? AND user_type = ?";
    $deleteUserStmt = $conn->prepare($deleteUserSql);
    $deleteUserStmt->bind_param("is", $userId, $userType);
    $deleteUserStmt->execute();
    
    // Check if user was actually deleted
    if ($deleteUserStmt->affected_rows === 0) {
        throw new Exception('User not found or could not be deleted');
    }
    
    $deleteUserStmt->close();
    
    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>