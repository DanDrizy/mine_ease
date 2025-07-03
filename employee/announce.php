<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Department</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="../style/main.css">
    <style>
        .active_anno {
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
    </style>
</head>
<body>
    <?php
    include '../config.php';
    $page_name = 'Announcement';
    include 'main/slidebar.php';
    ?>
    
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="ann-stats-grid">
            <?php
            // Fetch announcements ordered by priority and creation date
            $query = "SELECT * FROM announcements WHERE type = 'All' OR type = 'emp' ORDER BY created_at DESC";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                while($announcement = $result->fetch_assoc()) {
                    $priority_class = 'priority-' . strtolower($announcement['location']);
                    $formatted_date = date('m-d-Y', strtotime($announcement['created_at']));
                    ?>
                    <div class="announce <?php echo $priority_class; ?>">
                        <h2 class="section-title"><?php echo htmlspecialchars($announcement['title']); ?></h2>
                        <p class="date"><?php echo $formatted_date; ?> | Published by: <?php echo htmlspecialchars($announcement['type']); ?></p>
                        <div class="message">
                            <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="announce">
                        <h2 class="section-title">No Announcements</h2>
                        <div class="message">
                            There are currently no announcements to display.
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