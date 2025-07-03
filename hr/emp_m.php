<?php
// Database connection
include_once '../config.php';

// Initialize variables
$search = "";
$employees = [];

// Handle search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT id, name, department, username as added_by, email, phone, 
            registration_date, salary, providence, tax, loan, site
            FROM users 
            WHERE name LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR department LIKE '%$search%'
            ORDER BY id DESC";
} else {
    // Default query to fetch all employees
    $sql = "SELECT id, name, department, username as added_by, email, phone, 
            registration_date, salary, providence, tax, loan, site
            FROM users 
            ORDER BY id DESC";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Fetch all employee records
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Handle adding/editing employee
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add_employee') {
        // Adding new employee
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
        $username = $_POST['username'];
        $user_type = $_POST['user_type'];
        $site = $_POST['site'];
        $department = $_POST['department'];
        $phone = $_POST['phone'];
        $salary = $_POST['salary'];
        $providence = $_POST['providence'];
        $tax = $_POST['tax'];
        $loan = $_POST['loan'];
        
        // Handle profile image upload
        $profile_image = "";
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
            if ($check !== false) {
                // Generate unique filename
                $profile_image = $target_dir . uniqid() . "." . $imageFileType;
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
                    // File uploaded successfully
                } else {
                    echo "Error uploading file.";
                }
            }
        }
        
        $current_date = date("Y-m-d H:i:s");
        
        $sql = "INSERT INTO users (name, email, password, username, user_type, site, department, 
                phone, profile_image, registration_date, created_at, updated_at, salary, providence, tax, loan) 
                VALUES ('$name', '$email', '$password', '$username', '$user_type', '$site', '$department', 
                '$phone', '$profile_image', '$current_date', '$current_date', '$current_date', '$salary', '$providence', '$tax', '$loan')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: emp_m.php?success=Employee added successfully");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'edit_employee') {
        // Editing existing employee
        $id = $_POST['employee_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $user_type = $_POST['user_type'];
        $site = $_POST['site'];
        $department = $_POST['department'];
        $phone = $_POST['phone'];
        $salary = $_POST['salary'];
        $providence = $_POST['providence'];
        $tax = $_POST['tax'];
        $loan = $_POST['loan'];
        
        $current_date = date("Y-m-d H:i:s");
        
        // Handle password update (only if new password is provided)
        $password_sql = "";
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_sql = ", password = '$password'";
        }
        
        // Handle profile image update
        $image_sql = "";
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
            if ($check !== false) {
                // Generate unique filename
                $profile_image = $target_dir . uniqid() . "." . $imageFileType;
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
                    $image_sql = ", profile_image = '$profile_image'";
                }
            }
        }
        
        $sql = "UPDATE users SET 
                name = '$name',
                email = '$email',
                username = '$username',
                user_type = '$user_type',
                site = '$site',
                department = '$department',
                phone = '$phone',
                updated_at = '$current_date',
                salary = '$salary',
                providence = '$providence',
                tax = '$tax',
                loan = '$loan'
                $password_sql
                $image_sql
                WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: emp_m.php?success=Employee updated successfully");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Handle attendance marking
if (isset($_POST['mark_attendance'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status']; // "Present", "Absent", etc.
    $current_date = date("Y-m-d");
    $current_time = date("H:i:s");
    
    // Check if there's already an attendance record for this user on this date
    $check_sql = "SELECT * FROM attendance WHERE user_id = $user_id AND date = '$current_date'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        // Update existing attendance record - mark time out
        $sql = "UPDATE attendance SET time_out = '$current_time', status = '$status' 
                WHERE user_id = $user_id AND date = '$current_date'";
    } else {
        // Insert new attendance record - mark time in
        $sql = "INSERT INTO attendance (user_id, date, time_in, status) 
                VALUES ($user_id, '$current_date', '$current_time', '$status')";
    }
    
    if ($conn->query($sql) === TRUE) {
        header("Location: emp_m.php?success=Attendance marked successfully");
        exit();
    } else {
        echo "Error marking attendance: " . $conn->error;
    }
}

// Handle employee export
if (isset($_GET['export']) && $_GET['export'] == 'true') {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="employees.csv"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write headers to CSV
    fputcsv($output, ['ID', 'Name', 'Department', 'Added By', 'Email', 'Phone', 'Registration Date', 'Salary', 'Providence', 'Tax', 'Loan']);
    
    // Write data rows to CSV
    foreach ($employees as $employee) {
        fputcsv($output, [
            $employee['id'],
            $employee['name'],
            $employee['department'],
            $employee['added_by'],
            $employee['email'],
            $employee['phone'],
            $employee['registration_date'],
            $employee['salary'],
            $employee['providence'],
            $employee['tax'],
            $employee['loan']
        ]);
    }
    
    // Close output stream
    fclose($output);
    exit();
}

// Get attendance status for each employee
function getAttendanceStatus($user_id, $conn) {
    $current_date = date("Y-m-d");
    $sql = "SELECT * FROM attendance WHERE user_id = $user_id AND date = '$current_date'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $attendance = $result->fetch_assoc();
        return [
            'status' => $attendance['status'],
            'time_in' => $attendance['time_in'],
            'time_out' => $attendance['time_out']
        ];
    }
    
    return [
        'status' => 'Not Marked',
        'time_in' => '',
        'time_out' => ''
    ];
}

// Close connection when done
// $conn->close(); // Commented out to keep connection open until end of script
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="style/style.css"> 
</head>
<style>
    .active-emp {
        background-color: #81C5B1;
            transition: background-color 0.3s ease;
            color: #1E1E1E;
    }
    .dashboard-row
    {
        height: 100vh;
    }
    .dashboard-column
    {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        
    }

    .search
    {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background:#fff;
        padding: 20px;
        border-radius: 20px;

    }

    .table-emp
    {
        width: 100%;
        height: 100%;
        /* background: #000; */
    }

    .table-emp table
    {
        width: 100%;
        border-collapse: collapse;
        /* background:#fff; */
    }

    .left-side
    {
        display: flex;
        align-items: center;
        gap: 10px;

    }
    .left-side input
    {
        padding: 10px;
        border-radius: 20px;
        border: 1px solid #ccc;
        width: 280px;
    }
    .left-side input:focus
    {
        outline: none;
        border-color: #81C5B1;
    }
    .left-side button
    {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #81C5B1;
        color: #fff;
        cursor: pointer;
    }
    .right-side
    {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .right-side button
    {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #81C5B1;
        color: #fff;
        cursor: pointer;
    }

    .add-emp-button {
        background-color: #81C5B1;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        cursor: pointer;
    }

    .table-container
    {
        background: #fff;
        padding: 40px;
        height: 85%;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: auto;

    }

    .table-emp th, .table-emp td
    {
        padding: 20px;
        text-align: left;
        border-bottom: 1px solid #ddd;  
    }
    .table-emp th
    {
        background-color: darkblue;
        color: white;
    }
    .table-emp tr:hover
    {
        background-color: #f5f5f5;
    }
    .peform
    {
        border:2px solid  #81C5B1;
        color: #81C5B1;
        /* border: none; */
        background: transparent;
        padding: 5px 10px;
        cursor: pointer;
    }
    .option
    {
        background-color: #81C5B1;
        color: #fff;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        overflow: auto;
    padding-bottom: 40px;
    }
    
    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 700px;
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close:hover {
        color: #000;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-group input, .form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .form-group button {
        background-color: #81C5B1;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-present {
        color: #28a745;
        font-weight: bold;
    }
    
    .status-absent {
        color: #dc3545;
        font-weight: bold;
    }
    
    .status-not-marked {
        color: #6c757d;
        font-style: italic;
    }
    
    .attendance-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
</style>
<body>

    <?php 
    $page_name = 'Employee Management';

     include'main/slidebar.php'; 

     $i = 1;
     
     ?>
    

    <div class="main-content">

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_GET['success']; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_GET['error']; ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-row">
            
            <div class="dashboard-column">
                <div class="table-emp">
                    <div class="search">
                        <div class="left-side">
                            <form action="" method="GET">
                                <input type="text" name="search" placeholder="Search Employee" class="search-input" value="<?php echo $search; ?>">
                                <button type="submit" class="search-button">Search</button>
                            </form>
                        </div>
                        <div class="right-side">
                            <button class="add-emp-button" onclick="openAddModal()">Add Employee</button>
                            <a href="?export=true" class="add-emp-button">Export</a>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Added By</th>
                                    <th>Email</th>
                                    <th>Tel</th>
                                    <th>Attendance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($employees) > 0): ?>
                                    <?php foreach ($employees as $employee): ?>
                                        <?php $attendanceInfo = getAttendanceStatus($employee['id'], $conn); ?>
                                        <tr>
                                            <td><?php echo $i; $i++; ?></td>
                                            <td><?php echo $employee['name']; ?></td>
                                            <td><?php echo $employee['department']; ?></td>
                                            <td><?php echo $employee['added_by']; ?></td>
                                            <td><?php echo $employee['email']; ?></td>
                                            <td><?php echo $employee['phone']; ?></td>
                                            <td>
                                                <?php if ($attendanceInfo['status'] == 'Not Marked'): ?>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="user_id" value="<?php echo $employee['id']; ?>">
                                                        <input type="hidden" name="status" value="Present">
                                                        <button type="submit" name="mark_attendance" class="peform">Mark Present</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="status-<?php echo strtolower($attendanceInfo['status']); ?>">
                                                        <?php echo $attendanceInfo['status']; ?>
                                                    </span>
                                                    <div class="attendance-info">
                                                        Time In: <?php echo $attendanceInfo['time_in']; ?>
                                                        <?php if (!empty($attendanceInfo['time_out'])): ?>
                                                            <br>Time Out: <?php echo $attendanceInfo['time_out']; ?>
                                                        <?php else: ?>
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="user_id" value="<?php echo $employee['id']; ?>">
                                                                <input type="hidden" name="status" value="<?php echo $attendanceInfo['status']; ?>">
                                                                <button type="submit" name="mark_attendance" class="peform" style="margin-top: 5px;">Mark Out</button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="option" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($employee)); ?>)">Edit</button>
                                                <button class="option" onclick="openAttendanceModal(<?php echo $employee['id']; ?>, '<?php echo $employee['name']; ?>')">History</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center;">No employees found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Employee</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_employee">
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="user_type">User Type</label>
                    <select id="user_type" name="user_type" required>
                        <option value="employee">Employee</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="site">Site</label>
                    <input type="text" id="site" name="site">
                </div>
                
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image">
                </div>
                
                <div class="form-group">
                    <label for="salary">Salary</label>
                    <input type="number" id="salary" name="salary" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="providence">Providence</label>
                    <input type="number" id="providence" name="providence" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="tax">Tax</label>
                    <input type="number" id="tax" name="tax" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="loan">Loan</label>
                    <input type="number" id="loan" name="loan" step="0.01">
                </div>
                
                <div class="form-group">
                    <button type="submit">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Employee Modal -->
    <div id="editEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Employee</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_employee">
                <input type="hidden" id="edit_employee_id" name="employee_id">
                
                <div class="form-group">
                    <label for="edit_name">Full Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" id="edit_username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_user_type">User Type</label>
                    <select id="edit_user_type" name="user_type" required>
                        <option value="employee">Employee</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_site">Site</label>
                    <input type="text" id="edit_site" name="site">
                </div>
                
                <div class="form-group">
                    <label for="edit_department">Department</label>
                    <input type="text" id="edit_department" name="department" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_phone">Phone</label>
                    <input type="text" id="edit_phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_profile_image">Profile Image (leave blank to keep current)</label>
                    <input type="file" id="edit_profile_image" name="profile_image">
                </div>
                
                <div class="form-group">
                    <label for="edit_salary">Salary</label>
                    <input type="number" id="edit_salary" name="salary" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_providence">Providence</label>
                    <input type="number" id="edit_providence" name="providence" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="edit_tax">Tax</label>
                    <input type="number" id="edit_tax" name="tax" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="edit_loan">Loan</label>
                    <input type="number" id="edit_loan" name="loan" step="0.01">
                </div>
                
                <div class="form-group">
                    <button type="submit">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Attendance History Modal -->
    <div id="attendanceHistoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAttendanceModal()">&times;</span>
            <h2>Attendance History: <span id="attendanceEmployeeName"></span></h2>
            
            <div id="attendanceHistoryContent" style="max-height: 400px; overflow-y: auto;">
                <!-- Attendance history will be loaded here via AJAX -->
                <p>Loading attendance data...</p>
            </div>
        </div>
    </div>
    
    <script>
        // Modal functions
        function openAddModal() {
            document.getElementById("addEmployeeModal").style.display = "block";
        }
        
        function closeAddModal() {
            document.getElementById("addEmployeeModal").style.display = "none";
        }
        
        function openEditModal(employee) {
            // Populate the form with employee data
            document.getElementById("edit_employee_id").value = employee.id;
            document.getElementById("edit_name").value = employee.name;
            document.getElementById("edit_email").value = employee.email;
            document.getElementById("edit_username").value = employee.added_by;
            document.getElementById("edit_department").value = employee.department;
            document.getElementById("edit_phone").value = employee.phone;
            document.getElementById("edit_site").value = employee.site || "";
            document.getElementById("edit_user_type").value = "employee"; // Default value
            document.getElementById("edit_salary").value = employee.salary || 0;
            document.getElementById("edit_providence").value = employee.providence || 0;
            document.getElementById("edit_tax").value = employee.tax || 0;
            document.getElementById("edit_loan").value = employee.loan || 0;
            
            // Show the modal
            document.getElementById("editEmployeeModal").style.display = "block";
        }
        
        function closeEditModal() {
            document.getElementById("editEmployeeModal").style.display = "none";
        }
        
        function openAttendanceModal(employeeId, employeeName) {
            document.getElementById("attendanceEmployeeName").textContent = employeeName;
            document.getElementById("attendanceHistoryContent").innerHTML = '<p>Loading attendance data...</p>';
            document.getElementById("attendanceHistoryModal").style.display = "block";
            
            // AJAX call to get attendance history
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_attendance.php?user_id=" + employeeId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("attendanceHistoryContent").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        
        function closeAttendanceModal() {
            document.getElementById("attendanceHistoryModal").style.display = "none";
        }
        
        // Close the modal when clicking outside of it
window.onclick = function(event) {
    if (event.target === document.getElementById("addEmployeeModal")) {
        closeAddModal();
    }
    if (event.target === document.getElementById("editEmployeeModal")) {
        closeEditModal();
    }
    if (event.target === document.getElementById("attendanceHistoryModal")) {
        closeAttendanceModal();
    }
}
</script>
</body>
</html>