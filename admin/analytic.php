<?php
// Database connection
include_once '../config.php';

// Function to handle database errors
function handleDbError($conn) {
    die("Database error: " . mysqli_error($conn));
}

// Fetch statistics data
function getStatData($conn, $type) {
    switch ($type) {
        case 'total_items':
            $query = "SELECT COUNT(*) as count FROM stock_items";
            break;
        case 'stock_in':
            $query = "SELECT SUM(quantity) as total FROM stock_items 
                     WHERE status = 'in-stock' AND MONTH(created_at) = MONTH(CURRENT_DATE())";
            break;
        case 'stock_out':
            $query = "SELECT SUM(quantity) as total FROM stock_items 
                     WHERE status = 'out-of-stock' AND MONTH(created_at) = MONTH(CURRENT_DATE())";
            break;
        case 'total_value':
            $query = "SELECT SUM(total_value) as total FROM stock_items";
            break;
        default:
            return 0;
    }
    
    $result = mysqli_query($conn, $query);
    if (!$result) {
        handleDbError($conn);
    }
    
    $row = mysqli_fetch_assoc($result);
    return $row['count'] ?? $row['total'] ?? 0;
}

// Fetch category distribution
function getCategoryData($conn) {
    $query = "SELECT category, SUM(quantity) as total_quantity, 
              SUM(total_value) as total_value FROM stock_items 
              GROUP BY category";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        handleDbError($conn);
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['category']] = [
            'quantity' => $row['total_quantity'],
            'value' => $row['total_value'],
            'percentage' => 0 // Will calculate later
        ];
    }
    
    // Calculate percentages
    $total = array_sum(array_column($data, 'quantity'));
    if ($total > 0) {
        foreach ($data as &$category) {
            $category['percentage'] = round(($category['quantity'] / $total) * 100, 2);
        }
    }
    
    return $data;
}

// Fetch recent stock movements
function getRecentStock($conn, $type, $limit = 5) {
    $status = ($type == 'in') ? 'in-stock' : 'out-of-stock';
    $query = "SELECT item_name, category, quantity, unit_price, total_value, location, 
              status, created_at FROM stock_items 
              WHERE status = '$status' 
              ORDER BY created_at DESC LIMIT $limit";
              
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        handleDbError($conn);
    }
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}

// Fetch locations
function getLocations($conn) {
    $query = "SELECT DISTINCT location FROM stock_items 
              WHERE location IS NOT NULL AND location != ''";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        handleDbError($conn);
    }
    
    $locations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $locations[] = $row['location'];
    }
    
    return $locations;
}

// Helper function to get location stats
function getLocationStats($conn, $location) {
    $query = "SELECT COUNT(*) as item_count, SUM(total_value) as total_value 
              FROM stock_items WHERE location = '" . mysqli_real_escape_string($conn, $location) . "'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        handleDbError($conn);
    }
    
    return mysqli_fetch_assoc($result);
}

// Get all data
$stats = [
    'total_items' => getStatData($conn, 'total_items'),
    'stock_in' => getStatData($conn, 'stock_in'),
    'stock_out' => getStatData($conn, 'stock_out'),
    'total_value' => getStatData($conn, 'total_value')
];

$categoryData = getCategoryData($conn);
$recentStockIn = getRecentStock($conn, 'in');
$recentStockOut = getRecentStock($conn, 'out');
$locations = getLocations($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
.active_analytic {
    background-color: var(--primary-color);
    color: white;

 }

.payroll-stats-grid {
    background: #D0E0FF;
    width: 100%;
    height: 9vh;
    border-radius: 15px;
}

.analytic-content {
    display: flex;
    justify-content: space-between;
    
}

.payroll-bottom-grid {
    background: #D0E0FF;
    width: 79%;
    height: 73vh;
    border-radius: 15px;
    margin-top: 20px;
}
.right-grid {
    /* background: #D0E0FF; */
    width: 20%;
    height: 73vh;
    border-radius: 15px;
    margin-top: 20px;
}
.right-top-grid
{
    background: #D0E0FF;
    width: 100%;
    height: 35vh;
    border-radius: 15px;
    /* margin-top: 20px; */
}
.right-buttom-grid
{
    background: #D0E0FF;
    width: 100%;
    height: 35vh;
    border-radius: 15px;
    margin-top: 20px;
}


/* Navigation Tabs */
.nav-tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
            padding: 10px;
            
        }
        .nav-tab:hover {
            background-color: #4a5568;
            color: white;
        }

        .nav-tab {
            background-color: #2d4a5b;
            color: white;
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            flex-grow: 1;
            cursor: pointer;
            
        }

        .nav-tab svg {
            margin-right: 10px;
        }

        .nav-tab:last-child {
            background-color: #6b46c1;
        }


         /* Stats Cards */

         .stats-cards {
            display: flex;
            gap: 5px;
            /* margin-bottom: 10px; */
            padding: 0 10px;
        }

        .stat-card {
            /* flex: 1; */
            border-radius: 10px;
            padding: 10px;
            color: #2d3748;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            width: 50%;
            height: 250px;
            margin: 10px 0;
            
        }

        .stat-card.yellow {
            background-color: #fbd38d;
        }

        .stat-card.green {
            background-color: #1f3828;
            color: white;
        }

        .stat-card.red {
            background-color: #f56565;
            color: white;
        }

        .stat-number {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .stat-description {
            font-size: 12px;
            opacity: 0.8;
        }

        .icon-container {
            margin-bottom: 15px;
        }

        .icon-container svg {
            width: 40px;
            height: 40px;
        }
        .top-row
        {
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
            /* background: #000; */
            padding: 10px;
        }
        /* Wide card grid */
        .wide-card-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
        }

        .stock-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .card.small {
            height: 200px;
            overflow-y: auto;
        }

        .card.medium {
            height: 300px;
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .predition-card
        {
            width: 50%;
            height: 100%;
            background: white;
            padding: 10px;
            margin: 0 0px 0 10px;
            font-size: 12px;
            border-radius: 10px;
            overflow-y: auto;
        }
        .marketing-card
        {
            width: 50%;
            height: 100%;
            background: white;
            padding: 10px;
            margin: 0 0px 0 10px;
            font-size: 12px;
            border-radius: 10px;
            overflow-y: auto;
        }
        .stock-out-card
        {
            width: 90%;
            height: 250px;
            background: white;
            padding: 10px;
            font-size: 12px;
            border-radius: 10px;
            overflow-y: auto;



            
        }

        .card-header {
            font-weight: 600;
            margin-bottom: 15px;
        }

      

        /* Progress Bars */
        .progress-item {
            margin-bottom: 15px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .progress-bar {
            height: 3px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-value {
            height: 100%;
            border-radius: 4px;
        }

        .gold {
            background-color: #f6ad55;
        }

        .diamond {
            background-color: #63b3ed;
        }

        .platinum {
            background-color: #48bb78;
        }

        .fibrate {
            background-color: #8fb317;
        }

        .premium {
            background-color: #9f7aea;
        }

        /* Marketing Table */
        .marketing-table {
            width: 100%;
            border-collapse: collapse;
        }

        .marketing-table th {
            text-align: left;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .marketing-table td {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Dropdown */
        .dropdown {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .dropdown select {
            border: none;
            background-color: transparent;
            font-size: 14px;
            color: #4a5568;
        }

        /* List */
        .list-item {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Wide card grid */
        .wide-card-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
        }

        .stock-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .card.small {
            height: 200px;
            overflow-y: auto;
        }

        .card.medium {
            height: 300px;
        }

        .bottom-rows
        {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            height: 15rem;
            padding: 10px;
        }
        .card.small {
            height: 100%;
            overflow-y: auto;
            font-size: 12px;
            border-radius: 10px;

        }



</style>
<body>
    <?php include 'main/slidebar.php'; ?>
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="analytic-content">
            <div class="payroll-bottom-grid">
                <div class="top-row">
                    <div class="stats-cards">
                        <div class="stat-card yellow">
                            <div class="type">Total Items</div>
                            <div class="icon-container">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['total_items']); ?></div>
                            <div class="stat-description">ITEMS IN INVENTORY</div>
                        </div>
                        <div class="stat-card green">
                            <div class="type">Stock In</div>
                            <div class="icon-container">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['stock_in']); ?></div>
                            <div class="stat-description">ITEMS THIS MONTH</div>
                        </div>
                        <div class="stat-card red">
                            <div class="type">Stock Out</div>
                            <div class="icon-container">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['stock_out']); ?></div>
                            <div class="stat-description">ITEMS THIS MONTH</div>
                        </div>
                        <div class="stat-card blue">
                            <div class="type">Total Value</div>
                            <div class="icon-container">
                                Frw
                            </div>
                            <div class="stat-number">Frw<?php echo number_format($stats['total_value']); ?></div>
                            <div class="stat-description">INVENTORY VALUE</div>
                        </div>
                    </div>

                    <div class="stock-out-card">
                        <div class="card-header">Category Distribution</div>
                        <?php foreach ($categoryData as $category => $data): ?>
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span><?php echo htmlspecialchars($category); ?></span>
                                    <span><?php echo $data['percentage']; ?>%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-value" style="width: <?php echo $data['percentage']; ?>%; background-color: <?php echo getColorForCategory($category); ?>"></div>
                                </div>
                                <div class="progress-details">
                                    Qty: <?php echo $data['quantity']; ?> | Value: Frw<?php echo number_format($data['value']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="bottom-rows">
                    <div class="predition-card">
                        <div class="card-header">Recent Stock In</div>
                        <table class="marketing-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentStockIn as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo htmlspecialchars($item['location']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="marketing-card">
                        <div class="card-header">Recent Stock Out</div>
                        <table class="marketing-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Value</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentStockOut as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>Frw<?php echo number_format($item['total_value']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="right-grid">
                <div class="right-top-grid">
                    <div class="card small">
                        <div class="dropdown">
                            <select onchange="filterData('stock', this.value)" style="width: 100%; background-color: lightgray;">
                                <option value="monthly">monthly</option>
                                <option value="weekly">weekly</option>
                                <option value="daily">daily</option>
                            </select>
                        </div>
                        <div><h4>Recent Stock Movements</h4></div>
                        <?php 
                        $recentMovements = array_merge($recentStockIn, $recentStockOut);
                        usort($recentMovements, function($a, $b) {
                            return strtotime($b['created_at']) - strtotime($a['created_at']);
                        });
                        $recentMovements = array_slice($recentMovements, 0, 5);
                        ?>
                        <?php foreach ($recentMovements as $item): ?>
                            <div class="list-item">
                                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                <span style="float: right; color: <?php echo $item['status'] == 'in-stock' ? 'green' : 'red'; ?>">
                                    <?php echo $item['status'] == 'in-stock' ? '+' : '-'; ?><?php echo $item['quantity']; ?>
                                </span>
                                <div style="font-size: 0.8em; color: #666;">
                                    <?php echo date('M d', strtotime($item['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="right-buttom-grid">
                    <div class="card small">
                        <div class="dropdown">
                            <select onchange="filterData('locations', this.value)" style="width: 100%; background-color: lightgray;">
                                <option value="all">all</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div><h4>Inventory Locations</h4></div>
                        <?php foreach ($locations as $location): ?>
                            <?php 
                            $locStats = getLocationStats($conn, $location);
                            $itemCount = $locStats['item_count'];
                            $totalValue = $locStats['total_value'];
                            ?>
                            <div class="list-item">
                                <strong><?php echo htmlspecialchars($location); ?></strong>
                                <span style="float: right;">
                                    <?php echo $itemCount; ?> items
                                </span>
                                <div style="font-size: 0.8em; color: #666;">
                                    Frw<?php echo number_format($totalValue); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterData(type, value) {
            // In a real implementation, this would fetch filtered data
            console.log(`Filtering ${type} by ${value}`);
            // You would typically make an AJAX call here to get filtered data
            // and then update the relevant sections of the dashboard
        }
        
        function refreshDashboard() {
            fetch('get_dashboard_data.php')
                .then(response => response.json())
                .then(data => {
                    // Update all dashboard elements with fresh data
                    document.querySelector('.stat-card.yellow .stat-number').textContent = data.total_items;
                    document.querySelector('.stat-card.green .stat-number').textContent = data.stock_in;
                    document.querySelector('.stat-card.red .stat-number').textContent = data.stock_out;
                    document.querySelector('.stat-card.blue .stat-number').textContent = '$' + data.total_value.toLocaleString();
                    
                    // Update category distribution
                    const categoryContainer = document.querySelector('.stock-out-card');
                    categoryContainer.innerHTML = '<div class="card-header">Category Distribution</div>';
                    
                    data.categories.forEach(category => {
                        categoryContainer.innerHTML += `
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span>${category.name}</span>
                                    <span>${category.percentage}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-value" style="width: ${category.percentage}%; background-color: ${category.color}"></div>
                                </div>
                                <div class="progress-details">
                                    Qty: ${category.quantity} | Value: $${category.value.toLocaleString()}
                                </div>
                            </div>
                        `;
                    });
                    
                    // Similar updates for other sections...
                });
        }
        
        // Refresh every 5 minutes
        setInterval(refreshDashboard, 300000);
    </script>
</body>
</html>

<?php
// Helper function to get color for categories
function getColorForCategory($category) {
    $colors = [
        'gold' => '#f6ad55',
        'diamond' => '#63b3ed',
        'platinum' => '#48bb78',
        'fibrate' => '#8fb317',
        'premium' => '#9f7aea'
    ];
    
    return $colors[strtolower($category)] ?? '#6b46c1';
}

// Close the database connection
mysqli_close($conn);
?>