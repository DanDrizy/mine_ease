<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "SELECT * FROM stock_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        echo '<div class="form-group">';
        echo '<label>Item Name:</label>';
        echo '<p>' . htmlspecialchars($item['item_name']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Category:</label>';
        echo '<p>' . htmlspecialchars($item['category']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Quantity:</label>';
        echo '<p>' . number_format($item['quantity']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Unit Price:</label>';
        echo '<p>Frw' . number_format($item['unit_price'], 2) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Total Value:</label>';
        echo '<p>Frw' . number_format($item['total_value'], 2) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Location:</label>';
        echo '<p>' . htmlspecialchars($item['location']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Status:</label>';
        echo '<p>' . htmlspecialchars($item['status']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Date Added:</label>';
        echo '<p>' . date('Y-m-d H:i:s', strtotime($item['created_at'])) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Last Updated:</label>';
        echo '<p>' . date('Y-m-d H:i:s', strtotime($item['updated_at'])) . '</p>';
        echo '</div>';
    } else {
        echo '<p>Item not found.</p>';
    }
} else {
    echo '<p>No item ID specified.</p>';
}
?>