<?php
// Database connection
include_once '../config.php';

// Function to handle database errors
function handleDbError($conn) {
    die("Database error: " . mysqli_error($conn));
}

// Get search term if exists
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch transaction reports data
function getTransactionReports($conn, $searchTerm = '') {
    $query = "SELECT 
    st.id,
    st.item_name,
    st.category,
    st.quantity,
    st.unit_price,
    st.created_at,
        COALESCE(si.quantity, 0) AS stockout_amount
    FROM stock_items st
    LEFT JOIN stockout si ON st.id = si.in_id
    GROUP BY st.id
    ORDER BY st.created_at DESC ";
    
    
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        handleDbError($conn);
    }
    
    $transactions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
    
    return $transactions;
}

$transactions = getTransactionReports($conn, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department - Stock Transaction Reports</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/report.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <style></style>
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="user-grid">
            <div class="header">
                <h2>Stock Transaction Reports</h2>
                <form method="GET" action="" class="searchbar">
                    <input type="search" name="search" placeholder="Search transactions..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="search-btn"> <i class=" fa fa-search "></i> </button>
                </form>
            </div>
            
            <div class="report-table">
                <?php if (count($transactions) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No<?php $i =1; ?></th>
                                <th>Item</th>
                                <th>Type</th>
                                <th class="numeric-cell">Qty In</th>
                                <th class="numeric-cell">Qty Out</th>
                                <th class="numeric-cell">Price</th>
                                <th class="numeric-cell">Value Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo $i; $i++; ?></td>
                                    <td><?php echo htmlspecialchars($transaction['item_name'] ?? 'N/A'); ?></td>
                                    <td class="transaction"><?php echo $transaction['category']; ?></td>
                                    <td class="numeric-cell"><?php echo $transaction['quantity'] > 0 ? number_format($transaction['quantity']) : '-'; ?></td>
                                    <td class="numeric-cell"><?php echo $transaction['stockout_amount'] > 0 ? number_format($transaction['stockout_amount']) : '-'; ?></td>
                                    <td class="numeric-cell"><?php echo $transaction['unit_price'] > 0 ? number_format($transaction['unit_price']) : '-'; ?> Rwf</td>
                                    <td class="numeric-cell"><?php echo $transaction['created_at'];  ?></td>
                                    
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-records">
                        No transactions found. <?php echo !empty($searchTerm) ? 'Try a different search term.' : ''; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function viewTransaction(transactionId) {
            // In a real implementation, this would open a modal with transaction details
            alert('Viewing transaction ID: ' + transactionId);
            // window.location.href = 'view_transaction.php?id=' + transactionId;
        }

        function exportTransaction(transactionId) {
            // In a real implementation, this would export the transaction data
            alert('Exporting transaction ID: ' + transactionId);
            // window.location.href = 'export_transaction.php?id=' + transactionId;
        }

        // Make table header sticky when scrolling
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            const tableHeader = document.querySelectorAll('.report-table th');
            const scrollPosition = window.scrollY;
            
            if (scrollPosition > 100) {
                header.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
                tableHeader.forEach(th => {
                    th.style.top = '60px';
                });
            } else {
                header.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';
                tableHeader.forEach(th => {
                    th.style.top = '80px';
                });
            }
        });
    </script>
</body>
</html>