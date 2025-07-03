<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="style/style.css"> 
    <style>
        .active-attend {
            background-color: #81C5B1;
            transition: background-color 0.3s ease;
            color: #1E1E1E;
        }

        .search {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px;
            border-radius: 20px;
            height: 5rem;
        }
        
        .search input {
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ddd;
        }
        
        .search input:focus {
            outline: none;
            border-color: #81C5B1;
        }

        .search button {
            padding: 10px 20px;
            background-color: #81C5B1;
            color: #fff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
        
        .table-container {
            background: #fff;
            padding: 20px;
            height: 100rem;
            border-radius: 20px;
            overflow: auto;
        }
        
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        
        .table-container th, .table-container td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .table-container tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }
        
        .table-container th {
            background-color: #81C5B1;
            font-weight: bold;
            color: #fff;
        }

        .user-details {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-radius: 20px;
            height: 8rem;
            font-size: 12px;
        }

        .info-box {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            width: 80%;
        }

        .image {
            height: 100%;
            width: 30%;
        }
        
        .image img {
            width: 80%;
            height: 80%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .user-form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: start;
            gap: 10px;
            width: 70%;
        }

        .month {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 30%;
        }
        
        .month-header {
            padding: 10px;
            background: #81C5B1;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .month-header h2 {
            margin: 0;
            font-size: 16px;
        }
        
        .year-selector {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
            font-size: 14px;
        }
        
        .month ul {
            list-style: none;
            padding: 10px;
            margin: 0;
            background: #81C5B1;
            width: 100%;
            overflow: auto;
            text-align: center;
        }

        .month li {
            text-align: left;
            border: 1px solid #ddd;
            display: inline-flex;
            color: #fff;
            padding: 2px;
            margin: 2px;
        }
        
        .paid-month {
            background-color: #1E1E1E;
            color: #fff !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php
    $page_name = 'Attendance Tracking';
    include 'main/slidebar.php'; 
    ?>

    <div class="main-content">
        <?php include 'main/header.php'; ?>

        <div class="dashboard-row" style="height: 60vh;">
            <div class="dashboard-column">
                <div class="search">
                    <div class="left-side">
                        <input type="text" placeholder="Search Employee" class="search-input" id="employeeSearch">
                        <button class="search-button">Search</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table-emp">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Added By</th>
                                <th>Email</th>
                                <th>Telephone</th>
                                <th>Monthly</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-paid-months="1,2,5,8,11" data-paid-years="{'2023': '1,5,11', '2024': '2,8', '2025': '1,2'}">
                                <td>001</td>
                                <td>John Doe</td>
                                <td>HR</td>
                                <td>Honore</td>
                                <td>john@gmail.com</td>
                                <td>0790365857</td>
                                <td>Worked</td>
                                <td>...</td>
                            </tr>
                            <tr data-paid-months="1,3,4,7,9" data-paid-years="{'2023': '1,3', '2024': '4,7,9', '2025': ''}">
                                <td>002</td>
                                <td>Jane Smith</td>
                                <td>IT</td>
                                <td>Honore</td>
                                <td>jane@gmail.com</td>
                                <td>0790365858</td>
                                <td>Worked</td>
                                <td>...</td>
                            </tr>
                            <tr data-paid-months="2,4,6,8,10,12" data-paid-years="{'2023': '2,4,6', '2024': '8,10,12', '2025': ''}">
                                <td>003</td>
                                <td>Robert Johnson</td>
                                <td>Finance</td>
                                <td>Honore</td>
                                <td>robert@gmail.com</td>
                                <td>0790365859</td>
                                <td>Worked</td>
                                <td>...</td>
                            </tr>
                            <tr data-paid-months="1,3,5,7,9,11" data-paid-years="{'2023': '1,3,5', '2024': '7,9,11', '2025': ''}">
                                <td>004</td>
                                <td>Sarah Williams</td>
                                <td>Marketing</td>
                                <td>Honore</td>
                                <td>sarah@gmail.com</td>
                                <td>0790365860</td>
                                <td>Worked</td>
                                <td>...</td>
                            </tr>
                            <tr data-paid-months="2,3,6,9,12" data-paid-years="{'2023': '2,3', '2024': '6,9', '2025': '12'}">
                                <td>005</td>
                                <td>Michael Brown</td>
                                <td>HR</td>
                                <td>Honore</td>
                                <td>michael@gmail.com</td>
                                <td>0790365861</td>
                                <td>Worked</td>
                                <td>...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="dashboard-row" style="height: 50vh;">
            <div class="dashboard-column">
                <div class="user-details">
                    <div class="image">
                        <img src="../img/user.svg" alt="Employee Photo">
                    </div>
                    <div class="info-box">
                        <div class="user-form">
                            <h2>Name: Select an employee</h2>
                            <h2>Title: --</h2>
                            <h2>Department: --</h2>
                            <h2>Tel: --</h2>
                        </div>
                        <div class="month">
                            <div class="month-header">
                                <h2>Paid Months</h2>
                                <select id="yearSelector" class="year-selector">
                                    <option value="2023">2023</option>
                                    <option value="2024" selected>2024</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>
                            <ul id="months-list">
                                <li data-month="1">January</li>
                                <li data-month="2">February</li>
                                <li data-month="3">March</li>
                                <li data-month="4">April</li>
                                <li data-month="5">May</li>
                                <li data-month="6">June</li>
                                <li data-month="7">July</li>
                                <li data-month="8">August</li>
                                <li data-month="9">September</li>
                                <li data-month="10">October</li>
                                <li data-month="11">November</li>
                                <li data-month="12">December</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all table rows except the header row
            const employeeRows = document.querySelectorAll('.table-emp tbody tr');
            const yearSelector = document.getElementById('yearSelector');
            let currentSelectedRow = null;
            
            // Add click event listener to each row
            employeeRows.forEach(row => {
                row.addEventListener('click', function() {
                    // Remove active class from all rows
                    employeeRows.forEach(r => r.classList.remove('active-attend'));
                    
                    // Add active class to the clicked row
                    this.classList.add('active-attend');
                    currentSelectedRow = this;
                    
                    // Get data from the selected row
                    const id = this.cells[0].textContent;
                    const name = this.cells[1].textContent;
                    const department = this.cells[2].textContent;
                    const email = this.cells[4].textContent;
                    const telephone = this.cells[5].textContent;
                    
                    // Update the user details section
                    document.querySelector('.user-form h2:nth-child(1)').textContent = `Name: ${name}`;
                    document.querySelector('.user-form h2:nth-child(2)').textContent = `Title: ${getDepartmentTitle(department)}`;
                    document.querySelector('.user-form h2:nth-child(3)').textContent = `Department: ${department}`;
                    document.querySelector('.user-form h2:nth-child(4)').textContent = `Tel: ${telephone}`;
                    
                    // Update months based on the currently selected year
                    updatePaidMonthsForSelectedYear();
                });
            });
            
            // Year selector change event
            yearSelector.addEventListener('change', function() {
                if (currentSelectedRow) {
                    updatePaidMonthsForSelectedYear();
                }
            });
            
            // Function to update paid months based on selected year
            function updatePaidMonthsForSelectedYear() {
                if (!currentSelectedRow) return;
                
                const selectedYear = yearSelector.value;
                const paidYearsData = currentSelectedRow.getAttribute('data-paid-years');
                
                if (paidYearsData) {
                    try {
                        const yearsObject = JSON.parse(paidYearsData.replace(/'/g, '"'));
                        const paidMonthsForYear = yearsObject[selectedYear];
                        
                        if (paidMonthsForYear) {
                            highlightPaidMonths(paidMonthsForYear.split(','));
                        } else {
                            resetPaidMonths();
                        }
                    } catch (e) {
                        console.error("Error parsing paid years data:", e);
                        resetPaidMonths();
                    }
                } else {
                    // Fallback to the original data-paid-months attribute if years data is not available
                    const paidMonthsString = currentSelectedRow.getAttribute('data-paid-months');
                    if (paidMonthsString) {
                        highlightPaidMonths(paidMonthsString.split(','));
                    } else {
                        resetPaidMonths();
                    }
                }
            }
            
            // Helper function to get a title based on department
            function getDepartmentTitle(department) {
                const titles = {
                    'HR': 'HR Manager',
                    'IT': 'IT Specialist',
                    'Finance': 'Financial Analyst',
                    'Marketing': 'Marketing Specialist'
                };
                return titles[department] || 'Employee';
            }
            
            // Function to highlight paid months
            function highlightPaidMonths(paidMonths) {
                // Reset all months first
                resetPaidMonths();
                
                // Highlight the specified months
                paidMonths.forEach(month => {
                    if (month && month.trim() !== '') {
                        const monthElement = document.querySelector(`.month ul li[data-month="${month.trim()}"]`);
                        if (monthElement) {
                            monthElement.classList.add('paid-month');
                        }
                    }
                });
            }
            
            // Function to reset all month highlights
            function resetPaidMonths() {
                const months = document.querySelectorAll('.month ul li');
                months.forEach(month => {
                    month.classList.remove('paid-month');
                });
            }
            
            // Add search functionality
            const searchInput = document.getElementById('employeeSearch');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                employeeRows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const id = row.cells[0].textContent.toLowerCase();
                    const department = row.cells[2].textContent.toLowerCase();
                    const email = row.cells[4].textContent.toLowerCase();
                    
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
            
            // Initialize the year selector with current year
            const currentYear = new Date().getFullYear();
            if (document.querySelector(`#yearSelector option[value="${currentYear}"]`)) {
                yearSelector.value = currentYear.toString();
            }
        });
    </script>
</body>
</html>