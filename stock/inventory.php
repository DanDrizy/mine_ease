<?php
// Database connection (adjust credentials as needed)
include_once '../config.php';

// Handle date filtering
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to first day of current month
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Default to today

// Prepare SQL query with date filtering
$sql = "SELECT 
            item_name, 
            location, 
            category AS department, 
            DATE(created_at) AS date,
            quantity,
            unit_price,
            total_value,
            status
        FROM stock_items 
        WHERE created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
        ORDER BY created_at DESC";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);

// Close statement and connection (will be closed at end of script)
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        /* Original styles */
        .active-inventory-reports{
            background-color: #7A4B9D;
            color: white;
        }

        .dashboard-cards-stock {
            width: 100%;  
            margin: 10px 0px 20px;
        }
        .card-stock {
            background: #D0E0FF;
            width: 100%;
            overflow: hidden;
        }
        .stock-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
        }
        .stock-in, .stock-out {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #4768A8;
            width: 40%;
            padding: 10px;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .stock-in.active, .stock-out.active {
            background: #7A4B9D;
        }
        .stock-in:hover, .stock-out:hover {
            background: #5a78b8;
        }
        .detail {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail img {
            width: 40px;
            height: 40px;
        }
        .bottom-cards-stock {
            background: #D0E0FF;
            width: 100%;
            height: 70vh;
            border-radius: 10px;
            padding: 20px;
            overflow: auto;
        }
        .search-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #4768A8;
        }
        .search-input {
            padding: 10px;
            border-radius: 5px;
            border: none;
            width: 300px;
            border: 2px solid transparent;
        }
        .search-input:focus {
            outline: none;
            border: 2px solid #7A4B9D;
        }
        .search-button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background:rgb(186, 95, 255);
            color: white;
            cursor: pointer;
        }
        .adding button {
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid #5a3e7c;
            background: white;
            color:rgb(186, 95, 255);
            cursor: pointer;
        }
        .stock-table {
            width: 100%;
            overflow: auto;
        }
        .stock-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .stock-table th, .stock-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .stock-table th {
            background: #4768A8;
            color: white;
        }
        .stock-table tr:hover {
            background: #f1f1f1;
        }
        .stock-table button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            background: #7A4B9D;
            color: white;
            cursor: pointer;
        }
        .stock-table button:hover {
            background: #5a3e7c;
        }
        .stock-in-table, .stock-out-table {
            display: none;
        }
        .stock-in-table.active, .stock-out-table.active {
            display: block;
        }
        .table-title {
            margin-top: 0;
            margin-bottom: 15px;
            color: #fff;
            font-size: 1.5rem;
        }
        
        /* Additional styles for the report */
        .report-summary {
            background: #4768A8;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .summary-item {
            text-align: center;
        }
        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

   <!-- Sidebar -->
    <?php 
    $page_name = " / Inventory Management "; // Set the page name for the header
    include 'main/sidebar.php'; 
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <div class="bottom-cards-stock">
            <div class="search-box">
                <h3 style="color: white;">Inventory Report</h3>
                <form method="GET" class="adding">
                    <input type="date" name="start_date" class="search-input" 
                           value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="date" name="end_date" class="search-input" 
                           value="<?php echo htmlspecialchars($end_date); ?>">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
            
            <!-- Report Summary -->
            <div class="report-summary">
                <?php
                // Calculate summary statistics
                $total_items = count($items);
                $total_value = 0;
                $total_quantity = 0;
                
                foreach ($items as $item) {
                    $total_value += $item['total_value'];
                    $total_quantity += $item['quantity'];
                }
                
                $avg_price = $total_quantity > 0 ? $total_value / $total_quantity : 0;
                
                // Count items by status
                $status_counts = array();
                foreach ($items as $item) {
                    $status = $item['status'];
                    if (!isset($status_counts[$status])) {
                        $status_counts[$status] = 0;
                    }
                    $status_counts[$status]++;
                }
                
                $in_stock = $status_counts['in_stock'] ?? 0;
                $out_of_stock = $status_counts['out_of_stock'] ?? 0;
                ?>
                
                <div class="summary-item">
                    <div class="summary-label">Total Items</div>
                    <div class="summary-value"><?php echo $total_items; ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Value</div>
                    <div class="summary-value">Frw <?php echo number_format($total_value, 2); ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Avg. Price</div>
                    <div class="summary-value">Frw <?php echo number_format($avg_price, 2); ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">In Stock</div>
                    <div class="summary-value"><?php echo $in_stock; ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Out of Stock</div>
                    <div class="summary-value"><?php echo $out_of_stock; ?></div>
                </div>
            </div>
            
            <!-- Inventory Table -->
            <div id="stockInTable" class="stock-table stock-in-table active">
                <table>
                    <tr>
                        <th>Item Name</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No inventory items found for the selected date range.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['location']); ?></td>
                                <td><?php echo htmlspecialchars($item['department']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>Frw <?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>Frw <?php echo number_format($item['total_value'], 2); ?></td>
                                <td>
                                    <span style="color: <?php echo $item['status'] == 'in_stock' ? 'green' : 'red'; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($item['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <?php
    // Close connection
    $conn->close();
    ?>
</body>
</html>