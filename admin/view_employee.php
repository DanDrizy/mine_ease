<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/user.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .active_user {
            background-color: var(--primary-color);
            color: white;
        }
        .user-detail-container {
            background: #D0E0FF;
            width: 100%;
            height: 82vh;
            border-radius: 15px;
            padding: 20px;
            overflow-y: auto;
        }
        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .back-btn {
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .user-profile {
            display: flex;
            margin-bottom: 30px;
        }
        .user-avatar {
            width: 150px;
            height: 150px;
            background-color: #ccc;
            border-radius: 50%;
            margin-right: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 80px;
            color: #fff;
        }
        .user-info {
            flex-grow: 1;
        }
        .user-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .user-role {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
        }
        .user-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #FFC107;
            color: #333;
        }
        .status-active {
            background-color: #4CAF50;
            color: white;
        }
        .status-inactive {
            background-color: #F44336;
            color: white;
        }
        .user-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .detail-card h3 {
            margin-top: 0;
            color: #4a90e2;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .detail-value {
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-approve {
            background-color: #4CAF50;
            color: white;
        }
        .btn-deny {
            background-color: #F44336;
            color: white;
        }
        .btn-edit {
            background-color: #FF9800;
            color: white;
        }
    </style>
</head>
<body>
    <?php
    include 'main/slidebar.php';
    ?>
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="user-detail-container">
            <div class="user-header">
                <a href="user.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
                <h2>User Details</h2>
            </div>
            
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">Kevin Ishimwe</div>
                    <div class="user-role">Mining Department - Admin</div>
                    <div class="user-status status-pending">Pending Approval</div>
                </div>
            </div>
            
            <div class="user-details-grid">
                <div class="detail-card">
                    <h3>Personal Information</h3>
                    <div class="detail-item">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">Kevin Ishimwe</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value">kevin.ishimwe@example.com</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value">+250 788 123 456</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">National ID</div>
                        <div class="detail-value">1199080012345678</div>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3>Account Information</h3>
                    <div class="detail-item">
                        <div class="detail-label">Username</div>
                        <div class="detail-value">kevin.ishimwe</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Password</div>
                        <div class="detail-value password-container">
                            <span id="password">••••••••••</span>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">User Type</div>
                        <div class="detail-value">Admin</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Registration Date</div>
                        <div class="detail-value">21 March 2023</div>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3>Work Information</h3>
                    <div class="detail-item">
                        <div class="detail-label">Department</div>
                        <div class="detail-value">Mining</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Site Location</div>
                        <div class="detail-value">Kigali</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Position</div>
                        <div class="detail-value">Senior Manager</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Employee ID</div>
                        <div class="detail-value">EMP-2023-0045</div>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3>System Access</h3>
                    <div class="detail-item">
                        <div class="detail-label">Last Login</div>
                        <div class="detail-value">15 April 2025, 09:45 AM</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Access Level</div>
                        <div class="detail-value">Level 3 (Advanced)</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">Pending Approval</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Authorized By</div>
                        <div class="detail-value">Pending</div>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-approve">
                    <i class="fas fa-check-circle"></i> Approve Access
                </button>
                <button class="btn btn-deny">
                    <i class="fas fa-times-circle"></i> Deny Access
                </button>
                <button class="btn btn-edit">
                    <i class="fas fa-edit"></i> Edit Details
                </button>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordField.innerHTML === '••••••••••') {
                passwordField.innerHTML = 'Kev!n2023Pass';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.innerHTML = '••••••••••';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>