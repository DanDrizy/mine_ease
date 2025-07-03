<?php
// Database connection
require_once '../config.php';

// Get mining requests data
$mining_requests = [];
$sql = "SELECT * FROM mining_requests ORDER BY request_date DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $mining_requests[] = $row;
    }
}

// Get approval process data
$approvals = [];
$sql = "SELECT * FROM approval_process ORDER BY approval_date DESC LIMIT 2";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $approvals[] = $row;
    }
}

// Get monthly history data
$monthly_history = [];
$sql = "SELECT * FROM monthly_history ORDER BY month DESC LIMIT 4";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $monthly_history[] = $row;
    }
}

// Get stock levels
$stock_in = 0;
$stock_out = 0;
$sql = "SELECT 
        SUM(CASE WHEN status = 'in-stock' THEN quantity ELSE 0 END) as stock_in,
        SUM(CASE WHEN status = 'out-of-stock' THEN quantity ELSE 0 END) as stock_out
        FROM stock_items";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stock_in = $row['stock_in'] ? $row['stock_in'] : 0;
    $stock_out = $row['stock_out'] ? $row['stock_out'] : 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager | Reorder Management</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .active-reorder-levels {
            background-color: #7A4B9D;
            color: white;
        }
       
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #4768A8;
            color: white;
        }
        
        .card-header h2 {
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        
        .card-header .add-button {
            width: 32px;
            height: 32px;
            background-color: white;
            color: #4768A8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .card-header .add-button:hover {
            background-color: #7A4B9D;
            color: white;
        }
        
        .card-content {
            padding: 15px 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        table th, table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #4768A8;
        }
        
        table tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Status Badges */
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
        
        /* Performance Indicators */
        .performance-good {
            color: #27ae60;
            font-weight: 500;
        }
        
        .performance-bad {
            color: #e74c3c;
            font-weight: 500;
        }
        
        /* Stock Chart */
        .stock-chart {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 20px;
        }
        
        .bar {
            border: 2px solid #4768A8;
            margin: 15px;
            border-radius: 50%;
            width: 180px;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .bar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .bar h1 {
            font-size: 18px;
            margin: 0 0 10px 0;
            color: #4768A8;
        }
        
        .bar h3 {
            font-size: 24px;
            margin: 0;
            color: #7A4B9D;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #4768A8;
        }
        
        .close {
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .close:hover {
            color: #333;
        }
        
        .modal-body {
            padding: 15px 0;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php 
    $page_name = " / Reorder Management";
    include 'main/sidebar.php'; 
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Mining Requesting Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-hammer"></i>
                        Mining Requests
                    </h2>
                    <a href="add_mining_request.php" style="text-decoration: none;">
                        <div class="add-button">+</div>
                    </a>
                </div>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Request</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($mining_requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['request_text']); ?></td>
                                <td><?php echo date('d M y', strtotime($request['request_date'])); ?></td>
                                <td>
                                    <span class="status status-<?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn view-btn" onclick="showRequestModal(<?php echo $request['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($mining_requests)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No mining requests found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Stock Level Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-boxes"></i>
                        Stock Level
                    </h2>
                </div>
                <div class="stock-chart">
                    <div class="bar stock-in">
                        <h1>Stock-in</h1>
                        <h3><?php echo number_format($stock_in); ?>+</h3>
                    </div>
                    <div class="bar stock-out">
                        <h1>Stock-out</h1>
                        <h3><?php echo number_format($stock_out); ?>+</h3>
                    </div>
                </div>
            </div>
            
            <!-- Approval Process Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-check-circle"></i>
                        Approval Process
                    </h2>
                </div>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Approval</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($approvals as $approval): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($approval['approval_title']); ?></td>
                                <td><?php echo date('d M y', strtotime($approval['approval_date'])); ?></td>
                                <td>
                                    <span class="status status-approved">Approved</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($approvals)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">No approvals found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Monthly Historic Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Monthly History
                    </h2>
                </div>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Performance</th>
                                <th>Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($monthly_history as $history): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($history['type']); ?></td>
                                <td><?php echo number_format($history['quantity']); ?></td>
                                <td>
                                    <span class="performance-<?php echo $history['performance'] === 'Good' ? 'good' : 'bad'; ?>">
                                        <?php echo htmlspecialchars($history['performance']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('F', mktime(0, 0, 0, $history['month'], 1)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($monthly_history)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No history data found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Mining Request Details</h3>
                <span class="close" onclick="closeModal('requestModal')">&times;</span>
            </div>
            <div class="modal-body" id="requestModalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // Show request details modal
        function showRequestModal(requestId) {
            const modal = document.getElementById('requestModal');
            const modalBody = document.getElementById('requestModalBody');
            
            // Show loading state
            modalBody.innerHTML = '<p>Loading request details...</p>';
            modal.style.display = 'block';
            
            // Fetch request details via AJAX
            fetch(`get_request_details.php?id=${requestId}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    modalBody.innerHTML = `<p>Error loading request details: ${error.message}</p>`;
                });
        }
        
        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>