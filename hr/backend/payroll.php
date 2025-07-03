<?php

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_attendance'])) {
        $employee_id = intval($_POST['employee_id']);
        $status = $_POST['status'] === 'attended' ? 1 : 0;
        $month = date('n');
        $year = date('Y');
        
        // Check if attendance already exists
        $stmt = $conn->prepare("SELECT id FROM mattendance WHERE user_id = ? AND month = ? AND year = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("iii", $employee_id, $month, $year);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing attendance
            $stmt = $conn->prepare("UPDATE mattendance SET status = ? WHERE user_id = ? AND month = ? AND year = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iiii", $status, $employee_id, $month, $year);
        } else {
            // Insert new attendance
            $stmt = $conn->prepare("INSERT INTO mattendance (user_id, month, year, status) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iiii", $employee_id, $month, $year, $status);
        }
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Fetch all employees
$employees = [];
$attendance_data = [];

try {
    // Get all employees
    $stmt = $conn->prepare("SELECT id, name, email, department, phone FROM users");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $employees = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get attendance data for all employees
    $stmt = $conn->prepare("SELECT user_id, GROUP_CONCAT(DISTINCT month) as months, 
                           GROUP_CONCAT(DISTINCT CONCAT(year, ':', month)) as 'year_month' 
                           FROM mattendance WHERE status = 1 GROUP BY user_id");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $attendance_data[$row['user_id']] = [
            'months' => $row['months'],
            'year_month' => $row['year_month']
        ];
    }
    $stmt->close();

} catch(Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("Database error occurred. Please try again later.");
}

// Function to format paid years data for HTML data attribute
function formatPaidYearsData($year_month) {
    if (empty($year_month)) return '';
    
    $pairs = explode(',', $year_month);
    $years = [];
    
    foreach ($pairs as $pair) {
        list($year, $month) = explode(':', $pair);
        if (!isset($years[$year])) {
            $years[$year] = [];
        }
        $years[$year][] = $month;
    }
    
    $result = [];
    foreach ($years as $year => $months) {
        $result[$year] = implode(',', $months);
    }
    
    return htmlspecialchars(json_encode($result));
}

// Get current month attendance status for each employee
$current_month = date('n');
$current_year = date('Y');
$current_attendance = [];

$stmt = $conn->prepare("SELECT user_id, status FROM mattendance WHERE month = ? AND year = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $current_month, $current_year);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $current_attendance[$row['user_id']] = $row['status'];
}
$stmt->close();
?>