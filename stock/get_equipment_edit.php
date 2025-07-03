<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get equipment details
    $sql = "SELECT * FROM equipment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get departments
    $dept_sql = "SELECT department FROM users";
    $dept_result = $conn->query($dept_sql);
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        echo '<form id="editEquipmentForm">';
        echo '<input type="hidden" name="id" value="' . $id . '">';
        
        echo '<div class="form-group">';
        echo '<label for="item_name">Item Name</label>';
        echo '<input type="text" class="form-control" id="item_name" name="item_name" value="' . htmlspecialchars($item['item_name']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="description">Description</label>';
        echo '<textarea class="form-control" id="description" name="description" rows="3">' . htmlspecialchars($item['description']) . '</textarea>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="serial_number">Serial Number</label>';
        echo '<input type="text" class="form-control" id="serial_number" name="serial_number" value="' . htmlspecialchars($item['serial_number']) . '">';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="location">Location</label>';
        echo '<input type="text" class="form-control" id="location" name="location" value="' . htmlspecialchars($item['location']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="department_id">Department</label>';
        echo '<select class="form-control" id="department_id" name="department_id" required>';
        if ($dept_result && $dept_result->num_rows > 0) {
            while($dept = $dept_result->fetch_assoc()) {
                $selected = $dept['id'] == $item['department_id'] ? ' selected' : '';
                echo '<option value="' . $dept['department'] . '"' . $selected . '>' . htmlspecialchars($dept['department']) . '</option>';
            }
        }
        echo '</select>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="notes">Notes</label>';
        echo '<textarea class="form-control" id="notes" name="notes" rows="2">' . htmlspecialchars($item['notes']) . '</textarea>';
        echo '</div>';
        
        echo '</form>';
    } else {
        echo '<p>Equipment not found.</p>';
    }
} else {
    echo '<p>No equipment ID specified.</p>';
}
?>