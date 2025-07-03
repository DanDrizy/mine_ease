<?php 

session_start();
$user_id = $_SESSION['user_id'];
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
        .active_request {
            color: yellow;
            border-bottom: 2px solid yellow;
        }
        .ann-stats-grid {
            width: 100%;
        }
        .announce {
            padding: 20px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            height: auto;
            margin-bottom: 20px;
        }
        .message {
            padding: 20px;
            border-radius: 20px;
            overflow-y: auto;
            font-size: 13px;
            background-color: #f9f9f9;
            border: 1px solid #eaeaea;
            margin-top: 10px;
        }
        p.date {
            text-align: center;
            color: #888;
            font-size: 12px;
            margin: 5px 0;
        }
        .section-title {
            color: #153D00;
            margin-bottom: 5px;
        }
        .priority-high {
            border-left: 4px solid red;
            padding-left: 16px;
        }
        .priority-medium {
            border-left: 4px solid orange;
            padding-left: 16px;
        }
        .priority-low {
            border-left: 4px solid green;
            padding-left: 16px;
        }
        .umwanzuro
        {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .umwanzuro p {
            margin: 0;
        }
        .pending
        {
            color: grey;
        }
        .approved
        {
            color: green;
        }
        .denied
        {
            color: red;
        }
    </style>
</head>
<body>
    <?php
    include '../config.php';
    $page_name = 'Request';
    include 'main/slidebar.php';
    ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="ann-stats-grid">
            <?php
            // Fetch announcements ordered by priority and creation date
            $query = "SELECT * FROM leave_requests WHERE employee_id = '$user_id' ORDER BY status DESC";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                while($announcement = $result->fetch_assoc()) {
                    $priority_class = 'Department-' . strtolower($announcement['department']);
                    $formatted_date = date('m-d-Y', strtotime($announcement['created_at']));
                    ?>
                    <div class="announce <?php echo $priority_class; ?>">
                        <div class="umwanzuro"><h2 class="section-title"><?php echo htmlspecialchars($announcement['leave_type']); ?></h2>
                        
                        
                        <h3><?php echo " <span class=". $announcement['status'] ." > ". nl2br(htmlspecialchars($announcement['status'])) ." </span> " ?></h3>
                    
                    
                    
                    </div>
                        <p class="date"><?php echo $formatted_date; ?> | Requested by: <?php echo htmlspecialchars($announcement['employee_name']); ?></p>
                        <div class="message">
                            <?php echo htmlspecialchars($announcement['reason']); ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="announce">
                        <h2 class="section-title">No Request</h2>
                        <div class="message">
                            There are currently no Request to display.
                        </div>
                      </div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>