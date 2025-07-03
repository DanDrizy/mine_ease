<?php
// Start session
session_start();
include_once '../config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_payroll'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $providence = mysqli_real_escape_string($conn, $_POST['providence']);
    $tax = mysqli_real_escape_string($conn, $_POST['tax']);
    $loan = mysqli_real_escape_string($conn, $_POST['loan']);
    
    // Update payroll data in database
    $query = "UPDATE users SET 
              salary = '$salary',
              providence = '$providence',
              tax = '$tax',
              loan = '$loan',
              updated_at = NOW()
              WHERE id = '$employee_id'";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Payroll updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating payroll: " . mysqli_error($conn);
    }
    
    // Redirect to avoid form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch all employees
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$employees = [];
while ($row = mysqli_fetch_assoc($result)) {
    $employees[] = $row;
}

$i=1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/payroll.css">
    <link rel="stylesheet" href=" https///cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<style></style>
<body>

    <?php include 'main/slidebar.php'; ?>
    <div class="main-content">
        
        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="action-feedback" id="feedback-message" style="display: block;">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-feedback" id="error-message" style="display: block;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="payroll-stats-grid">
            <div class="payroll-table">
                <div class="header">
                    <h2>Payroll</h2>
                    <div class="searchbar">
                        <input type="search" id="employee-search" placeholder="Search...">
                        <div class="search-icon"> <i class="fa fa-search" > </i> </div>
                    </div>
                </div>
                <div class="table-container">
                    <table id="employee-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>email</th>
                                <th>Site</th>
                                <th>Department</th>
                                <th>Registered Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <tr data-id="<?php echo $employee['id']; ?>" 
                                    data-name="<?php echo htmlspecialchars($employee['name']); ?>" 
                                    data-department="<?php echo htmlspecialchars($employee['department']); ?>" 
                                    data-site="<?php echo htmlspecialchars($employee['site']); ?>" 
                                    data-joindate="<?php echo date('d F Y', strtotime($employee['registration_date'])); ?>"
                                    data-salary="<?php echo $employee['salary'] ?? 0; ?>" 
                                    data-providence="<?php echo $employee['providence'] ?? 0; ?>" 
                                    data-tax="<?php echo $employee['tax'] ?? 0; ?>" 
                                    data-loan="<?php echo $employee['loan'] ?? 0; ?>">
                                    <td><?php echo $i; $i++; ?></td>
                                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['user_type']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['site']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['department']); ?></td>
                                    <td><?php echo date('d F Y', strtotime($employee['registration_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="payroll-bottom-grid" id="details-section">
            <div class="container">
                <form method="POST" action="" id="payroll-form">
                    <input type="hidden" name="employee_id" id="form-employee-id">
                    <input type="hidden" name="save_payroll" value="1">
                    
                    <div class="details-section">
                        <div class="details-left">
                            <div class="detail-row">
                                <span class="detail-label">Date of join:</span> <span id="join-date"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Pay Period:</span> <span id="pay-period"><?php echo date('F Y'); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Worked days:</span> <span id="worked-days">30</span>
                            </div>
                        </div>
                        <div class="details-right">
                            <div class="detail-row">
                                <span class="detail-label">Name:</span> <span id="employee-name"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Department:</span> <span id="employee-dept"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Site:</span> <span id="employee-site"></span>
                            </div>
                        </div>
                    </div>
                    
                    <table class="pay-table">
                        <tr class="pay-table-header">
                            <th style="width: 70%">Item</th>
                            <th style="width: 30%">Amount (RWF)</th>
                        </tr>
                        <tr class="pay-row">
                            <td class="pay-item"><a href="#">Salary</a></td>
                            <td class="pay-amount editable" id="salary"></td>
                        </tr>
                        <tr class="pay-row">
                            <td class="pay-item"><a href="#">Providence Funds</a></td>
                            <td class="pay-amount negative editable" id="providence"></td>
                        </tr>
                        <tr class="pay-row">
                            <td class="pay-item"><a href="#">Professional Tax</a></td>
                            <td class="pay-amount negative editable" id="tax"></td>
                        </tr>
                        <tr class="pay-row">
                            <td class="pay-item"><a href="#">Loan</a></td>
                            <td class="pay-amount negative editable" id="loan"></td>
                        </tr>
                        <tr class="pay-table-footer">
                            <td colspan="1" style="text-align: left;">Total Amount:</td>
                            <td id="total-amount"></td>
                        </tr>
                    </table>
                    <button type="submit" class="save-btn" id="save-button">Save Changes</button>
                </form>
            </div>
        </div>
        
        <!-- Action feedback message -->
        <div class="action-feedback" id="feedback-message">Changes saved successfully!</div>
        <div class="error-feedback" id="error-message">Error saving changes!</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeTable = document.getElementById('employee-table');
            const detailsSection = document.getElementById('details-section');
            const saveButton = document.getElementById('save-button');
            const searchInput = document.getElementById('employee-search');
            const searchIcon = document.querySelector('.search-icon');
            const feedbackMessage = document.getElementById('feedback-message');
            const errorMessage = document.getElementById('error-message');
            const payrollForm = document.getElementById('payroll-form');
            const formEmployeeId = document.getElementById('form-employee-id');
            let selectedEmployeeId = null;
            
            // Format number with commas
            function formatNumber(num) {
                return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
            
            // Calculate total amount
            function calculateTotal() {
                const salary = parseFloat(document.getElementById('salary').getAttribute('data-value') || 0);
                const providence = parseFloat(document.getElementById('providence').getAttribute('data-value') || 0);
                const tax = parseFloat(document.getElementById('tax').getAttribute('data-value') || 0);
                const loan = parseFloat(document.getElementById('loan').getAttribute('data-value') || 0);
                
                const total = salary - providence - tax - loan;
                document.getElementById('total-amount').textContent = formatNumber(total);
                return total;
            }
            
            // Show feedback message
            function showFeedback(message, isError = false) {
                if (isError) {
                    errorMessage.textContent = message;
                    errorMessage.style.display = 'block';
                    setTimeout(function() {
                        errorMessage.style.display = 'none';
                    }, 3000);
                } else {
                    feedbackMessage.textContent = message;
                    feedbackMessage.style.display = 'block';
                    setTimeout(function() {
                        feedbackMessage.style.display = 'none';
                    }, 3000);
                }
            }
            
            // Update employee details
            function updateEmployeeDetails(row) {
                // Get data from the row
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');
                const department = row.getAttribute('data-department');
                const site = row.getAttribute('data-site');
                const joinDate = row.getAttribute('data-joindate');
                const salary = parseFloat(row.getAttribute('data-salary'));
                const providence = parseFloat(row.getAttribute('data-providence'));
                const tax = parseFloat(row.getAttribute('data-tax'));
                const loan = parseFloat(row.getAttribute('data-loan'));
                
                // Update the details section
                document.getElementById('employee-name').textContent = name;
                document.getElementById('employee-dept').textContent = department;
                document.getElementById('employee-site').textContent = site;
                document.getElementById('join-date').textContent = joinDate;
                document.getElementById('pay-period').textContent = '<?php echo date('F Y'); ?>';
                document.getElementById('worked-days').textContent = '30';
                
                // Update payment values and store raw values as data attributes
                const salaryElement = document.getElementById('salary');
                salaryElement.textContent = formatNumber(salary);
                salaryElement.setAttribute('data-value', salary);
                
                const providenceElement = document.getElementById('providence');
                providenceElement.textContent = '-' + formatNumber(providence);
                providenceElement.setAttribute('data-value', providence);
                
                const taxElement = document.getElementById('tax');
                taxElement.textContent = '-' + formatNumber(tax);
                taxElement.setAttribute('data-value', tax);
                
                const loanElement = document.getElementById('loan');
                loanElement.textContent = '-' + formatNumber(loan);
                loanElement.setAttribute('data-value', loan);
                
                // Calculate and update the total
                calculateTotal();
                
                // Set the employee ID in the form
                formEmployeeId.value = id;
                
                // Show the details section
                detailsSection.style.display = 'block';
                
                // Update selected employee ID
                selectedEmployeeId = id;
            }
            
            // Make a field editable
            function makeEditable(element) {
                // Don't create a new input if already editing
                if (element.querySelector('input')) return;
                
                const currentValue = parseFloat(element.getAttribute('data-value'));
                const input = document.createElement('input');
                input.type = 'number';
                input.className = 'amount-edit';
                input.value = currentValue;
                input.min = 0;
                
                // For deductions, we'll handle the negative sign during display
                if (element.id !== 'salary') {
                    input.step = 100;
                } else {
                    input.step = 1000;
                }
                
                // Replace the text with the input
                element.textContent = '';
                element.appendChild(input);
                input.focus();
                
                // Handle input blur event
                input.addEventListener('blur', function() {
                    const newValue = parseFloat(input.value);
                    if (isNaN(newValue)) {
                        // If input is invalid, revert to old value
                        element.setAttribute('data-value', currentValue);
                        if (element.id === 'salary') {
                            element.textContent = formatNumber(currentValue);
                        } else {
                            element.textContent = '-' + formatNumber(currentValue);
                        }
                    } else {
                        // Update with new value
                        element.setAttribute('data-value', newValue);
                        if (element.id === 'salary') {
                            element.textContent = formatNumber(newValue);
                        } else {
                            element.textContent = '-' + formatNumber(newValue);
                        }
                    }
                    
                    // Recalculate total
                    calculateTotal();
                });
                
                // Handle Enter key
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        input.blur();
                    }
                });
            }
            
            // Handle row click event
            employeeTable.addEventListener('click', function(e) {
                const row = e.target.closest('tr');
                if (row && row.parentNode.tagName !== 'THEAD') {
                    // Remove selected class from all rows
                    const rows = employeeTable.querySelectorAll('tbody tr');
                    rows.forEach(r => r.classList.remove('selected-row'));
                    
                    // Add selected class to the clicked row
                    row.classList.add('selected-row');
                    
                    // Update the details section with the employee data
                    updateEmployeeDetails(row);
                }
            });
            
            // Make amount fields editable on click
            document.querySelectorAll('.editable').forEach(function(element) {
                element.addEventListener('click', function() {
                    makeEditable(this);
                });
            });
            
            // Search functionality
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = employeeTable.querySelectorAll('tbody tr');
                
                rows.forEach(function(row) {
                    const name = row.getAttribute('data-name').toLowerCase();
                    const department = row.getAttribute('data-department').toLowerCase();
                    const site = row.getAttribute('data-site').toLowerCase();
                    
                    if (name.includes(searchTerm) || department.includes(searchTerm) || site.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            // Search on input change
            searchInput.addEventListener('input', performSearch);
            
            // Search when clicking the search icon
            searchIcon.addEventListener('click', performSearch);
            
            // Search when pressing Enter in the search input
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
            
            // Auto-select first employee if none selected
            if (document.querySelector('tbody tr')) {
                document.querySelector('tbody tr').click();
            }
        });
    </script>
</body>
</html>