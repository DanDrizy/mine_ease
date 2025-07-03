
<?php

// include'../config.php';

require_once '../config.php';

$sel = mysqli_query($conn, "SELECT 
    *,
    si.quantity AS stock_quantity,
    si.unit_price AS stock_unit_price,
    so.quantity AS stockout_quantity,
    so.unit_price AS stockout_unit_price
FROM stock_items si
LEFT JOIN stockout so ON si.id = so.in_id AND so.quantity > 0;

");

$i =1;
$y =1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/finance.css">
    <style></style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Financial";
        
        
        
        include 'main/sidebar.php'; 
        
        ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'main/header.php'; ?>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid-finance">
                <!-- Search and Filter Section -->
                <form method="GET" action="" class="search">
                    
                    
                    <label for="">Start Date: </label> <input type="date" name="start_date">
                    <label for="">End Date: </label> <input type="date" name="end_date">
                    
                    <div class="field">
                        <input type="search" name="search" placeholder="Search items or categories" value="">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                
                <!-- Financial Data Table -->
                <div class="finance-table">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="6"  style=" background:transparent; color: darkcyan; border: none; " > <H2>Stock in</H2> </th>
                                <th colspan="5"  style=" background:transparent; color: darkcyan; border: none; "> <H2>Stock out</H2> </th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Stock-In</th>
                                <th>Amount</th>
                                <th>Price</th>
                                <th>Total Price</th>
                                <th></th>
                                <th>No</th>
                                <th>Stock-Out</th>
                                <th>Amount</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while( $row = mysqli_fetch_array($sel) ) : ?>
                                    <tr>
                                        <td> <?php echo $i; $i++; ?> </td>
                                        <td> <?php echo $row['item_name'] ?> </td>
                                        <td> <?php echo number_format($row['stock_quantity']) ?> </td>
                                        <td> <?php echo number_format($row['stock_unit_price']) ?> RWF </td>
                                        <td> <?php echo number_format($row['total_value']) ?> RWF </td>
                                        <td></td>
                                        <td> <?php echo $y; $y++; ?> </td>
                                        <td> <?php echo $row['item_name'] ?> </td>
                                        <td> <?php echo number_format($row['stockout_quantity']) ? : 0 ?>  </td>
                                        <td> <?php echo number_format($row['stockout_unit_price']) ? : 0 ?> RWF </td>
                                        <td> <?php echo number_format($row['stockout_quantity'] * $row['stockout_unit_price']) ;  ?> Rwf </td>
                                        
                                    </tr>
                            <?php endwhile; ?>
                                
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