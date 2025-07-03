<?php
// Include your database connection file
include '../../config.php'; // Replace with your actual path

// Check if the request is valid
if(isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Prepare and execute the query to mark notification as read
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
    
    $stmt->close();
} else {
    echo "error: Invalid notification ID";
}

$conn->close();
?>