<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'mine_ease');

// Create connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check function
function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../login.php");
        exit();
    }
}
?>