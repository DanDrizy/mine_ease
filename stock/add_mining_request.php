<?php
// Database connection
require_once '../config.php';

// Initialize variables
$message = '';
$message_type = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_text = $conn->real_escape_string(trim($_POST['request_text']));
    $user_id = $_SESSION['user_id'] ?? 0; // Assuming you have user authentication
    
    // Validate input
    if (empty($request_text)) {
        $message = 'Please enter a request description';
        $message_type = 'error';
    } else {
        // Insert into database
        $sql = "INSERT INTO mining_requests (user_id, request_text, status, request_date) 
                VALUES (?, ?, 'Pending', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $request_text);
        
        if ($stmt->execute()) {
            $message = 'Mining request submitted successfully!';
            $message_type = 'success';
            $_POST = array(); // Clear form
        } else {
            $message = 'Error submitting request: ' . $conn->error;
            $message_type = 'error';
        }
    }
}

// Get recent mining requests
$mining_requests = [];
$sql = "SELECT id, request_text, status, DATE_FORMAT(request_date, '%d %b %y') as formatted_date 
        FROM mining_requests 
        ORDER BY request_date DESC 
        LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $mining_requests[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager | Add Mining Request</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Original styles with enhancements */
        .active-reorder-levels {
            background-color: #7A4B9D;
            color: white;
        }

        .dashboard-cards-stock {
            width: 100%;  
            margin: 10px 0px 20px;
            height: 30vh;
        }
        
        .card-stock {
            background: #D0E0FF;
            width: 100%;
            height: 100%;
            border-radius: 10px;
            padding: 20px;
            overflow: hidden;
        }
        
        .min-request {
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: 100%;
        }
        
        .min-request label {
            font-size: 1.2rem;
            color: #4768A8;
            font-weight: bold;
        }
        
        .min-request textarea {
            padding: 15px;
            border-radius: 5px;
            border: 2px solid #5a3e7c;
            width: 100%;
            height: 150px;
            resize: none;
            font-family: inherit;
            font-size: 14px;
        }
        
        .min-request textarea:focus {
            outline: none;
            border: 2px solid #7A4B9D;
            box-shadow: 0 0 0 2px rgba(122, 75, 157, 0.2);
        }
        
        .min-request button {
            padding: 12px 25px;
            border-radius: 5px;
            border: none;
            background: #7A4B9D;
            color: white;
            cursor: pointer;
            width: auto;
            align-self: flex-end;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .min-request button:hover {
            background: #5a3e7c;
        }
        
        .bottom-cards-stock {
            background: #D0E0FF;
            width: 100%;
            height: 50vh;
            border-radius: 10px;
            padding: 20px;
            overflow: auto;
        }
        
        .table-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 0;
            margin-bottom: 15px;
            color: #4768A8;
            font-size: 1.5rem;
        }
        
        .stock-table {
            width: 100%;
            overflow: auto;
        }
        
        .stock-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .stock-table th, .stock-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .stock-table th {
            background: #4768A8;
            color: white;
            position: sticky;
            top: 0;
        }
        
        .stock-table tr:hover {
            background-color: rgba(122, 75, 157, 0.1);
        }
        
        .status {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #ffecdb;
            color: #e67e22;
        }
        
        .status-approved {
            background-color: #e3f7ec;
            color: #27ae60;
        }
        
        /* Message styling */
        .message {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .success {
            background-color: #e3f7ec;
            color: #27ae60;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Action buttons */
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            font-size: 12px;
        }
        
        .view-btn {
            background-color: #4768A8;
            color: white;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php 
    $page_name = "Reorder Management / Add Mining Request";
    include 'main/sidebar.php'; 
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <!-- Request Form -->
        <div class="dashboard-cards-stock">
            <div class="card-stock">
                <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <form class="min-request" method="POST" action="">
                    <label for="request_text">Add Mining Request</label>
                    <textarea id="request_text" name="request_text" placeholder="Describe your mining request in detail..." required><?php echo isset($_POST['request_text']) ? htmlspecialchars($_POST['request_text']) : ''; ?></textarea>
                    <button type="submit">Submit Request</button>
                </form>
            </div>
        </div>
        
        <!-- Recent Requests Table -->
        <div class="bottom-cards-stock">
            <div id="stockInTable" class="stock-table stock-in-table active">
                <h3 class="table-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg> 
                    Recent Mining Requests
                </h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($mining_requests)): ?>
                            <?php foreach($mining_requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(substr($request['request_text'], 0, 50)); ?><?php echo strlen($request['request_text']) > 50 ? '...' : ''; ?></td>
                                <td><?php echo htmlspecialchars($request['formatted_date']); ?></td>
                                <td>
                                    <span class="status status-<?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn view-btn" onclick="viewRequest(<?php echo $request['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn delete-btn" onclick="confirmDelete(<?php echo $request['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No mining requests found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Request Modal (hidden by default) -->
    <div id="viewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 8px; width: 80%; max-width: 600px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #4768A8;">Request Details</h3>
                <span style="cursor: pointer; font-size: 24px;" onclick="document.getElementById('viewModal').style.display = 'none'">&times;</span>
            </div>
            <div id="requestDetails" style="max-height: 60vh; overflow-y: auto;">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // View request details
        function viewRequest(requestId) {
            const modal = document.getElementById('viewModal');
            const contentDiv = document.getElementById('requestDetails');
            
            // Show loading state
            contentDiv.innerHTML = '<p>Loading request details...</p>';
            modal.style.display = 'flex';
            
            // Fetch request details
            fetch(`get_request_details.php?id=${requestId}`)
                .then(response => response.text())
                .then(data => {
                    contentDiv.innerHTML = data;
                })
                .catch(error => {
                    contentDiv.innerHTML = `<p>Error loading request: ${error.message}</p>`;
                });
        }
        
        // Confirm before deleting
        function confirmDelete(requestId) {
            if (confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
                // Submit delete request
                fetch(`delete_request.php?id=${requestId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Request deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
        
        // Form validation
        document.querySelector('.min-request').addEventListener('submit', function(e) {
            const textarea = this.querySelector('textarea');
            if (textarea.value.trim().length < 10) {
                alert('Please enter a detailed request (at least 10 characters)');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>