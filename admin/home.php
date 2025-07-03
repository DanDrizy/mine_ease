<?php
// Database connection
include '../config.php';

// Fetch statistics data using the users table
$employeeCount = 0;
$stakeholderCount = 0;
$hrCount = 0;
$stockManageCount = 0;

$statQuery = "SELECT 
    SUM(CASE WHEN user_type = 'employee' THEN 1 ELSE 0 END) as employee_count,
    SUM(CASE WHEN user_type = 'stakeholder' THEN 1 ELSE 0 END) as stakeholder_count,
    SUM(CASE WHEN user_type = 'HR' OR user_type = 'Human Resources' THEN 1 ELSE 0 END) as hr_count,
    (SELECT COUNT(*) FROM stock_items) as stock_count
    FROM users";

$statResult = $conn->query($statQuery);
if ($statResult && $statResult->num_rows > 0) {
    $statRow = $statResult->fetch_assoc();
    $employeeCount = $statRow['employee_count'];
    $stakeholderCount = $statRow['stakeholder_count'];
    $hrCount = $statRow['hr_count'];
    $stockManageCount = $statRow['stock_count'];
}

    // Fetch stock data
$stockOutQuantity = 0;
$stockOutValue = 0;
$stockInPercentage = 0;
$stockOutPercentage = 0;

// Better query to calculate total stock in and out quantities
$stockQuery = "SELECT 
    (SELECT SUM(quantity_out) FROM stock_transactions) as total_quantity_out,
    (SELECT SUM(value_out) FROM stock_transactions) as total_value_out,
    (SELECT SUM(quantity_in) FROM stock_transactions) as total_quantity_in,
    (SELECT SUM(value_in) FROM stock_transactions) as total_value_in";

$stockResult = $conn->query($stockQuery);
if ($stockResult && $stockResult->num_rows > 0) {
    $stockRow = $stockResult->fetch_assoc();
    
    $totalQuantityIn = $stockRow['total_quantity_in'] ?? 0;
    $totalQuantityOut = $stockRow['total_quantity_out'] ?? 0;
    $totalQuantity = $totalQuantityIn + $totalQuantityOut;
    
    // Calculate percentages only if there's data
    if ($totalQuantity > 0) {
        $stockInPercentage = round(($totalQuantityIn / $totalQuantity) * 100);
        $stockOutPercentage = round(($totalQuantityOut / $totalQuantity) * 100);
    }
    
    $stockOutQuantity = number_format($totalQuantityOut);
    $stockOutValue = number_format($stockRow['total_value_out']);
}

// Calculate SVG circle stroke-dasharray values based on percentages
$totalCircle = 377; // Total circumference of the circle
$stockInDash = ($stockInPercentage / 100) * $totalCircle;
$stockOutDash = ($stockOutPercentage / 100) * $totalCircle;

    // Fetch latest admin announcement
    $adminAnnouncement = "No announcements available.";
    
    $announceQuery = "SELECT content FROM announcements WHERE type='admin' ORDER BY created_at DESC LIMIT 1";
    $announceResult = $conn->query($announceQuery);
    if ($announceResult && $announceResult->num_rows > 0) {
        $announceRow = $announceResult->fetch_assoc();
        $adminAnnouncement = $announceRow['content'];
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
.active_dash {
    background-color: var(--primary-color);
    color: white;
}
.announcement-content a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
}
.admin-announcement
{
    width: 100%;
    height: 20rem;
    background: lightblue;
    padding: 10px;
    border-radius: 10px;
}
.title{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    background: lightseagreen;
    color: black;
    margin: 0 0 10px;
    width: 100%;

}
.message-content
{
    border: 1px solid darkblue;
    margin: 0 0 20px;
}
</style>
<body>
    <?php
    include 'main/slidebar.php';
    ?>
    
    <div class="main-content">
        
    <?php include 'main/header.php'; ?>
        
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-items">
                    <div class="stat-item">
                        <div class="stat-icon employee-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="info">
                        <div class="stat-label">Employee</div>
                        <div class="stat-number"><?php echo $employeeCount; ?></div></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon stakeholder-icon"><i class="fas fa-handshake"></i></div>
                        <div class="info">
                        <div class="stat-label">Stakeholders</div>
                        <div class="stat-number"><?php echo $stakeholderCount; ?></div>   
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon hr-icon"><i class="fas fa-users-cog"></i></div>
                        <div class="info">
                        <div class="stat-label">Human Resources</div>
                        <div class="stat-number"><?php echo $hrCount; ?></div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon stock-icon"><i class="fas fa-boxes"></i></div>
                        <div class="info">
                        <div class="stat-label">Stock item</div>
                        <div class="stat-number"><?php echo $stockManageCount; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="announcement-section">
                    
                    <!-- // Fetch different types of announcements -->
                    <div class="announcement-item">
                            <div class="announcement-icon"><i class="fas fa-bullhorn"></i></div>
                            <div class="announcement-content"><a href="announcements.php">Updates Announcement</a></div>
                    </div>
                    <div class="announcement-item">
                            <div class="announcement-icon"><i class="fas fa-bullhorn"></i></div>
                            <div class="announcement-content"><a href="stake-reply-announcements.php">Stakeholder Reply</a></div>
                    </div>
                    <div class="announcement-item">
                            <div class="announcement-icon"><i class="fas fa-bullhorn"></i></div>
                            <div class="announcement-content"><a href="stake-conv-announcements.php">Stakeholder Converstion</a></div>
                    </div>
                    
                
                </div>
            </div>
        </div>
        
        <div class="bottom-grid">
            <div class="admin-announcement">
                <div class="message">
                    <?php
                    
                    $sql = "SELECT * FROM announcements Where type = 'Admin' OR type = 'All' ORDER BY created_at";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()){
                        ?>
                        <div class="message-content">
                            <div class="title"><h3><?php echo $row['title'] ?></h3> <span><?php echo $row['created_at'] ?></span> </div>
                            <p><?php echo $row['content'] ?></p>
                        </div>

                        <?php }} else { ?>
                        <div class="message-content-error">No announcements found.</div>
                   <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>
</html>