<?php
// Database connection
include_once '../config.php';

// Function to handle database errors
function handleDbError($conn) {
    die("Database error: " . mysqli_error($conn));
}

// Get search term if exists
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Handle export requests
if (isset($_GET['export'])) {
    $exportType = $_GET['export'];
    $transactions = getTransactionReports($conn, $searchTerm);
    
    if ($exportType === 'excel') {
        exportToExcel($transactions);
    } elseif ($exportType === 'pdf') {
        exportToPDF($transactions);
    }
    exit;
}

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

// Export to Excel function
function exportToExcel($transactions) {
    $filename = "stock_transactions_" . date('Y-m-d_H-i-s') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for proper UTF-8 encoding in Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header row
    fputcsv($output, ['No', 'Item Name', 'Category', 'Qty In', 'Qty Out', 'Unit Price (Rwf)', 'Value Out', 'Date Created']);
    
    // Data rows
    $i = 1;
    foreach ($transactions as $transaction) {
        $row = [
            $i++,
            $transaction['item_name'] ?? 'N/A',
            $transaction['category'] ?? 'N/A',
            $transaction['quantity'] > 0 ? number_format($transaction['quantity']) : '-',
            $transaction['stockout_amount'] > 0 ? number_format($transaction['stockout_amount']) : '-',
            $transaction['unit_price'] > 0 ? number_format($transaction['unit_price']) : '-',
            ($transaction['stockout_amount'] > 0 && $transaction['unit_price'] > 0) ? 
                number_format($transaction['stockout_amount'] * $transaction['unit_price']) : '-',
            $transaction['created_at']
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
}

// Export to PDF function (requires TCPDF library)
function exportToPDF($transactions) {
    // Note: This requires TCPDF library to be installed
    // You can install it via composer: composer require tecnickcom/tcpdf
    
    require_once('../vendor/tcpdf/tcpdf.php'); // Adjust path as needed
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Stock Management System');
    $pdf->SetAuthor('Admin Department');
    $pdf->SetTitle('Stock Transaction Reports');
    $pdf->SetSubject('Stock Transaction Reports');
    
    // Set margins
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 20);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 12);
    
    // Title
    $pdf->Cell(0, 10, 'Stock Transaction Reports', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
    $pdf->Ln(5);
    
    // Table header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(15, 8, 'No', 1, 0, 'C');
    $pdf->Cell(35, 8, 'Item Name', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Category', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Qty In', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Qty Out', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Price (Rwf)', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Value Out', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Date', 1, 1, 'C');
    
    // Table data
    $pdf->SetFont('helvetica', '', 9);
    $i = 1;
    foreach ($transactions as $transaction) {
        $pdf->Cell(15, 6, $i++, 1, 0, 'C');
        $pdf->Cell(35, 6, substr($transaction['item_name'] ?? 'N/A', 0, 20), 1, 0, 'L');
        $pdf->Cell(25, 6, substr($transaction['category'] ?? 'N/A', 0, 15), 1, 0, 'L');
        $pdf->Cell(20, 6, $transaction['quantity'] > 0 ? number_format($transaction['quantity']) : '-', 1, 0, 'C');
        $pdf->Cell(20, 6, $transaction['stockout_amount'] > 0 ? number_format($transaction['stockout_amount']) : '-', 1, 0, 'C');
        $pdf->Cell(25, 6, $transaction['unit_price'] > 0 ? number_format($transaction['unit_price']) : '-', 1, 0, 'R');
        $valueOut = ($transaction['stockout_amount'] > 0 && $transaction['unit_price'] > 0) ? 
            number_format($transaction['stockout_amount'] * $transaction['unit_price']) : '-';
        $pdf->Cell(25, 6, $valueOut, 1, 0, 'R');
        $pdf->Cell(25, 6, date('Y-m-d', strtotime($transaction['created_at'])), 1, 1, 'C');
    }
    
    // Output PDF
    $filename = "stock_transactions_" . date('Y-m-d_H-i-s') . ".pdf";
    $pdf->Output($filename, 'D');
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
    <style>
        .export-buttons {
            margin: 20px;
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .export-btn.excel {
            background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%);
        }
        
        .export-btn.excel:hover {
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        
        .export-btn.pdf {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        }
        
        .export-btn.pdf:hover {
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        /* Enhanced Search Bar Styles */
        .searchbar {
            position: relative;
            display: flex;
            align-items: center;
            background: transparent;
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 2px solid rgb(0, 23, 46);
            /* border-radius: 25px; */
            padding: 5px;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); */
            transition: all 0.3s ease;
            min-width: 300px;
        }
        
        .searchbar:focus-within {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .searchbar input[type="search"] {
            flex: 1;
            border: none;
            outline: none;
            padding: 12px 20px;
            font-size: 14px;
            background: transparent;
            color: #333;
            border-radius: 20px;
        }
        
        .searchbar input[type="search"]::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .searchbar input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }
        
        .search-btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: scale(1.05);
        }
        
        .search-btn:active {
            transform: scale(0.95);
        }
        
        .search-results-info {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e1e5e9;
            border-radius: 10px;
            padding: 10px 15px;
            margin-top: 5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 12px;
            color: #666;
            z-index: 10;
        }
        
        .search-clear {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .search-clear:hover {
            background: #f0f0f0;
            color: #333;
        }
        
        .search-clear.show {
            display: flex;
        }
        
        /* Loading animation for search */
        .search-loading {
            position: absolute;
            right: 55px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }
        
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
        
        /* Table row highlighting for search results */
        .report-table tbody tr.highlight {
            background-color: #fff3cd;
            transition: background-color 0.3s ease;
        }
        
        .report-table tbody tr.hidden {
            display: none;
        }
        
        /* No results message */
        .no-search-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .no-search-results i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 10px;
        }
        
        /* Responsive search bar */
        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .header-left {
                width: 100%;
                flex-direction: column;
                gap: 15px;
            }
            
            .searchbar {
                min-width: 100%;
                max-width: 100%;
            }
            
            .export-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="user-grid">
            <div class="header-actions">
                <div class="header-left">
                    <h2></h2>
                    <div class="searchbar">
                        <input type="search" id="searchInput" placeholder="Search transactions..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <div class="search-loading" id="searchLoading"></div>
                        <button type="button" class="search-clear" id="searchClear">
                            <i class="fas fa-times"></i>
                        </button>
                        <button type="button" class="search-btn" id="searchBtn">
                            <i class="fa fa-search"></i>
                        </button>
                        <div class="search-results-info" id="searchInfo" style="display: none;"></div>
                    </div>
                </div>
                
                <div class="export-buttons">
                    <a href="?export=excel<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                       class="export-btn excel">
                        <i class="fas fa-file-excel"></i>
                        Export to Excel
                    </a>
                    <a href="?export=pdf<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                       class="export-btn pdf">
                        <i class="fas fa-file-pdf"></i>
                        Export to PDF
                    </a>
                </div>
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
                                <th class="numeric-cell">Date</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTableBody">
                            <?php foreach ($transactions as $transaction): ?>
                                <tr class="transaction-row" 
                                    data-item="<?php echo strtolower($transaction['item_name'] ?? ''); ?>"
                                    data-category="<?php echo strtolower($transaction['category'] ?? ''); ?>"
                                    data-date="<?php echo $transaction['created_at']; ?>"
                                    data-quantity="<?php echo $transaction['quantity']; ?>"
                                    data-stockout="<?php echo $transaction['stockout_amount']; ?>"
                                    data-price="<?php echo $transaction['unit_price']; ?>">
                                    <td><?php echo $i; $i++; ?></td>
                                    <td><?php echo htmlspecialchars($transaction['item_name'] ?? 'N/A'); ?></td>
                                    <td class="transaction"><?php echo $transaction['category']; ?></td>
                                    <td class="numeric-cell"><?php echo $transaction['quantity'] > 0 ? number_format($transaction['quantity']) : '-'; ?></td>
                                    <td class="numeric-cell"><?php echo $transaction['stockout_amount'] > 0 ? number_format($transaction['stockout_amount']) : '-'; ?></td>
                                    <td class="numeric-cell"><?php echo $transaction['unit_price'] > 0 ? number_format($transaction['unit_price']) : '-'; ?> Rwf</td>
                                    <td class="numeric-cell">
                                        <?php 
                                        if ($transaction['stockout_amount'] > 0 && $transaction['unit_price'] > 0) {
                                            echo number_format($transaction['stockout_amount'] * $transaction['unit_price']) . ' Rwf';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="numeric-cell"><?php echo date('Y-m-d', strtotime($transaction['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-records">
                        <i class="fas fa-inbox"></i>
                        <p>No transactions found. <?php echo !empty($searchTerm) ? 'Try a different search term.' : ''; ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="no-search-results" id="noSearchResults" style="display: none;">
                    <i class="fas fa-search"></i>
                    <p>No transactions match your search criteria.</p>
                    <small>Try adjusting your search terms or clear the search to see all transactions.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Real-time search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        const searchLoading = document.getElementById('searchLoading');
        const searchInfo = document.getElementById('searchInfo');
        const transactionRows = document.querySelectorAll('.transaction-row');
        const noSearchResults = document.getElementById('noSearchResults');
        const reportTable = document.querySelector('.report-table table');
        
        // Initialize search functionality
        function initializeSearch() {
            searchInput.addEventListener('input', handleSearch);
            searchClear.addEventListener('click', clearSearch);
            
            // Show/hide clear button based on input
            searchInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    searchClear.classList.add('show');
                } else {
                    searchClear.classList.remove('show');
                }
            });
            
            // Initial state
            if (searchInput.value.trim() !== '') {
                searchClear.classList.add('show');
            }
        }
        
        // Handle search input
        function handleSearch() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Show loading animation
            searchLoading.style.display = 'block';
            
            // Debounce search for better performance
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
                searchLoading.style.display = 'none';
            }, 300);
        }
        
        // Perform the actual search
        function performSearch(searchTerm) {
            let visibleCount = 0;
            let totalCount = transactionRows.length;
            
            transactionRows.forEach((row, index) => {
                const itemName = row.getAttribute('data-item');
                const category = row.getAttribute('data-category');
                const date = row.getAttribute('data-date');
                const quantity = row.getAttribute('data-quantity');
                const stockout = row.getAttribute('data-stockout');
                const price = row.getAttribute('data-price');
                
                // Check if search term matches any field
                const matches = 
                    itemName.includes(searchTerm) ||
                    category.includes(searchTerm) ||
                    date.includes(searchTerm) ||
                    quantity.includes(searchTerm) ||
                    stockout.includes(searchTerm) ||
                    price.includes(searchTerm);
                
                if (searchTerm === '' || matches) {
                    row.style.display = '';
                    row.classList.remove('hidden');
                    visibleCount++;
                    
                    // Update row number
                    const firstCell = row.querySelector('td:first-child');
                    if (firstCell) {
                        firstCell.textContent = visibleCount;
                    }
                    
                    // Highlight matching text
                    if (searchTerm !== '' && matches) {
                        highlightMatchingText(row, searchTerm);
                    } else {
                        removeHighlight(row);
                    }
                } else {
                    row.style.display = 'none';
                    row.classList.add('hidden');
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0 && searchTerm !== '') {
                noSearchResults.style.display = 'block';
                reportTable.style.display = 'none';
            } else {
                noSearchResults.style.display = 'none';
                reportTable.style.display = '';
            }
            
            // Update search info
            updateSearchInfo(searchTerm, visibleCount, totalCount);
        }
        
        // Highlight matching text in table cells
        function highlightMatchingText(row, searchTerm) {
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchTerm)) {
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    cell.innerHTML = cell.textContent.replace(regex, '<mark>$1</mark>');
                }
            });
        }
        
        // Remove highlight from table cells
        function removeHighlight(row) {
            const marks = row.querySelectorAll('mark');
            marks.forEach(mark => {
                mark.outerHTML = mark.innerHTML;
            });
        }
        
        // Update search information
        function updateSearchInfo(searchTerm, visibleCount, totalCount) {
            if (searchTerm === '') {
                searchInfo.style.display = 'none';
                return;
            }
            
            let infoText = '';
            if (visibleCount === 0) {
                infoText = `No results found for "${searchTerm}"`;
            } else if (visibleCount === totalCount) {
                infoText = `All ${totalCount} transactions match your search`;
            } else {
                infoText = `Showing ${visibleCount} of ${totalCount} transactions`;
            }
            
            searchInfo.textContent = infoText;
            searchInfo.style.display = 'block';
            
            // Hide info after 3 seconds
            setTimeout(() => {
                searchInfo.style.display = 'none';
            }, 3000);
        }
        
        // Clear search function
        function clearSearch() {
            searchInput.value = '';
            searchClear.classList.remove('show');
            searchInfo.style.display = 'none';
            performSearch('');
        }
        
        // Initialize search when page loads
        document.addEventListener('DOMContentLoaded', initializeSearch);
        
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
            const header = document.querySelector('.header-actions');
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

        // Loading state for export buttons
        document.querySelectorAll('.export-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
                this.style.pointerEvents = 'none';
                
                // Reset button after 3 seconds (in case of issues)
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                }, 3000);
            });
        });
        
        // Add CSS for highlighted text
        const style = document.createElement('style');
        style.textContent = `
            mark {
                background-color: #ffeb3b;
                color: #333;
                padding: 0 2px;
                border-radius: 2px;
                font-weight: bold;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>