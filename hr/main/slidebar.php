<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <style>
        .log-icon
    {
        text-align: center;
        padding: 20px;
        margin: 0 0 30px;
        color: white;
        border-radius: 10px 10px 0 0;
    }
    </style>
<div class="sidebar">
        <div class="log-icon">
        <h1>Mine_Ease</h1>
        <p>Management System</p>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php " class="sidebar-item active-dash">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="2" y="3" width="20" height="18" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="8" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="8" y1="14" x2="16" y2="14" stroke="currentColor" stroke-width="2"/>
                </svg>
                Dashboard
            </a>
            <a href="emp_m.php" class="sidebar-item active-emp">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
                Employee Management
            </a>
            
            <a href="payroll.php" class="sidebar-item active-payroll"> 
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="4" y1="10" x2="20" y2="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="10" y1="4" x2="10" y2="20" stroke="currentColor" stroke-width="2"/>
                </svg>
                Payroll Processing
            </a>
            <a href="leave.php" class="sidebar-item active-leave"> 
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-2 1.7l-1.38 9a2 2 0 002 2.3H10z" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M17 2v7" stroke="currentColor" stroke-width="2"/>
                </svg>
                Leave Requests
            </a>
            <a href="report.php" class="sidebar-item active-report"> 
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="8" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="8" y1="14" x2="12" y2="14" stroke="currentColor" stroke-width="2"/>
                </svg>
                HR Reports
            </a>
        </div>
    </div>
</body>
</html>