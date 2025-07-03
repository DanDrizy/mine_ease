<?php
// error_reporting(0);
// Database connection
include_once '../config.php';

$i = 1;
$sum = 0;

include'backend/inventory.php';

// Handle form submissions

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/inventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="stock-dashboard">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-boxes"></i> Stock Management</h1>
            <p>Manage your inventory with ease</p>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-boxes in-stock"></i>
                <h3><?php echo $count_stock_in ? $count_stock_in : 0; ?></h3>
                <p>In Stock</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-times-circle out-stock"></i>
                <h3><?php echo $count_stock_out ? $count_stock_out : 0; ?></h3>
                <p>Out of Stock</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign total-value"></i>
                <h3><?php echo number_format($sum_stockout, 0); ?> Rwf</h3>
                <p>Total Sold</p>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="controls-section">
            <form method="GET" action="">
                <div class="search-filters">
                    <input type="search" name="search_term" value="<?php echo isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : ''; ?>" placeholder="Search items, categories, or suppliers...">
                    
                    <select name="category_filter" onchange="this.form.submit()">
                        <option value="Stockin" <?php echo ($category_filter == 'Stockin') ? 'selected' : ''; ?>>Stock In Values</option>
                        <option value="Stockout" <?php echo ($category_filter == 'Stockout') ? 'selected' : ''; ?>>Stock Out Values</option>
                    </select>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="search" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-success" onclick="openModal('addTypeModal')">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Stock Table -->
        <div class="stock-table">
            <?php if (mysqli_num_rows($sele_items) > 0):
                
                if($table_name == 'stockout'): ?>
                <table>
                    <thead>
                        <tr><td colspan="7"><?php echo $table_title; ?></td></tr>
                        <tr>
                            <th>No</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reset the result pointer
                        mysqli_data_seek($sele_items, 0);
                        while($row = mysqli_fetch_array($sele_items)): ?>
                        <tr>
                            <td><?php echo $i; $i++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['item_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <span style="font-weight: bold; font-size: 1.1em;"><?php echo $row['quantity_out']; ?></span>
                            </td>
                            <td><?php echo number_format($row['unit_price'], 0); ?> Rwf</td>
                            <td><?php echo number_format($row['quantity_out'] * $row['unit_price'], 0); ?> Rwf</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn btn-warning" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>, '<?php echo $table_name; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="action-btn btn-danger" onclick="openDeleteModal(<?php echo $row['id']; ?>, '<?php echo $table_name; ?>', '<?php echo htmlspecialchars($row['item_name']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php elseif( $table_name == 'stock_items'): ?>
                    <table>
                    <thead>
                        <tr><td colspan="7"><?php echo $table_title; ?></td></tr>
                        <tr>
                            <th>No</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reset the result pointer
                        mysqli_data_seek($sele_items, 0);
                        while($row = mysqli_fetch_array($sele_items)): ?>
                        <tr>
                            <td><?php echo $i; $i++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['item_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <span style="font-weight: bold; font-size: 1.1em;"><?php echo $row['quantity']; ?></span>
                            </td>
                            <td><?php echo number_format($row['unit_price'], 0); ?> Rwf</td>
                            <td><?php echo number_format($row['quantity'] * $row['unit_price'], 0); ?> Rwf</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn btn-warning" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>, '<?php echo $table_name; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="action-btn btn-danger" onclick="openDeleteModal(<?php echo $row['id']; ?>, '<?php echo $table_name; ?>', '<?php echo htmlspecialchars($row['item_name']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
                
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #666;">
                    <i class="fas fa-box-open" style="font-size: 3em; margin-bottom: 20px;"></i>
                    <h3>No items found</h3>
                    <p>Start by adding your first stock item</p>
                    <br>
                    <button type="button" class="btn btn-success" onclick="openModal('addTypeModal')">
                        <i class="fas fa-plus"></i> Add First Item
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Type Selection Modal -->
    <div id="addTypeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addTypeModal')">&times;</span>
            <h2>Select Item Type</h2>
            <p>Choose whether you want to add a Stock In or Stock Out item:</p>
            <div class="type-selection">
                <div class="type-card stockin" onclick="redirectToAddPage('stockin')">
                    <i class="fas fa-plus-circle" style="color: #28a745;"></i>
                    <h3>Stock In</h3>
                    <p>Add new inventory</p>
                </div>
                <div class="type-card stockout" onclick="redirectToAddPage('stockout')">
                    <i class="fas fa-minus-circle" style="color: #dc3545;"></i>
                    <h3>Stock Out</h3>
                    <p>Record item sale</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Item</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="in_id" id="in_id">
                <input type="hidden" name="table" id="edit_table">
                
                <div class="form-group">
                    <label for="edit_item_name">Item Name:</label>
                    <input type="text" name="item_name" id="edit_item_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Category:</label>
                    <input type="text" name="category" id="edit_category" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_quantity">Stockin Quantity:</label>
                    <input type="number" name="quantity" id="edit_quantity" required min="0">
                </div>
                <div class="form-group">
                    <label for="edit_quantity">Stockout Quantity:</label>
                    <input type="number" name="quantity_out" id="edit_quantity_out" required min="0">
                </div>
                
                <div class="form-group">
                    <label for="edit_unit_price">Unit Price (Rwf):</label>
                    <input type="number" name="unit_price" id="edit_unit_price" required min="0" step="0.01">
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="update_item" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Item
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete "<span id="delete_item_name"></span>"?</p>
            <p style="color: #dc3545; font-weight: bold;">This action cannot be undone!</p>
            
            <form method="POST" id="deleteForm">
                <input type="hidden" name="id" id="delete_id">
                <input type="hidden" name="table" id="delete_table">
                
                <div class="btn-group">
                    <button type="submit" name="delete_item" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Yes, Delete
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script></script>

</body>
<script src="js/inventory.js"></script>
</html>

<?php
mysqli_close($conn);
?>