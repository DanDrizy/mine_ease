<?php
require_once '../config.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    
    $sql = "SELECT * FROM stock_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        echo '<form id="editForm" method="post">';
        echo '<input type="hidden" name="id" value="' . $id . '">';
        echo '<input type="hidden" name="type" value="' . $type . '">';
        
        echo '<div class="form-group">';
        echo '<label for="item_name">Item Name</label>';
        echo '<input type="text" class="form-control" id="item_name" name="item_name" value="' . htmlspecialchars($item['item_name']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="category">Category</label>';
        echo '<input type="text" class="form-control" id="category" name="category" value="' . htmlspecialchars($item['category']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="quantity">Quantity</label>';
        echo '<input type="number" class="form-control" id="quantity" name="quantity" value="' . $item['quantity'] . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="unit_price">Unit Price</label>';
        echo '<input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="' . $item['unit_price'] . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="location">Location</label>';
        echo '<input type="text" class="form-control" id="location" name="location" value="' . htmlspecialchars($item['location']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="status">Status</label>';
        echo '<select class="form-control" id="status" name="status" required>';
        echo '<option value="in-stock"' . ($item['status'] == 'in-stock' ? ' selected' : '') . '>In Stock</option>';
        echo '<option value="out-of-stock"' . ($item['status'] == 'out-of-stock' ? ' selected' : '') . '>Out of Stock</option>';
        echo '</select>';
        echo '</div>';
        
        echo '</form>';
    } else {
        echo '<p>Item not found.</p>';
    }
} else {
    echo '<p>No item ID or type specified.</p>';
}
?>