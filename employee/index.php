<?php
// Start session and include database connection
session_start();
include '../config.php';

// Get current user ID from session
$user_id = $_SESSION['user_id'] ?? 0;

$select_payroll = $conn->query("SELECT * FROM users WHERE id = $user_id");
$payroll = $select_payroll->fetch_assoc();


//attendance

$select_dates = mysqli_query($conn, "SELECT * FROM attendance WHERE user_id = '$user_id' ORDER BY date DESC LIMIT 7");
$dates = mysqli_fetch_all($select_dates, MYSQLI_ASSOC);

// Get current date
$current_date = date('Y-m-d');

$announcement = mysqli_query($conn,"SELECT * FROM announcements WHERE type = 'All' OR  type = 'emp' ORDER BY created_at DESC ");
// $data = mysqli_fetch_array($announcement);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/stats.css">
    <style></style>
</head>
<body>
    <?php
    $page_name = 'Dashboard';
    include 'main/slidebar.php';
    ?>
    
    <div class="main-content">

        
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-items">
                    <div class="table-content">
                        <table>
                            <tr>
                                <th>Dates</th>
                                <th>Department</th>
                            </tr>
                            <?php foreach ($dates as $date) : ?>
                            <tr>
                                <td> <?php
                                
                                $day = date( 'l',strtotime( $date['date']));
                                echo $day;

                                $present = $date['status'];

                                switch ($day) {
                                    case 'Monday':
                                        if( $present == 'present'){
                                            $result = 'Present';
                                        }else{
                                            $result = 'Absent';
                                        }
                                        break;
                                    case 'Tuesday':
                                        if( $present == 'present'){
                                            $result = 'Present';
                                        }else{
                                            $result = 'Absent';
                                        }
                                        break;
                                    case 'Wednesday':
                                        if( $present == 'present'){
                                            $result = 'Present';
                                        }else{
                                            $result = 'Absent';
                                        }
                                        break;
                                    case 'Thursday':
                                        if( $present == 'present'){
                                            $result = 'Present';
                                        }else{
                                            $result = 'Absent';
                                        }
                                        break;
                                    case 'Friday':
                                        if( $present == 'present'){
                                            $result = 'Present';
                                        }else{
                                            $result = 'Absent';
                                        }
                                        break;
                                    case 'Saturday':
                                        if( $present == 'present'){
                                            $result = 'Present';
                                        }else{
                                            $result = 'Absent';
                                        }
                                        break;
                                }
                                

                                
                                ?> </td>
                                <td> <?php echo $result; ?> </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="announcement-section">
                    <div class="table-content">
                        
                        <div class="table-container">
                            <table id="performanceTable">
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="payroll-summary">
                        <h3>Payroll Summary</h3>
                        <div class="payroll-item">
                            <span>Gross Salary:</span>
                            <span> <?php echo number_format($payroll['salary']) ?> Rwf</span>
                        </div>
                        <div class="payroll-item">
                            <span>Providence Fund:</span>
                            <span>- <?php echo number_format($payroll['providence']) ?> Rwf</span>
                        </div>
                        <div class="payroll-item">
                            <span>Tax Deduction:</span>
                            <span>- <?php echo number_format($payroll['tax']) ?> Rwf</span>
                        </div>
                        <div class="payroll-item">
                            <span>Loan Deduction:</span>
                            <span>- <?php echo number_format($payroll['loan']) ?>Rwf</span>
                        </div>
                        <div class="payroll-item payroll-total">
                            <span>Net Salary: 
                        <?php
                        $new_amount = $payroll['salary'] - $payroll['providence'] - $payroll['tax'] - $payroll['loan'];
                        ?>
                        </span>
                            <span> <?php echo number_format($new_amount); ?> Rwf</span>
                        </div>
                    </div>
                </div>
            </div>
        
        
    </div>
    <div class="bottom-grid-bottom">
    
            
            <div class="admin-announce">
                <h2 class="section-title">Admin Announcement</h2>
                <?php while($data = mysqli_fetch_array($announcement)){ ?>
                    <div class="announcement-item">
                        <h3 class="announcement-title"><?php echo $data['title'] ?></h3>
                        <p class="announcement-content"><?php echo $data['content'] ?></p>
                    </div>
                <?php } ?>
            </div>
       
            </div>
        </div>
    </div>
</body>
</html>