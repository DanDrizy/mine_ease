<?php
// Database connection
require_once '../config.php';

// Get equipment data
$equipment = [];
$sql = "SELECT * FROM equipment";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }
}

// Get departments for dropdown
$departments = [];
$sql = "SELECT id, name FROM departments";
$dept_result = $conn->query($sql);
if ($dept_result && $dept_result->num_rows > 0) {
    while($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager | Equipment Tracking</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Original styles with enhancements */
        .active-equipment-tracking {
            background-color: #7A4B9D;
            color: white;
        }

        .bottom-cards-stock {
            background: #D0E0FF;
            width: 100%;
            height: 70vh;
            border-radius: 10px;
            padding: 20px;
            overflow: auto;
        }
        
        .search-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #4768A8;
            border-radius: 5px;
        }
        
        .search-input {
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            width: 300px;
            font-size: 14px;
        }
        
        .search-button, .adding button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .search-button {
            background: rgb(186, 95, 255);
            color: white;
        }
        
        .adding button {
            background: white;
            color: rgb(186, 95, 255);
            border: 2px solid #5a3e7c;
        }
        
        .adding button:hover {
            background: #f5f5f5;
        }
        
        .stock-table {
            width: 100%;
            overflow: auto;
        }
        
        .stock-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .stock-table th, .stock-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .stock-table th {
            background: #4768A8;
            color: white;
            position: sticky;
            top: 0;
        }
        
        .stock-table tr:hover {
            background-color: rgba(122, 75, 157, 0.1);
        }
        
        .action-btn {
            padding: 6px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            font-size: 14px;
        }
        
        .view-btn {
            background: #4768A8;
            color: white;
        }
        
        .edit-btn {
            background: #5a78b8;
            color: white;
        }
        
        .delete-btn {
            background: #d9534f;
            color: white;
        }
        
        .table-title {
            margin-top: 0;
            margin-bottom: 15px;
            color: #4768A8;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 25px;
            border-radius: 8px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        
        .modal-title {
            font-size: 1.25rem;
            color: #4768A8;
            margin: 0;
        }
        
        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        .close:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #7A4B9D;
            outline: none;
            box-shadow: 0 0 0 2px rgba(122, 75, 157, 0.2);
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #7A4B9D;
            color: white;
            border: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
            }
            
            .search-box {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php 
    $page_name = " / Equipment Tracking";
    include 'main/sidebar.php'; 
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <div class="bottom-cards-stock">
            <div class="search-box">
                <form method="GET" class="search">
                    <input type="text" name="search" placeholder="Search for items..." class="search-input" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="search-button">Search</button>
                </form>
                <div class="adding">
                    <a href="add-equipment.php"><button>Add New</button></a>
                </div>
            </div>
            
            <div id="stockInTable" class="stock-table stock-in-table active">
                <h3 class="table-title">
                    <i class="fas fa-tools"></i>
                    Equipment Inventory
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Location</th>
                            <th>Department</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($equipment as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['location']); ?></td>
                            <td><?php echo htmlspecialchars($item['department_id']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($item['date_added'])); ?></td>
                            <td>
                                <button class="action-btn view-btn" onclick="showViewModal(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn edit-btn" onclick="showEditModal(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="confirmDelete(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($equipment)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No equipment found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Equipment Details</h3>
                <span class="close" onclick="closeModal('viewModal')">&times;</span>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Equipment</h3>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button class="btn btn-primary" onclick="saveEquipment()">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        // Global variable to store current equipment ID
        let currentEquipmentId = null;
        
        // Show view modal
        function showViewModal(equipmentId) {
            currentEquipmentId = equipmentId;
            const modal = document.getElementById('viewModal');
            const modalBody = document.getElementById('viewModalBody');
            
            // Show loading state
            modalBody.innerHTML = '<p>Loading equipment details...</p>';
            modal.style.display = 'block';
            
            // Fetch equipment details via AJAX
            fetch(`get_equipment_details.php?id=${equipmentId}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    modalBody.innerHTML = `<p>Error loading details: ${error.message}</p>`;
                });
        }
        
        // Show edit modal
        function showEditModal(equipmentId) {
            currentEquipmentId = equipmentId;
            const modal = document.getElementById('editModal');
            const modalBody = document.getElementById('editModalBody');
            
            // Show loading state
            modalBody.innerHTML = '<p>Loading equipment for editing...</p>';
            modal.style.display = 'block';
            
            // Fetch equipment details via AJAX
            fetch(`get_equipment_edit.php?id=${equipmentId}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    modalBody.innerHTML = `<p>Error loading for edit: ${error.message}</p>`;
                });
        }
        
        // Save equipment changes
        function saveEquipment() {
            const form = document.getElementById('editEquipmentForm');
            const formData = new FormData(form);
            
            fetch('save_equipment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Equipment updated successfully!');
                    closeModal('editModal');
                    window.location.reload();
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                alert(`Error: ${error.message}`);
            });
        }
        
        // Confirm delete
        function confirmDelete(equipmentId) {
            if (confirm('Are you sure you want to delete this equipment? This action cannot be undone.')) {
                fetch(`delete_equipment.php?id=${equipmentId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Equipment deleted successfully!');
                        window.location.reload();
                    } else {
                        alert(`Error: ${data.message}`);
                    }
                })
                .catch(error => {
                    alert(`Error: ${error.message}`);
                });
            }
        }
        
        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>