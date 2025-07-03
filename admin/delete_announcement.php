<?php
include '../config.php';

// Check if ID parameter exists and is valid
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    /* Optional authentication check
    if(!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
        die("Unauthorized access");
    }
    */
    
    // Prepare the SQL statement
    $sql = "DELETE FROM announcements WHERE id = $id";
    
    // Execute the query
    if(mysqli_query($conn, $sql)) {
        // Check if any rows were affected
        if(mysqli_affected_rows($conn) > 0) {
            // Redirect with success message
            header("Location: announcements.php?delete_success=1");
        } else {
            // No rows affected (ID didn't exist)
            header("Location: announcements.php?delete_error=1&message=Announcement not found");
        }
        exit();
    } else {
        // Database error
        header("Location: announcements.php?delete_error=1&message=" . urlencode(mysqli_error($conn)));
        exit();
    }
} else {
    // Invalid or missing ID
    header("Location: announcements.php?delete_error=1&message=Invalid announcement ID");
    exit();
}

// Close connection
mysqli_close($conn);
?>