<?php
// Start session and include database connection
session_start();
require_once '../config.php';

// Set page name
$page_name = 'Payroll Management';
include 'main/slidebar.php';

$select = "SELECT *, users.id as users_id_num FROM users LEFT JOIN payroll ON users.id = payroll.user_id AND payroll.month = MONTH(CURDATE()) AND payroll.year = YEAR(CURDATE())";
$result = mysqli_query($conn, $select);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - Payroll</title>
    <link rel="stylesheet" href="style/style.css"> 
    <link rel="stylesheet" href="style/payroll.css">
    <style>
        .pay-check-btn {
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
            transition: all 0.3s ease;
        }
        
        .pay-check-btn:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }
        
        .unpay-check-btn {
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
            transition: all 0.3s ease;
        }
        
        .unpay-check-btn:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }
        
        .paid-status {
            background-color: #d4edda;
            color: #155724;
        }
        
        .unpaid-status {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .employee-row {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .employee-row:hover {
            background-color: #f8f9fa;
        }
        
        .employee-row.selected {
            background-color: #e3f2fd;
        }
        
        .payroll-history {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .payroll-history h3 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .payroll-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        
        .payroll-item.paid {
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        
        .payroll-item.unpaid {
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .month-year {
            font-weight: bold;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-badge.paid {
            background-color: #28a745;
            color: white;
        }
        
        .status-badge.unpaid {
            background-color: #dc3545;
            color: white;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .current-month-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="current-month-header">
            <h2>Payroll Management - <?php echo date('F Y'); ?></h2>
        </div>

        <div class="dashboard-row" style="height: 80vh;">
            <div class="dashboard-column">
                <div class="search">
                    <div class="left-side">
                        <input type="text" placeholder="Search Employee" class="search-input" id="employeeSearch">
                        <button class="search-button">Search</button>
                    </div>
                </div>
                <div class="table-container" >
                    <table class="table-emp">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Telephone</th>
                                <th>Current Month (<?php echo date('F'); ?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_array($result)){ ?>
                                <tr class="employee-row">
                                    <td><?php echo str_pad($user['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['department']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td> 
                                        <?php 
                                        $month = $user['month'];
                                        $year = $user['year'];
                                        if($user['is_paid'] != 1){ ?>

                                        <a href="update-payroll.php?id=<?php echo $user['users_id_num']; ?>&status=pay&month=<?php echo $month; ?>&year=<?php echo $year; ?>"><button class="unpay-check-btn">  Mark as Unpaid </button> </a>

                                      <?php }else{ ?>
                                       <a href="update-payroll.php?id=<?php echo $user['users_id_num']; ?>&status=unpay"><button class="pay-check-btn" >  Mark as Paid </button> </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeRows = document.querySelectorAll('.employee-row');
            const payrollHistory = document.getElementById('payrollHistory');
            const selectedEmployeeName = document.getElementById('selectedEmployeeName');
            const payrollContent = document.getElementById('payrollContent');
            let currentSelectedRow = null;

           const searchInput = document.getElementById('employeeSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    employeeRows.forEach(row => {
                        const name = row.cells[1].textContent.toLowerCase();
                        const id = row.cells[0].textContent.toLowerCase();
                        const department = row.cells[2].textContent.toLowerCase();
                        const email = row.cells[3].textContent.toLowerCase();
                        
                        if (name.includes(searchTerm) || 
                            id.includes(searchTerm) || 
                            department.includes(searchTerm) || 
                            email.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });

   </script>
   <script>
    
        // Add CSS for modal and payment history
        const additionalCSS = `
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                animation: fadeIn 0.3s ease;
            }

            .modal-content {
                background-color: #fefefe;
                margin: 5% auto;
                padding: 0;
                border-radius: 10px;
                width: 80%;
                max-width: 800px;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                animation: slideIn 0.3s ease;
            }

            .modal-header {
                background: linear-gradient(135deg, #007bff, #0056b3);
                color: white;
                padding: 20px;
                border-radius: 10px 10px 0 0;
                position: relative;
            }

            .modal-header h2 {
                margin: 0;
                font-size: 24px;
            }

            .modal-header .employee-info {
                margin-top: 10px;
                font-size: 14px;
                opacity: 0.9;
            }

            .close {
                position: absolute;
                right: 20px;
                top: 20px;
                color: white;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .close:hover {
                transform: scale(1.1);
                opacity: 0.8;
            }

            .modal-body {
                padding: 30px;
            }

            .payment-summary {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }

            .summary-card {
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            .summary-card.total {
                background: linear-gradient(135deg, #6c757d, #495057);
                color: white;
            }

            .summary-card.paid {
                background: linear-gradient(135deg, #28a745, #1e7e34);
                color: white;
            }

            .summary-card.unpaid {
                background: linear-gradient(135deg, #dc3545, #c82333);
                color: white;
            }

            .summary-card h3 {
                margin: 0 0 10px 0;
                font-size: 32px;
                font-weight: bold;
            }

            .summary-card p {
                margin: 0;
                font-size: 14px;
                opacity: 0.9;
            }

            .payment-history-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 15px;
                margin-top: 20px;
            }

            .payment-record {
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .payment-record:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }

            .payment-record.paid {
                background-color: #d4edda;
                border-left-color: #28a745;
            }

            .payment-record.unpaid {
                background-color: #f8d7da;
                border-left-color: #dc3545;
            }

            .payment-record.skipped {
                background-color: #fff3cd;
                border-left-color: #ffc107;
            }

            .payment-date {
                font-weight: bold;
                font-size: 16px;
                margin-bottom: 5px;
            }

            .payment-status {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .payment-status.paid {
                background-color: #28a745;
                color: white;
            }

            .payment-status.unpaid {
                background-color: #dc3545;
                color: white;
            }

            .payment-status.skipped {
                background-color: #ffc107;
                color: #212529;
            }

            .loading-spinner {
                text-align: center;
                padding: 40px;
            }

            .spinner {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #007bff;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 0 auto;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slideIn {
                from { 
                    opacity: 0;
                    transform: translateY(-50px);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .no-records {
                text-align: center;
                color: #666;
                font-style: italic;
                padding: 40px;
            }
        `;

        // Add the CSS to the page
        const style = document.createElement('style');
        style.textContent = additionalCSS;
        document.head.appendChild(style);

        document.addEventListener('DOMContentLoaded', function() {
            const employeeRows = document.querySelectorAll('.employee-row');
            const searchInput = document.getElementById('employeeSearch');

            // Create modal HTML
            const modalHTML = `
                <div id="paymentModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="close">&times;</span>
                            <h2>Payment History</h2>
                            <div class="employee-info" id="employeeInfo"></div>
                        </div>
                        <div class="modal-body">
                            <div class="payment-summary" id="paymentSummary"></div>
                            <div id="paymentHistoryContent">
                                <div class="loading-spinner">
                                    <div class="spinner"></div>
                                    <p>Loading payment history...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Add modal to page
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const modal = document.getElementById('paymentModal');
            const closeBtn = document.querySelector('.close');
            const employeeInfo = document.getElementById('employeeInfo');
            const paymentSummary = document.getElementById('paymentSummary');
            const paymentHistoryContent = document.getElementById('paymentHistoryContent');

            // Close modal events
            closeBtn.addEventListener('click', closeModal);
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.style.display === 'block') {
                    closeModal();
                }
            });

            function closeModal() {
                modal.style.display = 'none';
            }

            // Add click event to each employee row
            employeeRows.forEach(row => {
                row.addEventListener('click', function() {
                    const employeeId = this.cells[0].textContent.trim();
                    const employeeName = this.cells[1].textContent.trim();
                    const department = this.cells[2].textContent.trim();
                    const email = this.cells[3].textContent.trim();

                    // Update employee info in modal
                    employeeInfo.innerHTML = `
                        <strong>${employeeName}</strong> (ID: ${employeeId})<br>
                        ${department} • ${email}
                    `;

                    // Show modal
                    modal.style.display = 'block';

                    // Reset content
                    paymentHistoryContent.innerHTML = `
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                            <p>Loading payment history...</p>
                        </div>
                    `;

                    // Fetch payment history
                    fetchPaymentHistory(employeeId.replace(/^0+/, '')); // Remove leading zeros
                });
            });

            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    employeeRows.forEach(row => {
                        const name = row.cells[1].textContent.toLowerCase();
                        const id = row.cells[0].textContent.toLowerCase();
                        const department = row.cells[2].textContent.toLowerCase();
                        const email = row.cells[3].textContent.toLowerCase();
                        
                        if (name.includes(searchTerm) || 
                            id.includes(searchTerm) || 
                            department.includes(searchTerm) || 
                            email.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Function to fetch payment history via AJAX
            function fetchPaymentHistory(userId) {
                fetch('get_payment_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPaymentHistory(data.payments, data.summary);
                    } else {
                        paymentHistoryContent.innerHTML = `
                            <div class="no-records">
                                <p>Error loading payment history: ${data.message || 'Unknown error'}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    paymentHistoryContent.innerHTML = `
                        <div class="no-records">
                            <p>Error loading payment history. Please try again.</p>
                        </div>
                    `;
                });
            }

            // Function to display payment history
            function displayPaymentHistory(payments, summary) {
                

                if (payments.length === 0) {
                    paymentHistoryContent.innerHTML = `
                        <div class="no-records">
                            <p>No paid records found for this employee.</p>
                        </div>
                    `;
                    return;
                }

                // Generate payment history grid (only paid records)
                const historyHTML = payments.map(payment => {
                    return `
                        <div class="payment-record paid">
                            <div class="payment-date">${payment.month_name} ${payment.year}</div>
                            <div class="payment-status paid">Paid</div>
                            ${payment.payment_date ? `<div style="font-size: 12px; color: #666; margin-top: 5px;">Date: ${payment.payment_date}</div>` : ''}
                        </div>
                    `;
                }).join('');

                paymentHistoryContent.innerHTML = `
                    <h3 style="margin-bottom: 20px; color: #333;">Paid Months</h3>
                    <div class="payment-history-grid">
                        ${historyHTML}
                    </div>
                `;
            }
        });

        // Additional utility functions
        function formatMonth(monthNumber) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return months[monthNumber - 1] || 'Unknown';
        }
    
   </script>
   <script src="js/payroll.js"></script>
</body>
</html>