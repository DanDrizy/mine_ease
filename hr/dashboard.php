<?php
// Start session and include database connection
session_start();
require_once '../config.php';

// Initialize variables
$attended_count = 0;
$total_employees = 0;
$requests = [];
// $announcement = "the good behaving of this chart is that stock out but be balancing the same space with stock-in order to understand that the data must always be in stock and also the stock out be currently used the good behaving of this chart is that stock out but be balancing the same space with stock-in order to understand that the data must always be in stock and also the stock out be currently used the good behaving of this chart is that stock out but be balancing the same space with stock-in order to understand that the data must always be in stock and also the stock out be currently used";

try {
    // Get total employees count
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_employees = $row['total'];
    $stmt->close();
    
    // Get attended employees count
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as attended FROM attendance WHERE date = CURDATE()");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $attended_count = $row['attended'];
    $stmt->close();
    
    // Get recent requests
    $stmt = $conn->prepare("SELECT u.name, u.department FROM requests r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 3");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    $stmt->close();
    
} catch(Exception $e) {
    // Handle errors
    error_log("Database error: " . $e->getMessage());
}

// Calculate unattended count
$unattended_count = $total_employees - $attended_count;

$page_name = 'Dashboard';
include 'main/slidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="style/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php 
    function getFirstTwoDigits($num1) {
    // Remove any decimal point and minus sign
    $number = preg_replace('/[^0-9]/', '', strval($num1));
    return substr($number, 0, 2);
    }

    function getFirstTwoDigits2($num1) {
    // Remove any decimal point and minus sign
    $number = preg_replace('/[^0-9]/', '', strval($num1));
    return substr($number, 0, 2);
    }
    
    $num1 = getFirstTwoDigits($attended_count); 
    $num2 = getFirstTwoDigits2($total_employees); 

    if ($num1 > 10) {

        $num1 = 10; // Cap the height at 10rem

    }else if ($num2 < 10) {

        $num2 = 10; // Minimum height at 10rem
    }

    
    
    ?>
    <style>
        .active-dash {
            background-color: #81C5B1;
            transition: background-color 0.3s ease;
            color: #1E1E1E;
        }
        

        .column-bar.attended {

        background: transparent;
        height: 2rem;
        display: flex;
        text-align: center;
        justify-content: center;
        font-size: 25px;
        font-weight: 600;
            
            
        }
        
        .column-bar.unattended {
            
        height: 2rem;
        background: transparent;
        display: flex;
        text-align: center;
        justify-content: center;
        font-size: 25px;
        font-weight: 600;



            
        }
        .announcement-item
        {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            /* background-color: #f9f9f9; */
        }
    </style>
</head>
<body>
    <div class="main-content">
        <?php include 'main/header.php'; ?>

        <div class="dashboard-row">
            <div class="dashboard-column">
                <div class="stats-container">
                    <div class="stat-card green">
                        <div class="stat-info">
                            <div class="stat-title">Attended Emp</div>
                            <div class="stat-value"><?php echo number_format($attended_count); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-info">
                            <div class="stat-title">Total Emp</div>
                            <div class="stat-value"><?php echo number_format($total_employees); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="employees-card">
                    <div class="employees-title">Attendance Rate</div>
                    <div class="employees-count">
                        <?php echo $total_employees > 0 ? round(($attended_count / $total_employees) * 100) : 0; ?>%
                    </div>
                    <div class="employees-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="dashboard-column">
                <div class="chart-container">
                    <div class="column-chart">
                        <div class="column">
                            <div class="column-bar attended"><?php echo $attended_count; ?></div>
                            <div class="column-label attended-label">Attended</div>
                        </div>
                        <div class="column">
                            <div class="column-bar unattended"><?php echo $unattended_count; ?></div>
                            <div class="column-label unattended-label">Unattended</div>
                        </div>
                    </div>
                    
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color attended"></div>
                            <div class="attended-label">Attended</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color unattended"></div>
                            <div class="unattended-label">Unattended</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-row">
            <div class="dashboard-column">
                <div class="section-title">Recent Requests</div>
                <table class="request-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['department']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No recent requests</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="dashboard-column">
                <div class="section-title">Announcement</div>
                <div class="announcement-content">
                    <?php
                    $select_announcement = mysqli_query($conn, "SELECT * FROM announcements WHERE type= 'hr' or type= 'All' ORDER BY created_at DESC LIMIT 2");

                    while ($row = mysqli_fetch_assoc($select_announcement)) {
                        $announcement = htmlspecialchars($row['content']);
                        $head = htmlspecialchars($row['title']);
                        echo"<div class='announcement-item'>";
                        echo "<div class='headers'><h3 class='head-text'>{$head}</h3></div>";
                        echo "<p class='announcement-text'>{$announcement}</p>";
                        echo "</div>";
                    }
                    
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>