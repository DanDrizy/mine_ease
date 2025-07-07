<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="../style/main.css">
</head>
<style>
.active_atte {
    color: yellow;
 border-bottom:2px solid yellow;

 }
.stats-grid
{
    background: white;
    width: 100%;
    height: 15rem;
    border-radius: 20px;
    display: flex;
}

.table-content
{
   margin: 20px;
    width: 100%;
}
.table-content table
{
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.table-content table th
{
    color: blue;
}
.table-content table td
{
    color: darkblue;
}

.table-content table th, .table-content table td
{
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}


        
        .stock-status {
            width: 40%;
            height: 22rem;
            font-size: 12px;
            overflow: hidden;
            border-right: 1px solid #eaeaea;
        }
        
        .table-content {
            width: 97%;
            margin: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #f9f9f9;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #eaeaea;
        }
        
        td {
            padding: 10px 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        tr:hover {
            background-color: #f5fff5;
        }
        
        select {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            outline: none;
        }
        
        select:focus {
            border-color: #153D00;
        }
        
        .admin-announce {
            width: 60%;
            height: 22rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #153D00;
        }
        
        .admin-announce h2 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .admin-announce h1 {
            font-size: 10rem;
            margin: 0;
            transition: all 0.3s ease;
        }
        
        tr.total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        .performance-cell {
            position: relative;
        }
        
        .performance-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: rgba(21, 61, 0, 0.1);
            z-index: 0;
            transition: width 0.5s ease-out;
        }
        
        .performance-value {
            position: relative;
            z-index: 1;
        }

        .table-container {
            height: calc(22rem - 50px);
            overflow-y: auto;
        }
        
        /* Hide scrollbar for Chrome, Safari and Opera */
        .table-container::-webkit-scrollbar {
            display: none;
        }
        
        /* Hide scrollbar for IE, Edge and Firefox */
        .table-container {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }


</style>
<body>
    <?php 

    session_start();
    $user_id = $_SESSION['user_id'] ?? 0;
    
    ?>

    <?php
    include '../config.php';
    $page_name = 'Attendance';
    include 'main/slidebar.php';
    ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <!-- <div class="stats-grid">
            <div class="table-content">
                <table>
                    <tr>
                        <th>Co-worker</th>
                        <th>Contact</th>
                        <th>Performance</th>
                    </tr>
                    <?php
                    // Fetch users data
                    $users_query = "SELECT id, name, email FROM users WHERE user_type = 'employee'";
                    $users_result = $conn->query($users_query);
                    
                    if ($users_result->num_rows > 0) {
                        while($user = $users_result->fetch_assoc()) {
                            // Calculate performance (example: based on attendance)
                            $attendance_query = "SELECT COUNT(*) as total, 
                                               SUM(status = 'present') as present 
                                               FROM attendance 
                                               WHERE user_id = ".$user['id'];
                            $attendance_result = $conn->query($attendance_query);
                            $attendance = $attendance_result->fetch_assoc();
                            
                            $performance = 0;
                            if ($attendance['total'] > 0) {
                                $performance = round(($attendance['present'] / $attendance['total']) * 100);
                            }
                            
                            echo "<tr>
                                    <td>".htmlspecialchars($user['name'])."</td>
                                    <td>".htmlspecialchars($user['email'])."</td>
                                    <td>".$performance."%</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No employees found</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div> -->
        
        <div class="bottom-grid">
            <div class="stock-status">
                <div class="table-content">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <select id="timeFrame">
                                        <option value="week">Week</option>
                                        <option value="month">Month</option>
                                        <option value="year">Year</option>
                                    </select>
                                </th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="table-container">
                        <table id="performanceTable">
                            <tbody>
                                <!-- Table content will be generated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="admin-announce">
                <h2 id="periodText">Total Performance Per Week</h2>
                <h1 id="totalPerformance">
                    <?php
                    // Calculate overall performance
                    $overall_query = "SELECT COUNT(*) as total, 
                                     SUM(status = 'present') as present 
                                     FROM attendance";
                    $overall_result = $conn->query($overall_query);
                    $overall = $overall_result->fetch_assoc();
                    
                    $overall_performance = 0;
                    if ($overall['total'] > 0) {
                        $overall_performance = round(($overall['present'] / $overall['total']) * 100);
                    }
                    echo $overall_performance."%";
                    ?>
                </h1>
            </div>
        </div>
    </div>

    <script>
        // Data sets for different time frames
        const performanceData = {
            week: [
                <?php
                // Get weekly data
                $week_query = "SELECT DAYNAME(date) as day, 
                              COUNT(*) as total, 
                              SUM(status = 'present') as present 
                              FROM attendance 
                              WHERE YEARWEEK(date) = YEARWEEK(CURDATE()) 
                              GROUP BY DAYOFWEEK(date)";
                $week_result = $conn->query($week_query);
                
                $week_data = array();
                if ($week_result->num_rows > 0) {
                    while($row = $week_result->fetch_assoc()) {
                        $performance = 0;
                        if ($row['total'] > 0) {
                            $performance = round(($row['present'] / $row['total']) * 100);
                        }
                        $week_data[] = "{ period: '".$row['day']."', performance: ".$performance." }";
                    }
                }
                echo implode(",\n", $week_data);
                ?>
            ],
            month: [
                <?php
                // Get monthly data
                $month_query = "SELECT MONTHNAME(date) as month, 
                               COUNT(*) as total, 
                               SUM(status = 'present') as present 
                               FROM attendance 
                               WHERE YEAR(date) = YEAR(CURDATE()) 
                               GROUP BY MONTH(date)";
                $month_result = $conn->query($month_query);
                
                $month_data = array();
                if ($month_result->num_rows > 0) {
                    while($row = $month_result->fetch_assoc()) {
                        $performance = 0;
                        if ($row['total'] > 0) {
                            $performance = round(($row['present'] / $row['total']) * 100);
                        }
                        $month_data[] = "{ period: '".$row['month']."', performance: ".$performance." }";
                    }
                }
                echo implode(",\n", $month_data);
                ?>
            ],
            year: [
                <?php
                // Get yearly data
                $year_query = "SELECT YEAR(date) as year, 
                              COUNT(*) as total, 
                              SUM(status = 'present') as present 
                              FROM attendance 
                              GROUP BY YEAR(date)";
                $year_result = $conn->query($year_query);
                
                $year_data = array();
                if ($year_result->num_rows > 0) {
                    while($row = $year_result->fetch_assoc()) {
                        $performance = 0;
                        if ($row['total'] > 0) {
                            $performance = round(($row['present'] / $row['total']) * 100);
                        }
                        $year_data[] = "{ period: '".$row['year']."', performance: ".$performance." }";
                    }
                }
                echo implode(",\n", $year_data);
                ?>
            ]
        };

        // Rest of your JavaScript remains the same
        function calculateTotal(data) {
            let sum = 0;
            data.forEach(item => {
                sum += item.performance;
            });
            return Math.round(sum / data.length);
        }

        function updatePerformanceView(timeFrame) {
            const tableBody = document.getElementById('performanceTable').getElementsByTagName('tbody')[0];
            const periodText = document.getElementById('periodText');
            const totalPerformance = document.getElementById('totalPerformance');
            const data = performanceData[timeFrame];
            
            tableBody.innerHTML = '';
            
            data.forEach(item => {
                const row = tableBody.insertRow();
                const periodCell = row.insertCell(0);
                const performanceCell = row.insertCell(1);
                
                periodCell.textContent = item.period;
                
                performanceCell.className = 'performance-cell';
                performanceCell.innerHTML = `
                    <div class="performance-bar" style="width: ${item.performance}%"></div>
                    <span class="performance-value">${item.performance}%</span>
                `;
            });
            
            const totalRow = tableBody.insertRow();
            totalRow.className = 'total-row';
            const totalCell = totalRow.insertCell(0);
            const totalValueCell = totalRow.insertCell(1);
            
            const totalValue = calculateTotal(data);
            
            totalCell.textContent = 'Total';
            totalValueCell.className = 'performance-cell';
            totalValueCell.innerHTML = `
                <div class="performance-bar" style="width: ${totalValue}%"></div>
                <span class="performance-value">${totalValue}%</span>
            `;
            
            let periodName = 'Week';
            if (timeFrame === 'month') {
                periodName = 'Month';
            } else if (timeFrame === 'year') {
                periodName = 'Year';
            }
            
            periodText.textContent = `Total Performance Per ${periodName}`;
            
            const currentValue = parseInt(totalPerformance.textContent);
            const targetValue = totalValue;
            
            let start = null;
            const duration = 500;
            
            function animateValue(timestamp) {
                if (!start) start = timestamp;
                const progress = Math.min((timestamp - start) / duration, 1);
                const value = Math.floor(currentValue + progress * (targetValue - currentValue));
                totalPerformance.textContent = `${value}%`;
                
                if (progress < 1) {
                    window.requestAnimationFrame(animateValue);
                } else {
                    totalPerformance.textContent = `${targetValue}%`;
                }
            }
            
            window.requestAnimationFrame(animateValue);
        }

        // Initialize with weekly data
        updatePerformanceView('week');

        // Add event listener for select change
        document.getElementById('timeFrame').addEventListener('change', function() {
            updatePerformanceView(this.value);
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>