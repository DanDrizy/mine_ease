<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Management</title>
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
        .hr-container {
            background: #D0E0FF;
            width: 100%;
            height: 82vh;
            border-radius: 15px;
            padding: 20px;
            overflow-y: auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card i {
            font-size: 24px;
            color: #4a90e2;
            margin-bottom: 10px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .section-title {
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4a90e2;
        }
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .employee-table th {
            background-color: #4a90e2;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }
        .employee-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        .employee-table tr:hover {
            background-color: #f5f5f5;
        }
        .employee-table tr:last-child td {
            border-bottom: none;
        }
        .assignment-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .assignment-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .assignment-box h3 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        .employee-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .employee-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .employee-item:last-child {
            border-bottom: none;
        }
        .employee-item:hover {
            background-color: #f5f5f5;
        }
        .employee-name {
            font-weight: bold;
        }
        .employee-detail {
            color: #666;
            font-size: 12px;
        }
        .hr-selector {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .hr-item {
            background-color: #f5f5f5;
            border-radius: 5px;
            padding: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .hr-item.selected {
            background-color: #e3f2fd;
            border: 2px solid #4a90e2;
        }
        .hr-avatar {
            width: 50px;
            height: 50px;
            background-color: #4a90e2;
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            color: #fff;
        }
        .hr-info {
            flex-grow: 1;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .transfer-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4a90e2;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .assign-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
        }
        .search-box {
            display: flex;
            margin-bottom: 15px;
        }
        .search-box input {
            flex-grow: 1;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php
    include 'main/slidebar.php';
    ?>
    <div class="main-content">
        <?php include 'main/header.php'; ?>
        
        <div class="hr-container">
            <div class="page-header">
                <h2>HR Management</h2>
                <div>
                    <select id="department-filter" class="filter-select">
                        <option value="all">All Departments</option>
                        <option value="mining">Mining</option>
                        <option value="it">IT</option>
                        <option value="finance">Finance</option>
                        <option value="marketing">Marketing</option>
                    </select>
                </div>
            </div>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-number">126</div>
                    <div class="stat-label">Total Employees</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-tie"></i>
                    <div class="stat-number">8</div>
                    <div class="stat-label">HR Managers</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-check"></i>
                    <div class="stat-number">115</div>
                    <div class="stat-label">Assigned</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-clock"></i>
                    <div class="stat-number">11</div>
                    <div class="stat-label">Unassigned</div>
                </div>
            </div>
            
            <h3 class="section-title">Employee Assignment</h3>
            
            <div class="assignment-container">
                <div class="assignment-box">
                    <h3>Available Employees</h3>
                    <div class="search-box">
                        <input type="text" placeholder="Search employees...">
                    </div>
                    <div class="employee-list" id="available-employees">
                        <div class="employee-item">
                            <div>
                                <div class="employee-name">Jean Claude</div>
                                <div class="employee-detail">IT Department • Kigali</div>
                            </div>
                            <button class="transfer-btn" onclick="moveToSelected(this)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="employee-item">
                            <div>
                                <div class="employee-name">Diane Ishimwe</div>
                                <div class="employee-detail">Marketing Department • Muhanga</div>
                            </div>
                            <button class="transfer-btn" onclick="moveToSelected(this)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="employee-item">
                            <div>
                                <div class="employee-name">Eric Nshimiye</div>
                                <div class="employee-detail">Sales Department • Rwamagana</div>
                            </div>
                            <button class="transfer-btn" onclick="moveToSelected(this)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="employee-item">
                            <div>
                                <div class="employee-name">Claudine Abayo</div>
                                <div class="employee-detail">Customer Service • Nyamata</div>
                            </div>
                            <button class="transfer-btn" onclick="moveToSelected(this)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="employee-item">
                            <div>
                                <div class="employee-name">James Karera</div>
                                <div class="employee-detail">Finance Department • Kigali</div>
                            </div>
                            <button class="transfer-btn" onclick="moveToSelected(this)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="employee-item">
                            <div>
                                <div class="employee-name">Jolie Uwamahoro</div>
                                <div class="employee-detail">Operations • Huye</div>
                            </div>
                            <button class="transfer-btn" onclick="moveToSelected(this)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="assignment-box">
                    <h3>Selected Employees</h3>
                    <div class="search-box">
                        <input type="text" placeholder="Search selected employees...">
                    </div>
                    <div class="employee-list" id="selected-employees">
                        <div class="employee-item">
                            <button class="transfer-btn" onclick="moveToAvailable(this)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div>
                                <div class="employee-name">Kevin Ishimwe</div>
                                <div class="employee-detail">Mining Department • Kigali</div>
                            </div>
                        </div>
                        <div class="employee-item">
                            <button class="transfer-btn" onclick="moveToAvailable(this)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div>
                                <div class="employee-name">Murera Mimi</div>
                                <div class="employee-detail">Manager Department • Kabuga</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="section-title">Assign to HR Manager</h3>
            
            <div class="hr-selector">
                <div class="hr-item" onclick="selectHR(this)">
                    <div class="hr-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="hr-info">
                        <div class="employee-name">Alice Uwimana</div>
                        <div class="employee-detail">HR Manager • Kigali • Managing 32 employees</div>
                    </div>
                </div>
                <div class="hr-item" onclick="selectHR(this)">
                    <div class="hr-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="hr-info">
                        <div class="employee-name">Patrick Ndoli</div>
                        <div class="employee-detail">HR Manager • Nyagatare • Managing 28 employees</div>
                    </div>
                </div>
                <div class="hr-item" onclick="selectHR(this)">
                    <div class="hr-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="hr-info">
                        <div class="employee-name">Grace Mukamana</div>
                        <div class="employee-detail">HR Manager • Huye • Managing 26 employees</div>
                    </div>
                </div>
            </div>
            
            <button class="assign-btn">
                <i class="fas fa-check-circle"></i> Assign Selected Employees
            </button>
        </div>
    </div>

    <script>
        // Function to move employee from available to selected list
        function moveToSelected(button) {
            const employeeItem = button.parentNode;
            const employeeClone = employeeItem.cloneNode(true);
            
            // Change button direction and onclick function
            const transferButton = employeeClone.querySelector('.transfer-btn');
            transferButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
            transferButton.setAttribute('onclick', 'moveToAvailable(this)');
            
            // Rearrange to put button on the left
            employeeClone.insertBefore(transferButton, employeeClone.firstChild);
            
            // Add to selected list and remove from available list
            document.getElementById('selected-employees').appendChild(employeeClone);
            employeeItem.remove();
        }
        
        // Function to move employee from selected to available list
        function moveToAvailable(button) {
            const employeeItem = button.parentNode;
            const employeeClone = employeeItem.cloneNode(true);
            
            // Change button direction and onclick function
            const transferButton = employeeClone.querySelector('.transfer-btn');
            transferButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
            transferButton.setAttribute('onclick', 'moveToSelected(this)');
            
            // Move button to the right
            employeeClone.removeChild(transferButton);
            const employeeInfo = employeeClone.firstChild;
            employeeClone.appendChild(transferButton);
            
            // Add to available list and remove from selected list
            document.getElementById('available-employees').appendChild(employeeClone);
            employeeItem.remove();
        }
        
        // Function to select HR manager
        function selectHR(element) {
            // Remove selected class from all HR items
            document.querySelectorAll('.hr-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Add selected class to clicked HR item
            element.classList.add('selected');
        }
    </script>
</body>
</html>