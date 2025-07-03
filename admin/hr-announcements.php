<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Resource Announcements | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .active_dash {
    background-color: var(--primary-color);
    color: white;
}
        .announcement-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .announcement-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #304c8e;
        }
        
        .announcement-date {
            color: #777;
            font-size: 0.9rem;
        }
        
        .announcement-content {
            line-height: 1.6;
            color: #444;
        }
        
        .announcement-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
        }
        
        .announcement-category {
            background-color: #f0f7ff;
            color: #304c8e;
            padding: 4px 10px;
            border-radius: 20px;
        }
        
        .announcement-actions a {
            color: var(--primary-color);
            margin-left: 15px;
            text-decoration: none;
        }
        
        .announcement-actions a:hover {
            text-decoration: underline;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .back-button {
            background-color: #eee;
            color: #444;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .back-button i {
            margin-right: 5px;
        }
        
        .back-button:hover {
            background-color: #ddd;
        }
        
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filter-box {
            display: flex;
            gap: 10px;
        }
        
        .filter-select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .search-box {
            display: flex;
        }
        
        .search-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            border-right: none;
        }
        
        .search-button {
            background-color: #304c8e;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            padding: 0 15px;
            cursor: pointer;
        }
        
        .no-announcements {
            text-align: center;
            padding: 40px 0;
            color: #777;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        
        .pagination a {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: #304c8e;
            background-color: #f0f7ff;
        }
        
        .pagination a.active {
            background-color: #304c8e;
            color: white;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #d0e0ff;
        }
    </style>
</head>
<body>
    <?php
     include'main/slidebar.php';
     ?>
    <div class="main-content">
        
    <?php include'main/header.php'; ?>
        
        <div class="page-header">
            <h1>Human Resource Announcements</h1>
            <a href="home.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        
        <div class="filter-container">
            <div class="filter-box">
                
               <a href="add_announcement.php" > <button class="filter-select"  style=" background: #304c8e; color:#fff; border: none; padding: 10px; cursor: pointer; "> <i  class="fas fa-add"></i>  Add New</button></a>
            </div>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search announcements...">
                <button class="search-button"><i class="fas fa-search"></i></button>
            </div>
        </div>
        
        <!-- Announcement 1 -->
        <div class="announcement-container">
            <div class="announcement-header">
                <div class="announcement-title">Annual Performance Review Schedule Released</div>
                <div class="announcement-date">April 10, 2025</div>
            </div>
            <div class="announcement-content">
                <p>Dear Team Members,</p>
                <p>We are pleased to announce that the schedule for the annual performance reviews has been finalized. Reviews will begin on May 15th and continue through June 30th. Your specific appointment time will be shared by your direct supervisor by the end of this week.</p>
                <p>Please ensure you have completed your self-assessment form available on the HR portal by May 10th. This year, we are implementing a new section focusing on professional development goals, so please give this special attention.</p>
                <p>If you have any questions about the process, please reach out to the HR department.</p>
            </div>
            <div class="announcement-footer">
                <div class="announcement-category">Admin Updates</div>
                <div class="announcement-actions">
                    <a href="#"><i class="far fa-file-pdf"></i> Download PDF</a>
                    <a href="#"><i class="far fa-comment"></i> Comment</a>
                </div>
            </div>
        </div>
        
        <!-- Announcement 2 -->
        <div class="announcement-container">
            <div class="announcement-header">
                <div class="announcement-title">Upcoming Company Retreat: Registration Open</div>
                <div class="announcement-date">April 8, 2025</div>
            </div>
            <div class="announcement-content">
                <p>Hello Everyone,</p>
                <p>We're excited to announce our annual company retreat, scheduled for June 15-17 at Mountain Pine Resort. This year's theme is "Innovation and Collaboration."</p>
                <p>The retreat will feature team-building activities, strategy sessions, and plenty of opportunities for relaxation and networking. Registration is now open through the Human Resource Portal until May 15th.</p>
                <p>Please indicate any dietary restrictions or accommodation needs during registration. Transportation will be provided from the main office.</p>
                <p>We look forward to seeing you all there!</p>
            </div>
            <div class="announcement-footer">
                <div class="announcement-category">Stock</div>
                <div class="announcement-actions">
                    <a href="#"><i class="far fa-file-pdf"></i> Download PDF</a>
                    <a href="#"><i class="far fa-comment"></i> Comment</a>
                </div>
            </div>
        </div>
        
        <!-- Announcement 3 -->
        <div class="announcement-container">
            <div class="announcement-header">
                <div class="announcement-title">New Health Insurance Benefits Starting Next Month</div>
                <div class="announcement-date">April 5, 2025</div>
            </div>
            <div class="announcement-content">
                <p>Dear Colleagues,</p>
                <p>We are pleased to announce that our company has secured an enhanced health insurance package that will take effect on May 1, 2025. The new plan includes:</p>
                <ul>
                    <li>Expanded mental health coverage with reduced co-pays</li>
                    <li>Additional preventive care services at no cost</li>
                    <li>Increased coverage for dependents</li>
                    <li>New telehealth options with 24/7 availability</li>
                </ul>
                <p>Information sessions will be held in the main conference room on April 15th and 16th. Representatives from our insurance provider will be available to answer questions.</p>
                <p>Please review the detailed information packet that was sent to your email and update your enrollment preferences by April 25th.</p>
            </div>
            <div class="announcement-footer">
                <div class="announcement-category">Stakeholder</div>
                <div class="announcement-actions">
                    <a href="#"><i class="far fa-file-pdf"></i> Download PDF</a>
                    <a href="#"><i class="far fa-comment"></i> Comment</a>
                </div>
            </div>
        </div>
        
        
    </div>
</body>
</html>