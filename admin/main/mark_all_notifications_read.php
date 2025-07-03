<?php
// Include your database connection file
include '../../config.php'; // Replace with your actual path

// Prepare and execute the query to mark all notifications as read
$query = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
$result = $conn->query($query);

if($result) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}

$conn->close();
?>