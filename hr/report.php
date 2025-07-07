<?php 
    $page_name = 'Report';
    
    // Database connection
    require_once '../config.php';
    
    // Handle date filtering
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    
    // Base query for users table only
    $query = "SELECT 
                id as employee_id, 
                name, 
                department, 
                username as added_by,
                email, 
                phone as tel, 
                salary, 
                registration_date as date
              FROM users
              WHERE user_type = 'employee'";
    
    // Add date filter if provided
    if (!empty($start_date) && !empty($end_date)) {
        $query .= " AND DATE(registration_date) BETWEEN '$start_date' AND '$end_date'";
    }
    
    // Execute query
    $result = mysqli_query($conn, $query);
    
    // Check for errors
    if (!$result) {
        die("Database query failed: " . mysqli_error($conn));
    }

    $i = 1;

    
    include 'main/slidebar.php'; 
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
    .active-report {
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
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-bottom: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .search input,
    .search button {
        padding: 10px;
        border-radius: 5px;
        border: none;
        font-size: 16px;
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

    /* Date Filter Styles */
    .date-filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    .date-filter-form input[type="date"] {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .date-filter-form button {
        padding: 8px 16px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .date-filter-form button:hover {
        background-color: #0056b3;
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

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .report-title {
        color: #333;
        margin: 0;
        font-size: 28px;
    }

    .filter-export-section {
        display: flex;
        align-items: center;
        gap: 15px;
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
        body {
            margin: 0;
            padding: 0;
        }
    }
</style>
<body>
    
    <div class="main-content">

        <div class="dashboard-row">
            <div class="dashboard-column">
                <div class="table-emp">
                    
                    <div class="search">
                        <div class="report-header">
                            <h1 class="report-title">Employee Report</h1>
                            <div class="filter-export-section">
                                <form method="GET" action="" class="date-filter-form">
                                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" placeholder="Start Date">
                                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" placeholder="End Date">
                                    <button type="submit">Filter</button>
                                </form>
                                
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
                    </div>

                    <div class="table-container">
                        <?php if (!empty($start_date) && !empty($end_date)): ?>
                            <div class="filter-info no-print" style="margin-bottom: 15px; padding: 10px; background-color: #e3f2fd; border-radius: 5px; font-size: 14px;">
                                <strong>Filter Applied:</strong> Showing employees registered between <?php echo date('F j, Y', strtotime($start_date)); ?> and <?php echo date('F j, Y', strtotime($end_date)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <table id="employeeReportTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Added By</th>
                                    <th>Email</th>
                                    <th>Tel</th>
                                    <th>Salary</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $i . "</td>"; $i++;
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['added_by']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tel']) . "</td>";
                                        echo "<td>Frw " . number_format($row['salary'], 2) . "</td>";
                                        echo "<td>" . date('m-d-Y', strtotime($row['date'])) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' style='text-align: center; color: #666; font-style: italic;'>No employees found</td></tr>";
                                }
                                
                                // Close database connection
                                mysqli_close($conn);
                                ?>
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
            doc.setFontSize(20);
            doc.text('Employee Report', 14, 22);
            
            // Add date filter info if applied
            const filterInfo = document.querySelector('.filter-info');
            if (filterInfo) {
                doc.setFontSize(12);
                doc.text(filterInfo.textContent.replace('Filter Applied: ', ''), 14, 32);
            }
            
            // Add generation date
            doc.setFontSize(11);
            doc.text('Generated on: ' + new Date().toLocaleDateString(), 14, filterInfo ? 42 : 32);
            
            // Get table data
            const table = document.getElementById('employeeReportTable');
            const headers = [];
            const data = [];
            
            // Extract headers
            const headerCells = table.querySelectorAll('thead th');
            headerCells.forEach(cell => {
                headers.push(cell.textContent.trim());
            });
            
            // Extract data
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const rowData = [];
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    rowData.push(cell.textContent.trim());
                });
                if (rowData.length > 0 && !rowData[0].includes('No employees found')) {
                    data.push(rowData);
                }
            });
            
            // Generate PDF table
            doc.autoTable({
                head: [headers],
                body: data,
                startY: filterInfo ? 50 : 40,
                styles: {
                    fontSize: 9,
                    cellPadding: 3,
                },
                headStyles: {
                    fillColor: [72, 61, 139],
                    textColor: 255,
                    fontStyle: 'bold',
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240],
                },
                columnStyles: {
                    0: { cellWidth: 15 }, // No column
                    1: { cellWidth: 25 }, // Name
                    2: { cellWidth: 25 }, // Department
                    3: { cellWidth: 25 }, // Added By
                    4: { cellWidth: 35 }, // Email
                    5: { cellWidth: 25 }, // Tel
                    6: { cellWidth: 25 }, // Salary
                    7: { cellWidth: 25 }, // Date
                },
            });
            
            // Save the PDF
            const filename = 'employee_report_' + new Date().toISOString().split('T')[0] + '.pdf';
            doc.save(filename);
        }

        // Function to export table to Excel
        function exportToExcel() {
            const table = document.getElementById('employeeReportTable');
            const ws = XLSX.utils.table_to_sheet(table);
            
            // Create workbook and add worksheet
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Employee Report');
            
            // Add metadata
            const filterInfo = document.querySelector('.filter-info');
            if (filterInfo) {
                // Add filter info as a separate sheet
                const filterData = [
                    ['Report Information'],
                    ['Filter Applied', filterInfo.textContent.replace('Filter Applied: ', '')],
                    ['Generated On', new Date().toLocaleDateString()],
                    ['Generated At', new Date().toLocaleTimeString()]
                ];
                const filterWS = XLSX.utils.aoa_to_sheet(filterData);
                XLSX.utils.book_append_sheet(wb, filterWS, 'Report Info');
            }
            
            // Save the file
            const filename = 'employee_report_' + new Date().toISOString().split('T')[0] + '.xlsx';
            XLSX.writeFile(wb, filename);
        }

        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('#employeeReportTable tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f8ff';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Add click-to-copy functionality for email addresses
            const emailCells = document.querySelectorAll('#employeeReportTable tbody tr td:nth-child(5)');
            emailCells.forEach(cell => {
                cell.style.cursor = 'pointer';
                cell.title = 'Click to copy email address';
                cell.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.textContent.trim()).then(() => {
                        // Show temporary feedback
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        setTimeout(() => {
                            this.textContent = originalText;
                        }, 1000);
                    });
                });
            });
        });
    </script>
</body>
</html>