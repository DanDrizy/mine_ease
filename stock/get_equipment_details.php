<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "SELECT * FROM equipment
            WHERE id = ?";
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
        echo '<label>Description:</label>';
        echo '<p>' . nl2br(htmlspecialchars($item['description'])) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Serial Number:</label>';
        echo '<p>' . htmlspecialchars($item['serial_number']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Location:</label>';
        echo '<p>' . htmlspecialchars($item['location']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Department:</label>';
        echo '<p>' . htmlspecialchars($item['department_id']) . '</p>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Date Added:</label>';
        echo '<p>' . date('F j, Y', strtotime($item['date_added'])) . '</p>';
        echo '</div>';
        
        if (!empty($item['notes'])) {
            echo '<div class="form-group">';
            echo '<label>Notes:</label>';
            echo '<p>' . nl2br(htmlspecialchars($item['notes'])) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>Equipment not found.</p>';
    }
} else {
    echo '<p>No equipment ID specified.</p>';
}
?>