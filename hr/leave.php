<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page title
$page_name = 'Leave Requests';

// Database connection (example - adjust according to your setup)
require_once '../config.php';

// Function to get leave requests from database
function getLeaveRequests($conn) {
    $requests = [];
    $query = "SELECT * FROM leave_requests WHERE status = 'pending'";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Database error: " . $conn->error);
    }
    return $requests;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $requestId = $_POST['request_id'];
        $action = $_POST['action'];
        
        if ($action === 'accept') {
            $query = "UPDATE leave_requests SET status = 'approved' WHERE id = ?";
        } elseif ($action === 'deny') {
            $query = "UPDATE leave_requests SET status = 'denied' WHERE id = ?";
        }
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $requestId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Leave request " . ($action === 'accept' ? 'approved' : 'denied') . " successfully";
            } else {
                $_SESSION['error'] = "Error processing request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        }
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Get leave requests from database
$leaveRequests = getLeaveRequests($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <!-- Include libraries for PDF and Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<style>
    .active-leave {
        background-color: #81C5B1;
        transition: background-color 0.3s ease;
        color: #1E1E1E;
    }
    .dashboard-row {
        height: 100vh;
    }
    .dashboard-column {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background:#fff;
        padding: 20px;
        border-radius: 20px;
    }

    .table-emp {
        width: 100%;
        height: 100%;
    }

    .table-emp table {
        width: 100%;
        border-collapse: collapse;
    }

    .left-side {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .left-side input {
        padding: 10px;
        border-radius: 20px;
        border: 1px solid #ccc;
        width: 280px;
    }
    .left-side input:focus {
        outline: none;
        border-color: #81C5B1;
    }
    .left-side button {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #81C5B1;
        color: #fff;
        cursor: pointer;
    }
    .right-side {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .right-side button {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #81C5B1;
        color: #fff;
        cursor: pointer;
    }

    .table-container {
        background: #fff;
        font-size: 13px;
        padding: 40px;
        height: 85%;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: auto;
    }

    .table-emp th, .table-emp td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;  
    }
    .table-emp th {
        background-color: darkblue;
        color: white;
    }
    .table-emp tr:hover {
        background-color: #f5f5f5;
    }
    .peform {
        border:2px solid green;
        color: green;
        background: transparent;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
        transition: background-color 0.3s ease;
    }
    .peform:hover {
        background-color: green;
        color: white;
        transition: background-color 0.3s ease;
    }
    .peform:active {
        background-color: #81C5B1;
        color: white;
    }

    .option {
        border:2px solid red;
        color: red;
        background: transparent;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
    }
    .option:hover {
        background-color: red;
        color: white;
        transition: background-color 0.3s ease;
    }
    .option:active {
        background-color: #81C5B1;
        color: white;
    }
    
    .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }
    .success {
        background-color: #d4edda;
        color: #155724;
    }
    .error {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Print/Export Button Styles */
    .print-dropdown {
        position: relative;
        display: inline-block;
    }

    .print-btn {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .print-btn:hover {
        background-color: #45a049;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border-radius: 5px;
        overflow: hidden;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        cursor: pointer;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .print-dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-arrow {
        border: solid white;
        border-width: 0 2px 2px 0;
        display: inline-block;
        padding: 3px;
        transform: rotate(45deg);
        margin-left: 5px;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        .table-container {
            box-shadow: none;
            height: auto;
        }
        .search {
            display: none;
        }
        .main-content {
            margin: 0;
        }
    }
</style>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>

        <div class="dashboard-row">
            <div class="dashboard-column">
                <div class="table-emp">
                    <div class="search">
                        <div class="left-side">
                            <h2>Leave Requests</h2>
                        </div>
                        <div class="right-side left-side">
                            <input type="text" placeholder="Search Employee" class="search-input">
                            <button class="search-button">Search</button>
                            
                            <!-- Print/Export Dropdown -->
                            <div class="print-dropdown">
                                <button class="print-btn">
                                    📄 Export
                                    <span class="dropdown-arrow"></span>
                                </button>
                                <div class="dropdown-content">
                                    <a onclick="printTable()">🖨️ Print</a>
                                    <a onclick="exportToPDF()">📑 Export to PDF</a>
                                    <a onclick="exportToExcel()">📊 Export to Excel</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="message success no-print"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="message error no-print"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        
                        <table id="leaveRequestsTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th class="no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($leaveRequests)): ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center;">No pending leave requests</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($leaveRequests as $request): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($request['employee_id']); ?></td>
                                            <td><?php echo htmlspecialchars($request['employee_name']); ?></td>
                                            <td><?php echo htmlspecialchars($request['department']); ?></td>
                                            <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
                                            <td><?php echo htmlspecialchars($request['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['end_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                            <td><?php echo htmlspecialchars($request['status']); ?></td>
                                            <td class="no-print">
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="action" value="accept">
                                                    <button type="submit" class="peform">Accept</button>
                                                </form>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="action" value="deny">
                                                    <button class="option">Deny</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to print the table
        function printTable() {
            window.print();
        }

        // Function to export table to PDF
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Add title
            doc.setFontSize(18);
            doc.text('Leave Requests Report', 14, 22);
            
            // Add date
            doc.setFontSize(11);
            doc.text('Generated on: ' + new Date().toLocaleDateString(), 14, 30);
            
            // Get table data
            const table = document.getElementById('leaveRequestsTable');
            const headers = [];
            const data = [];
            
            // Extract headers (excluding Action column)
            const headerCells = table.querySelectorAll('thead th');
            headerCells.forEach((cell, index) => {
                if (!cell.classList.contains('no-print')) {
                    headers.push(cell.textContent.trim());
                }
            });
            
            // Extract data (excluding Action column)
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const rowData = [];
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (!cell.classList.contains('no-print')) {
                        rowData.push(cell.textContent.trim());
                    }
                });
                if (rowData.length > 0) {
                    data.push(rowData);
                }
            });
            
            // Generate PDF table
            doc.autoTable({
                head: [headers],
                body: data,
                startY: 40,
                styles: {
                    fontSize: 9,
                    cellPadding: 2,
                },
                headStyles: {
                    fillColor: [72, 61, 139],
                    textColor: 255,
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240],
                },
            });
            
            // Save the PDF
            doc.save('leave_requests_' + new Date().toISOString().split('T')[0] + '.pdf');
        }

        // Function to export table to Excel
        function exportToExcel() {
            const table = document.getElementById('leaveRequestsTable');
            const ws = XLSX.utils.table_to_sheet(table);
            
            // Remove Action column from the worksheet
            const range = XLSX.utils.decode_range(ws['!ref']);
            const actionColumnIndex = range.e.c; // Last column index
            
            // Delete the Action column
            for (let row = range.s.r; row <= range.e.r; row++) {
                const cellAddress = XLSX.utils.encode_cell({r: row, c: actionColumnIndex});
                if (ws[cellAddress]) {
                    delete ws[cellAddress];
                }
            }
            
            // Update the range
            ws['!ref'] = XLSX.utils.encode_range({
                s: {r: range.s.r, c: range.s.c},
                e: {r: range.e.r, c: range.e.c - 1}
            });
            
            // Create workbook and add worksheet
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Leave Requests');
            
            // Save the file
            XLSX.writeFile(wb, 'leave_requests_' + new Date().toISOString().split('T')[0] + '.xlsx');
        }

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchButton = document.querySelector('.search-button');
            const table = document.getElementById('leaveRequestsTable');
            
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            searchButton.addEventListener('click', performSearch);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
</body>
</html>