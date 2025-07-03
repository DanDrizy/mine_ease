<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-icon-logs {

            /* position: absolute; */
            display: flex;
            align-items: center;
            justify-content: center;
            top: 10px;
            right: 20px;
            cursor: pointer;
            background-color: #2c3e50;
            padding: 10px;
            border-radius: 50%;
            transition: background-color 0.3s ease;

        }
        .profile-icon-logs i {
            font-size: 20px;
            color: white;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .modal-content h3 {
            margin-top: 0;
            color: #333;
            font-size: 20px;
        }
        
        .modal-content p {
            color: #666;
            margin: 20px 0;
            font-size: 16px;
        }
        
        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }
        
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        
        .btn-cancel {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-cancel:hover {
            background-color: #7f8c8d;
        }
        
        .btn-logout {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-logout:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin</h1>
        <div class="header-icons">
            <div class="profile-icon-logs" onclick="showLogoutModal()">
                <i class="fa fa-sign-out" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to log out?</p>
            <div class="modal-buttons">
                <button class="btn btn-cancel" onclick="hideLogoutModal()">Cancel</button>
                <button class="btn btn-logout" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }
        
        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }
        
        function confirmLogout() {
            // Redirect to logout page
            window.location.href = '../log/logout.php';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                hideLogoutModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideLogoutModal();
            }
        });
    </script>
</body>
</html>