<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .active-dashboard {
            background-color: #7A4B9D;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php 
    $page_name = " / Dashboard";
    include 'main/sidebar.php'; 
    
    // Database connection using MySQLi
    include '../config.php';
    
    // Initialize variables
    $total_items = 0;
    $total_categories = 0;
    $low_stock_items = 0;
    $approved_items = 0;
    $total_value = 0;
    $recent_activity = [];

    
    // Get total items count
    $sql = "SELECT COUNT(*) as total FROM stock_items";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_items = $row['total'];
    }
    
    // Get total categories count
    $sql = "SELECT COUNT(DISTINCT category) as total FROM stock_items";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_categories = $row['total'];
    }
    
    // Get low stock items
    $sql = "SELECT COUNT(*) as total FROM stock_items WHERE status = 'low'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $low_stock_items = $row['total'];
    }
    
    // Get approved items
    $sql = "SELECT COUNT(*) as total FROM stock_items WHERE status = 'approved'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $approved_items = $row['total'];
    }
    
    // Get total stock value
    $sql = "SELECT SUM(total_value) as total FROM stock_items";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_value = $row['total'] ? $row['total'] : 0;
    }
    
    // Get recent activity
    $sql = "SELECT item_name, quantity, updated_at FROM stock_items ORDER BY updated_at DESC LIMIT 5";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $recent_activity[] = $row;
        }
    }
    
    $conn->close();
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <!-- Stock-in Card -->
            <div class="card stock-card">
                <div class="stock-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                    </svg>
                    <span>Total Stock</span>
                </div>
                <div class="stock-number"><?php echo number_format($total_items); ?> items</div>
                <div class="stock-types"><?php echo number_format($total_categories); ?> categories</div>
            </div>
            
            <!-- Stock-out Card -->
            <div class="card stock-card">
                <div class="stock-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 8v8a2 2 0 0 1-1 1.73l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 16V8a2 2 0 0 1 1-1.73l7-4a2 2 0 0 1 2 0l7 4A2 2 0 0 1 21 8z" />
                        <path d="M12 22V12" />
                        <path d="m5 12 7-4 7 4" />
                    </svg>
                    <span>Total Value</span>
                </div>
                <div class="stock-number">Frw <?php echo number_format($total_value, 2); ?></div>
                <div class="stock-types">Average: Frw<?php echo $total_items > 0 ? number_format($total_value/$total_items, 2) : '0.00'; ?></div>
            </div>
        </div>
        
        <!-- Bottom Cards -->
        <div class="bottom-cards">
            <!-- Reorder Manage -->
            <div class="reorder-manage">
                <div class="reorder-title">Reorder Manage</div>
                
                <div class="reorder-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                        <path d="M16.5 9.4 7.55 4.24" />
                    </svg>
                    <span><?php echo number_format($low_stock_items); ?> Items Need Reorder</span>
                </div>
                
                <div class="reorder-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <path d="m9 11 3 3L22 4" />
                    </svg>
                    <span><?php echo number_format($approved_items); ?> Approved Items</span>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="announcement">
                <div class="announcement-title">Recent Stock Activity</div>
                <div class="announcement-text">
                    <?php if(count($recent_activity) > 0): ?>
                        <ul>
                            <?php foreach($recent_activity as $activity): ?>
                                <?php 
                                $date = date('M j, Y', strtotime($activity['updated_at']));
                                $item_name = htmlspecialchars($activity['item_name']);
                                $quantity = htmlspecialchars($activity['quantity']);
                                ?>
                                <li><strong><?php echo $item_name; ?></strong> - Quantity: <?php echo $quantity; ?> (Updated: <?php echo $date; ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No recent stock activity found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript could be added here for interactive features
        // For example, to handle notifications, menu toggling, etc.
    </script>
</body>
</html>