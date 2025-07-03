<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/oper.css">
    <style>

        .High
        {
            color:green;
        }
        .Low
        {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Operations";
        
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
        $query = " SELECT 
        
        *, 
        so.unit_price as out_price,
        si.unit_price as in_price,
        so.quantity as out_quanity,
        si.quantity as in_quanity,
        SUM(so.quantity) as total_out,
        SUM(si.quantity) as total_in,
        SUM(si.unit_price) as total_in_price,
        SUM(so.unit_price) as total_out_price
        
        FROM stock_items si, stockout so WHERE si.id = so.in_id AND so.quantity > 0 ";
        
        // Apply filters
        if (!empty($location_filter)) {
            $query .= " AND si.location = '" . $conn->real_escape_string($location_filter) . "'";
        }
        
        if (!empty($start_date)) {
            $query .= " AND si.created_at >= '" . $conn->real_escape_string($start_date) . "'";
        }
        
        if (!empty($end_date)) {
            $query .= " AND si.created_at <= '" . $conn->real_escape_string($end_date) . " 23:59:59'";
        }
        
        if (!empty($search_term)) {
            $query .= " AND (si.item_name LIKE '%" . $conn->real_escape_string($search_term) . "%' 
                          OR stock_items.category LIKE '%" . $conn->real_escape_string($search_term) . "%')";
        }
        
        // Group by location
        
         $query .= " GROUP BY si.id";
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
                                <th>Name</th>
                                <th>Quanity in / out</th>
                                <th>Stock out Price</th>
                                <th>Total Price</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php $counter = 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                        <td> <font style=" color: darkgray; " ><?php echo number_format($row['in_quanity'])." / " ? : ''; ?></font><?php echo number_format($row['out_quanity']); ?></td>
                                        <td><?php echo number_format($row['out_price']); ?> Frw</td>
                                        <td><?php echo number_format($row['out_price'] * $row['out_quanity']); ?> Frw</td>
                                        <td>
                                        <?php
                                        
                                        $total = $row['total_out'] * $row['total_out_price'];
                                        $total_row = $row['out_price'] * $row['out_quanity'];

                                        $tota_deference = $total - $total_row;

                                        if( $tota_deference < $total_row ){

                                            $result = "High";
                                        }else if( $tota_deference > $total_row ){

                                            $result = "Low";

                                        }

                                        ?><b class="<?php echo $result ?>"><?php echo $result ?></b>
                                        

                                        </td>
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