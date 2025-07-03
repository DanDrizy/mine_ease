<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/contact.css">
    <style> </style>
</head>
<body>
    <?php
    include '../config.php';
    $page_name = 'Contact';
    include 'main/slidebar.php';
    
    // Initialize variables
    $success = '';
    $error = '';
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $employee_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $employee_name = $conn->real_escape_string(trim($_POST['employee_name']));
        $department = $conn->real_escape_string(trim($_POST['department']));
        $leave_type = $conn->real_escape_string(trim($_POST['leave_type']));
        $start_date = $conn->real_escape_string(trim($_POST['start_date']));
        $end_date = $conn->real_escape_string(trim($_POST['end_date']));
        $reason = $conn->real_escape_string(trim($_POST['reason']));
        $email = $conn->real_escape_string(trim($_POST['email']));
        $tel = $conn->real_escape_string(trim($_POST['tel']));
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        // Validate required fields
        if (empty($employee_name) || empty($department) || empty($leave_type) || 
            empty($start_date) || empty($end_date) || empty($reason) || 
            empty($email) || empty($tel)) {
            $error = "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } elseif (strtotime($start_date) > strtotime($end_date)) {
            $error = "End date must be after start date!";
        } else {
            // Insert leave request
            $query = "INSERT INTO leave_requests (
                        employee_id, employee_name, department, leave_type, 
                        start_date, end_date, reason, status, email, tel, added_by
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "issssssssi", 
                $employee_id, $employee_name, $department, $leave_type,
                $start_date, $end_date, $reason, $email, $tel, $added_by
            );
            
            if ($stmt->execute()) {
                $success = "Your leave request has been submitted successfully!";
                
                // Clear form fields
                $_POST = array();
            } else {
                $error = "Error submitting request: " . $conn->error;
            }
            
            $stmt->close();
        }
    }
    ?>
    
    <div class="main-content">
        
        <div class="ann-stats-grid">
            <div class="announce">
                <div class="form">
                    <h3>Leave Request Form</h3>
                    
                    <?php if ($success): ?>
                        <div class="success-message"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <div>
                                <h3>Employee Name</h3>
                                <input type="text" name="employee_name" placeholder="Your full name" 
                                       value="<?php echo isset($_POST['employee_name']) ? htmlspecialchars($_POST['employee_name']) : ''; ?>" required>
                            </div>
                            <div>
                                <h3>Department</h3>
                                <input type="text" name="department" placeholder="Your department" 
                                       value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div>
                                <h3>Leave Type</h3>
                                <select name="leave_type" required>
                                    <option value="">Select leave type</option>
                                    <option value="Annual Leave" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Annual Leave') ? 'selected' : ''; ?>>Annual Leave</option>
                                    <option value="Sick Leave" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Sick Leave') ? 'selected' : ''; ?>>Sick Leave</option>
                                    <option value="Maternity Leave" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Maternity Leave') ? 'selected' : ''; ?>>Maternity Leave</option>
                                    <option value="Paternity Leave" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Paternity Leave') ? 'selected' : ''; ?>>Paternity Leave</option>
                                    <option value="Bereavement Leave" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Bereavement Leave') ? 'selected' : ''; ?>>Bereavement Leave</option>
                                    <option value="Study Leave" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Study Leave') ? 'selected' : ''; ?>>Study Leave</option>
                                    <option value="Other" <?php echo (isset($_POST['leave_type']) && $_POST['leave_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div>
                                <h3>Start Date</h3>
                                <input type="date" name="start_date" 
                                       value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" required>
                            </div>
                            <div>
                                <h3>End Date</h3>
                                <input type="date" name="end_date" 
                                       value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div>
                            <h3>Email</h3>
                            <input type="email" name="email" placeholder="Your email address" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        
                        <div>
                            <h3>Phone Number</h3>
                            <input type="tel" name="tel" placeholder="Your phone number" 
                                   value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : ''; ?>" required>
                        </div>
                        
                        <div>
                            <h3>Content</h3>
                            <textarea name="reason" placeholder="Please explain the reason for your leave" required><?php 
                                echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <button type="submit">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>