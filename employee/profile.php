<?php
    
    // Assuming you have a session with user data
    session_start();
    require_once '../config.php'; 
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="../style/main.css">
    <style>
        .active_atte {
            color: yellow;
            border-bottom:2px solid yellow;
        }
        .stats-grid {
            background: white;
            width: 100%;
            height: 15rem;
            border-radius: 20px;
            display: flex;
        }
        .table-content {
            margin: 20px;
            width: 100%;
        }
        .table-content table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .table-content table th {
            color: blue;
        }
        .table-content table td {
            color: darkblue;
        }
        .table-content table th, .table-content table td {
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
            height: calc(22rem - 20px);
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
        .table-content-profile {
            width: 100%;
            display: flex;
            flex-direction: row;
            gap: 20px;
            padding: 20px;
        }
        .table-content-profile img {
            width: 30%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .table-content-profile .info {
            width: 70%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .info h3 {
            margin: 5px 0;
            color: #333;
        }
        .info p {
            margin: 0 0 10px 15px;
            color: #555;
        }
    </style>
</head>
<body>
    <?php
    $page_name = 'Profile';
    include 'main/slidebar.php';
    
    // Get user ID from session or URL
    $user_id = $_SESSION['user_id'] ?? null; // Adjust based on your auth system
    
    if ($user_id) {
        // Fetch user data from database
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }
    ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="stats-grid">
            <div class="table-content-profile">
                <img src="emp_image/<?php echo $user['profile_image'] ?? 'p.png'; ?>" alt="Profile Image">
                <div class="info">
                    <h3>Names:</h3> 
                    <p><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></p> 
                    
                    <h3>Department:</h3> 
                    <p><?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></p> 
                    
                    <h3>SITE:</h3> 
                    <p><?php echo htmlspecialchars($user['site'] ?? 'N/A'); ?></p>
                    
                    <h3>Email:</h3>
                    <p><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                    
                    <h3>Phone:</h3>
                    <p><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bottom-grid">
            <div class="stock-status">
                <div class="table-content">
                    <div class="table-container">
                        <table id="performanceTable">
                            <tbody>
                                <?php
                                // Generate payment status for each month
                                $months = [
                                    'January', 'February', 'March', 'April', 
                                    'May', 'June', 'July', 'August',
                                    'September', 'October', 'November', 'December'
                                ];
                                
                                $current_month = date('n') - 1; // Get current month index (0-11)
                                
                                foreach ($months as $index => $month) {
                                    $status = ($index < $current_month) ? 'Payed' : 'N-Y';
                                    echo "
                                    <tr class='total-row'>
                                        <td>$month</td>
                                        <td class='performance-cell'>
                                            <div class='performance-bar'></div>
                                            <span class='performance-value'>$status</span>
                                        </td>
                                    </tr>
                                    ";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="admin-announce">
                <table>
                    <tr>
                        <th>Reputation</th>
                        <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td><?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Site</th>
                        <td><?php echo htmlspecialchars($user['site'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Salary</th>
                        <td><?php echo number_format($user['salary'] ?? 0); ?> RWF</td>
                    </tr>
                    <tr>
                        <th>Tax</th>
                        <td><?php echo number_format($user['tax'] ?? 0); ?> RWF</td>
                    </tr>
                    <tr>
                        <th>Loan</th>
                        <td><?php echo number_format($user['loan'] ?? 0); ?> RWF</td>
                    </tr>
                    <tr>
                        <th>Member Since</th>
                        <td><?php echo date('M Y', strtotime($user['registration_date'] ?? 'now')); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // You can add JavaScript here to animate the performance bars if needed
        document.addEventListener('DOMContentLoaded', function() {
            const bars = document.querySelectorAll('.performance-bar');
            bars.forEach(bar => {
                const status = bar.nextElementSibling.textContent;
                if (status === 'Payed') {
                    bar.style.width = '100%';
                    bar.style.backgroundColor = 'rgba(0, 128, 0, 0.2)';
                } else {
                    bar.style.width = '0%';
                }
            });
        });
    </script>
</body>
</html>