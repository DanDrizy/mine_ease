<?php
require_once '../config.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'in';
$status = $type === 'in' ? 'in-stock' : 'out-of-stock';

$sql = "SELECT id, item_name, category, quantity, unit_price, total_value, location, created_at 
        FROM stock_items 
        WHERE status = ?
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $status);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<h3 class="table-title">Stock-' . ucfirst($type) . ' Items</h3>';
    echo '<table>';
    echo '<tr>
            <th>Item Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total Value</th>
            <th>Location</th>
            <th>Date Added</th>
            <th>Action</th>
          </tr>';
    
    while($item = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($item['item_name']) . '</td>
                <td>' . htmlspecialchars($item['category']) . '</td>
                <td>' . number_format($item['quantity']) . '</td>
                <td>$' . number_format($item['unit_price'], 2) . '</td>
                <td>$' . number_format($item['total_value'], 2) . '</td>
                <td>' . htmlspecialchars($item['location']) . '</td>
                <td>' . date('Y-m-d', strtotime($item['created_at'])) . '</td>
                <td>
                    <button class="action-btn view-btn" onclick="showViewModal(' . $item['id'] . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit-btn" onclick="showEditModal(' . $item['id'] . ', \'' . $type . '\')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete-btn" onclick="showDeleteModal(' . $item['id'] . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
              </tr>';
    }
    echo '</table>';
} else {
    echo '<p>No stock-' . $type . ' items found</p>';
}
?>