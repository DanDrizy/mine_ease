<?php
    // Database connection
    require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Original styles remain the same */
        .active-stock-levels {
            background-color: #7A4B9D;
            color: white;
        } 
        .dashboard-cards-stock {
            width: 100%;  
            margin: 10px 0px 20px;
        }
        .card-stock {
            background: #D0E0FF;
            width: 100%;
            overflow: hidden;
        }
        .stock-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
        }
        .stock-in, .stock-out {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #4768A8;
            width: 40%;
            padding: 10px;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .stock-in.active, .stock-out.active {
            background: #7A4B9D;
        }
        .stock-in:hover, .stock-out:hover {
            background: #5a78b8;
        }
        .detail {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail img {
            width: 40px;
            height: 40px;
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
            padding: 10px;
            background: #4768A8;
        }
        .search-box .search {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .search-input {
            padding: 10px;
            border-radius: 5px;
            border: none;
            width: 300px;
            border: 2px solid transparent;
        }
        .search-input:focus {
            outline: none;
            border: 2px solid #7A4B9D;
        }
        .search-button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background:rgb(186, 95, 255);
            color: white;
            cursor: pointer;
        }
        .adding button {
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid #5a3e7c;
            background: white;
            color:rgb(186, 95, 255);
            cursor: pointer;
        }
        .stock-table {
            width: 100%;
            overflow: auto;
        }
        .stock-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .stock-table th, .stock-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .stock-table th {
            background: #4768A8;
            color: white;
        }
        .stock-table tr:hover {
            background: #f1f1f1;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin: 0 2px;
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
        .action-btn:hover {
            opacity: 0.8;
        }
        
        .stock-in-table, .stock-out-table {
            display: none;
        }
        
        .stock-in-table.active, .stock-out-table.active {
            display: block;
        }
        
        .table-title {
            margin-top: 0;
            margin-bottom: 15px;
            color: #4768A8;
            font-size: 1.5rem;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
        }

        .popup-title {
            color: #4768A8;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .popup-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .popup-btn {
            padding: 12px 30px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .stock-in-btn {
            background-color: #4768A8;
            color: white;
        }

        .stock-out-btn {
            background-color: #7A4B9D;
            color: white;
        }

        .popup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .close-popup {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .close-popup:hover {
            color: #333;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            max-width: 800px;
            pointer-events: none;
        }
        
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 0.3rem;
            outline: 0;
        }
        
        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            background-color: #4768A8;
            color: white;
        }
        
        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
            font-size: 1.25rem;
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: white;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            background: transparent;
            border: 0;
        }
        
        .close:hover {
            opacity: .75;
            text-decoration: none;
        }
        
        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }
        
        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 1rem;
            border-top: 1px solid #e9ecef;
        }
        
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, 
                        border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #4768A8;
            border-color: #4768A8;
        }
        
        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .form-label {
            display: inline-block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        /* Toast Notifications */
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 5px;
    color: white;
    background-color: #4768A8;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1100;
}

.toast-notification.show {
    transform: translateY(0);
    opacity: 1;
}

.toast-notification.success {
    background-color: #28a745;
}

.toast-notification.error {
    background-color: #dc3545;
}

.toast-notification.warning {
    background-color: #ffc107;
    color: #212529;
}
    </style>
</head>
<body>

   <!-- Sidebar -->
    <?php 
    $page_name = " / Stock Level";
    include 'main/sidebar.php'; 
    
    // Initialize variables
    $total_stock_in = 0;
    $total_stock_out = 0;
    $stock_in_items = [];
    $stock_out_items = [];
    
    // Get total stock in (assuming status 'in-stock' indicates stock-in)
    $sql = "SELECT SUM(quantity) as total FROM stock_items WHERE status = 'in-stock'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_stock_in = $row['total'] ? $row['total'] : 0;
    }
    
    // Get total stock out (assuming status 'out-of-stock' indicates stock-out)
    $sql = "SELECT SUM(quantity) as total FROM stock_items WHERE status = 'out-of-stock'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_stock_out = $row['total'] ? $row['total'] : 0;
    }
    
    // Get stock-in items
    $sql = "SELECT id, item_name, category, quantity, unit_price, total_value, location, created_at 
            FROM stock_items 
            WHERE status = 'in-stock'
            ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $stock_in_items[] = $row;
        }
    }
    
    // Get stock-out items
    $sql = "SELECT id, item_name, category, quantity, unit_price, total_value, location, created_at 
            FROM stock_items 
            WHERE status = 'out-of-stock'
            ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $stock_out_items[] = $row;
        }
    }
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards-stock">
            <!-- Stock Card -->
            <div class="card-stock">
                <div class="stock-box">
                    <div id="stockInButton" class="stock-in active">
                        <div class="detail">
                            <img src="../img/stock-in.svg" alt="Stock In Icon">
                            <h2>STOCK-IN</h2>
                        </div>
                        <h4><?php echo number_format($total_stock_in); ?></h4>
                    </div>
                    <div id="stockOutButton" class="stock-out">
                        <div class="detail">
                            <img src="../img/stock-out.svg" alt="Stock Out Icon">
                            <h2>STOCK-OUT</h2>
                        </div>
                        <h4><?php echo number_format($total_stock_out); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bottom-cards-stock">
            <div class="search-box">
                <form method="GET" action="" class="search">
                    <input type="text" name="search" placeholder="Search for items..." class="search-input" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="search-button">Search</button>
                </form>
                <div class="adding">
                    <button id="addNewButton">Add New</button>
                </div>
            </div>
            
            <!-- Stock-In Table -->
            <div id="stockInTable" class="stock-table stock-in-table active">
                <h3 class="table-title">Stock-In Items</h3>
                <table>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Location</th>
                        <th>Date Added</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach($stock_in_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><?php echo number_format($item['quantity']); ?></td>
                        <td>Frw<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td>Frw<?php echo number_format($item['total_value'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['location']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
                        <td>
                            <button class="action-btn view-btn" onclick="showViewModal(<?php echo $item['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" onclick="showEditModal(<?php echo $item['id']; ?>, 'in')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="showDeleteModal(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($stock_in_items)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No stock-in items found</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <!-- Stock-Out Table -->
            <div id="stockOutTable" class="stock-table stock-out-table">
                <h3 class="table-title">Stock-Out Items</h3>
                <table>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Location</th>
                        <th>Date Added</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach($stock_out_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><?php echo number_format($item['quantity']); ?></td>
                        <td>Frw<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td>Frw<?php echo number_format($item['total_value'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['location']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
                        <td>
                            <button class="action-btn view-btn" onclick="showViewModal(<?php echo $item['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" onclick="showEditModal(<?php echo $item['id']; ?>, 'out')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="showDeleteModal(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($stock_out_items)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No stock-out items found</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Popup for Add New -->
    <div id="addNewPopup" class="popup-overlay">
        <div class="popup-content">
            <span id="closePopup" class="close-popup">&times;</span>
            <h2 class="popup-title">What would you like to add?</h2>
            <div class="popup-buttons">
                <button id="addStockInBtn" class="popup-btn stock-in-btn">Stock-In</button>
                <button id="addStockOutBtn" class="popup-btn stock-out-btn">Stock-Out</button>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item Details</h5>
                    <button type="button" class="close" onclick="closeModal('viewModal')">&times;</button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="close" onclick="closeModal('editModal')">&times;</button>
                </div>
                <div class="modal-body" id="editModalBody">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveItem()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" onclick="closeModal('deleteModal')">&times;</button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    <p>Are you sure you want to delete this item?</p>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables to store current item ID and type
        let currentItemId = null;
        let currentItemType = null;
        
        // Toggle between stock-in and stock-out views
        const stockInButton = document.getElementById('stockInButton');
        const stockOutButton = document.getElementById('stockOutButton');
        const stockInTable = document.getElementById('stockInTable');
        const stockOutTable = document.getElementById('stockOutTable');
        
        function showStockIn() {
            stockInButton.classList.add('active');
            stockOutButton.classList.remove('active');
            stockInTable.classList.add('active');
            stockOutTable.classList.remove('active');
        }
        
        function showStockOut() {
            stockOutButton.classList.add('active');
            stockInButton.classList.remove('active');
            stockOutTable.classList.add('active');
            stockInTable.classList.remove('active');
        }
        
        stockInButton.addEventListener('click', showStockIn);
        stockOutButton.addEventListener('click', showStockOut);
        
        // Set default view
        showStockIn();

        // Popup functionality
        const addNewButton = document.getElementById('addNewButton');
        const addNewPopup = document.getElementById('addNewPopup');
        const closePopup = document.getElementById('closePopup');
        const addStockInBtn = document.getElementById('addStockInBtn');
        const addStockOutBtn = document.getElementById('addStockOutBtn');

        addNewButton.addEventListener('click', function() {
            addNewPopup.style.display = 'flex';
        });

        closePopup.addEventListener('click', function() {
            addNewPopup.style.display = 'none';
        });

        addNewPopup.addEventListener('click', function(event) {
            if (event.target === addNewPopup) {
                addNewPopup.style.display = 'none';
            }
        });

        addStockInBtn.addEventListener('click', function() {
            window.location.href = 'stock-in-add.php';
        });

        addStockOutBtn.addEventListener('click', function() {
            window.location.href = 'stock-out-add.php';
        });

        // Modal functions
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        // View Item Modal
        function showViewModal(itemId) {
            currentItemId = itemId;
            const modal = document.getElementById('viewModal');
            const modalBody = document.getElementById('viewModalBody');
            
            // Show loading state
            modalBody.innerHTML = '<p>Loading item details...</p>';
            showModal('viewModal');
            
            // Fetch item details via AJAX
            fetch(`get-item-details.php?id=${itemId}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    modalBody.innerHTML = `<p>Error loading item details: ${error.message}</p>`;
                });
        }
        
        // Edit Item Modal
        function showEditModal(itemId, itemType) {
            currentItemId = itemId;
            currentItemType = itemType;
            const modal = document.getElementById('editModal');
            const modalBody = document.getElementById('editModalBody');
            
            // Show loading state
            modalBody.innerHTML = '<p>Loading item for editing...</p>';
            showModal('editModal');
            
            // Fetch item details via AJAX
            fetch(`get-item-edit.php?id=${itemId}&type=${itemType}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    modalBody.innerHTML = `<p>Error loading item for editing: ${error.message}</p>`;
                });
        }
        
        // Save Item
        function saveItem() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);
            
            fetch('save-item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item updated successfully!');
                    closeModal('editModal');
                    window.location.reload(); // Refresh the page to show changes
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                alert(`Error: ${error.message}`);
            });
        }
        
        // Delete Item Modal
function showDeleteModal(itemId) {
    currentItemId = itemId;
    showModal('deleteModal');
}

// Confirm Delete - Updated version
function confirmDelete() {
    // Show loading state
    const deleteBtn = document.querySelector('#deleteModal .btn-danger');
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    deleteBtn.disabled = true;

    // Use FormData to send the data
    const formData = new FormData();
    formData.append('id', currentItemId);
    formData.append('_method', 'DELETE'); // For RESTful convention

    fetch('delete-item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Success message with toast notification
            showToast('Item deleted successfully!', 'success');
            closeModal('deleteModal');
            // Refresh the table data without full page reload
            refreshTableData();
        } else {
            showToast(data.message || 'Error deleting item', 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error.message, 'error');
    })
    .finally(() => {
        deleteBtn.innerHTML = 'Delete';
        deleteBtn.disabled = false;
    });
}

// Helper function to show toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Helper function to refresh table data
function refreshTableData() {
    // Determine which table is active
    const activeTable = document.querySelector('.stock-in-table.active') ? 'in' : 'out';
    
    fetch(`refresh-table.php?type=${activeTable}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById(`stock${activeTable === 'in' ? 'In' : 'Out'}Table`).innerHTML = html;
        })
        .catch(error => {
            console.error('Error refreshing table:', error);
            // Fallback to full page reload if AJAX fails
            window.location.reload();
        });
}
        
        // Search functionality
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                this.form.submit();
            }
        });
    </script>
</body>
</html>