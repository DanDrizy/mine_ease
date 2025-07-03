<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page title
$page_name = 'Leave Requests';

// Database connection (example - adjust according to your setup)
require_once '../config.php';

// Function to get leave requests from database
function getLeaveRequests($conn) {
    $requests = [];
    $query = "SELECT * FROM leave_requests WHERE status = 'pending'";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Database error: " . $conn->error);
    }
    return $requests;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $requestId = $_POST['request_id'];
        $action = $_POST['action'];
        
        if ($action === 'accept') {
            $query = "UPDATE leave_requests SET status = 'approved' WHERE id = ?";
        } elseif ($action === 'deny') {
            $query = "UPDATE leave_requests SET status = 'denied' WHERE id = ?";
        }
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $requestId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Leave request " . ($action === 'accept' ? 'approved' : 'denied') . " successfully";
            } else {
                $_SESSION['error'] = "Error processing request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        }
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Get leave requests from database
$leaveRequests = getLeaveRequests($conn);
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
    .active-leave {
        background-color: #81C5B1;
        transition: background-color 0.3s ease;
        color: #1E1E1E;
    }
    .dashboard-row {
        height: 100vh;
    }
    .dashboard-column {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background:#fff;
        padding: 20px;
        border-radius: 20px;
    }

    .table-emp {
        width: 100%;
        height: 100%;
    }

    .table-emp table {
        width: 100%;
        border-collapse: collapse;
    }

    .left-side {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .left-side input {
        padding: 10px;
        border-radius: 20px;
        border: 1px solid #ccc;
        width: 280px;
    }
    .left-side input:focus {
        outline: none;
        border-color: #81C5B1;
    }
    .left-side button {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #81C5B1;
        color: #fff;
        cursor: pointer;
    }
    .right-side {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .right-side button {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #81C5B1;
        color: #fff;
        cursor: pointer;
    }

    .table-container {
        background: #fff;
        font-size: 13px;
        padding: 40px;
        height: 85%;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: auto;
    }

    .table-emp th, .table-emp td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;  
    }
    .table-emp th {
        background-color: darkblue;
        color: white;
    }
    .table-emp tr:hover {
        background-color: #f5f5f5;
    }
    .peform {
        border:2px solid green;
        color: green;
        background: transparent;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
        transition: background-color 0.3s ease;
    }
    .peform:hover {
        background-color: green;
        color: white;
        transition: background-color 0.3s ease;
    }
    .peform:active {
        background-color: #81C5B1;
        color: white;
    }

    .option {
        border:2px solid red;
        color: red;
        background: transparent;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
    }
    .option:hover {
        background-color: red;
        color: white;
        transition: background-color 0.3s ease;
    }
    .option:active {
        background-color: #81C5B1;
        color: white;
    }
    
    .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }
    .success {
        background-color: #d4edda;
        color: #155724;
    }
    .error {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>

        <div class="dashboard-row">
            <div class="dashboard-column">
                <div class="table-emp">
                    <div class="search">
                        <div class="left-side">
                            <h2>Leave Requests</h2>
                        </div>
                        <div class="right-side left-side">
                            <input type="text" placeholder="Search Employee" class="search-input">
                            <button class="search-button">Search</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="message success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($leaveRequests)): ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center;">No pending leave requests</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($leaveRequests as $request): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($request['employee_id']); ?></td>
                                            <td><?php echo htmlspecialchars($request['employee_name']); ?></td>
                                            <td><?php echo htmlspecialchars($request['department']); ?></td>
                                            <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
                                            <td><?php echo htmlspecialchars($request['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['end_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                            <td><?php echo htmlspecialchars($request['status']); ?></td>
                                            <td>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="action" value="accept">
                                                    <button type="submit" class="peform">Accept</button>
                                                </form>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="action" value="deny">
                                                    <button class="option">Deny</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>