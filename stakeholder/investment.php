<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/invest.css">
    <style></style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Investment";
        
        // Start session and include database connection
        session_start();
        require_once '../config.php';
        
        include 'main/sidebar.php'; 
        
        // Initialize filter variables
        $location_filter = isset($_GET['location']) ? $_GET['location'] : '';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $search_term = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Build base query
        $query = "SELECT 
    *,
    si.quantity AS stock_quantity,
    si.unit_price AS stock_unit_price,
    so.quantity AS stockout_quantity,
    so.unit_price AS stockout_unit_price,
    SUM(si.unit_price * si.quantity) AS total_stock_value,
    SUM(so.unit_price * so.quantity) AS total_stockout_value,
    (SUM(si.unit_price * si.quantity) - SUM(so.unit_price * so.quantity)) AS net_profit_loss
    FROM
    stock_items si
    LEFT JOIN stockout so ON si.id = so.in_id AND so.quantity > 0";
        
        // Apply filters
        if (!empty($location_filter)) {
            $query .= " AND location = '" . $conn->real_escape_string($location_filter) . "'";
        }
        
        if (!empty($start_date)) {
            $query .= " AND created_at >= '" . $conn->real_escape_string($start_date) . "'";
        }
        
        if (!empty($end_date)) {
            $query .= " AND created_at <= '" . $conn->real_escape_string($end_date) . " 23:59:59'";
        }
        
        if (!empty($search_term)) {
            $query .= " AND (item_name LIKE '%" . $conn->real_escape_string($search_term) . "%' 
                          OR category LIKE '%" . $conn->real_escape_string($search_term) . "%')";
        }
        
        // Group by location
        $query .= " GROUP BY location ORDER BY si.unit_price DESC ";
        
        // Execute query
        $result = $conn->query($query);
        
        // Get unique locations for dropdown
        $locations_query = "SELECT DISTINCT location FROM stock_items ORDER BY location";
        $locations_result = $conn->query($locations_query);
        $locations = [];
        while ($row = $locations_result->fetch_assoc()) {
            $locations[] = $row['location'];
        }
        ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'main/header.php'; ?>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid-finance">
                <!-- Search and Filter Section -->
                <form method="GET" action="" class="search">
                    <select name="location">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $location_filter == $loc ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    
                    <div class="field">
                        <input type="search" name="search" placeholder="Search items or categories" value="<?php echo htmlspecialchars($search_term); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                
                <!-- Financial Data Table -->
                <div class="finance-table">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Location</th>
                                <th>Invested</th>
                                <th>Stocked-in</th>
                                <th>Stocked-out</th>
                                <th>Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php $counter = 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td><?php echo number_format($row['stock_unit_price']); ?> Frw</td>
                                        <td><?php echo number_format($row['total_stock_value']); ?> Frw</td>
                                        <td><?php echo number_format($row['total_stockout_value']); ?> Frw</td>
                                        <td><?php echo number_format($row['net_profit_loss']); ?> Frw</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No financial data found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination would go here if implemented -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add loading animation
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Simulate loading time
        //     const body = document.querySelector('body');
        //     body.style.opacity = '0';
            
        //     setTimeout(() => {
        //         body.style.transition = 'opacity 0.5s ease';
        //         body.style.opacity = '1';
        //     }, 500);
        // });
        
        // Notification bell functionality
        const notificationBell = document.querySelector('.notification-bell');
    </script>
</body>
</html>