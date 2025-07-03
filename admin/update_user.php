<?php
// update_user.php - Handles user update operations

// Database connection
require_once '../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$userType = isset($_POST['user_type']) ? $_POST['user_type'] : '';

// Validation
if ($userId <= 0 || !in_array($userType, ['admin', 'stakeholder', 'hr', 'employee'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid user data']);
    exit;
}

// Sanitize input
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$site = mysqli_real_escape_string($conn, $_POST['site']);
$department = mysqli_real_escape_string($conn, $_POST['department']);
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Check if email is unique (excluding current user)
$checkEmailSql = "SELECT id FROM users WHERE email = ? AND id != ?";
$checkStmt = $conn->prepare($checkEmailSql);
$checkStmt->bind_param("si", $email, $userId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Begin transaction
$conn->begin_transaction();

try {
    // Update user data
    if (!empty($password)) {
        // Update with new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET 
                name = ?, 
                email = ?, 
                site = ?, 
                department = ?, 
                password = ? 
                WHERE id = ? AND user_type = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $name, $email, $site, $department, $hashedPassword, $userId, $userType);
    } else {
        // Update without changing password
        $sql = "UPDATE users SET 
                name = ?, 
                email = ?, 
                site = ?, 
                department = ?
                WHERE id = ? AND user_type = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $site, $department, $userId, $userType);
    }
    
    $success = $stmt->execute();
    $stmt->close();
    
    // Update HR specialization if applicable
    if ($userType === 'hr' && isset($_POST['specialization'])) {
        $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
        
        // Check if HR record exists
        $checkHrSql = "SELECT user_id FROM hr_details WHERE user_id = ?";
        $checkHrStmt = $conn->prepare($checkHrSql);
        $checkHrStmt->bind_param("i", $userId);
        $checkHrStmt->execute();
        $hrResult = $checkHrStmt->get_result();
        $checkHrStmt->close();
        
        if ($hrResult->num_rows > 0) {
            // Update existing HR record
            $updateHrSql = "UPDATE hr_details SET specialization = ? WHERE user_id = ?";
            $updateHrStmt = $conn->prepare($updateHrSql);
            $updateHrStmt->bind_param("si", $specialization, $userId);
            $updateHrStmt->execute();
            $updateHrStmt->close();
        } else {
            // Insert new HR record
            $insertHrSql = "INSERT INTO hr_details (user_id, specialization) VALUES (?, ?)";
            $insertHrStmt = $conn->prepare($insertHrSql);
            $insertHrStmt->bind_param("is", $userId, $specialization);
            $insertHrStmt->execute();
            $insertHrStmt->close();
        }
    }
    
    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>