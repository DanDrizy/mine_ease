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
    }
    
    .search input,
    .search button
     {
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
</style>
<body>
    
    <div class="main-content">

        <div class="dashboard-row">
            <div class="dashboard-column">
                <div class="table-emp">
                    
                    <div class="search">
                        <h1>Report</h1>
                            <form method="GET" action="">
                                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" class="search-input">
                                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" class="search-input">
                                
                                <button type="submit" class="search-button">Search</button>
                            </form>
                        <!-- </div> -->
                    </div>

                    <div class="table-container">
                        <table>
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
                                        echo "<td>Frw" . number_format($row['salary'], 2) . "</td>";
                                        echo "<td>" . date('m-d-Y', strtotime($row['date'])) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No employees found</td></tr>";
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
</body>
</html>