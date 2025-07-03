<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $requestId = (int)$_GET['id'];
    
    $sql = "SELECT r.*, u.username 
            FROM mining_requests r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();
        
        echo '<div style="margin-bottom: 15px;">';
        echo '<h4 style="margin: 0 0 5px 0; color: #4768A8;">Request Details</h4>';
        echo '<p style="white-space: pre-wrap; background: #f8f9fa; padding: 10px; border-radius: 5px;">';
        echo htmlspecialchars($request['request_text']);
        echo '</p>';
        echo '</div>';
        
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        echo '<div>';
        echo '<h4 style="margin: 0 0 5px 0; color: #4768A8;">Submitted By</h4>';
        echo '<p>' . htmlspecialchars($request['username'] ?? 'Unknown') . '</p>';
        echo '</div>';
        
        echo '<div>';
        echo '<h4 style="margin: 0 0 5px 0; color: #4768A8;">Date Submitted</h4>';
        echo '<p>' . date('F j, Y H:i', strtotime($request['request_date'])) . '</p>';
        echo '</div>';
        
        echo '<div>';
        echo '<h4 style="margin: 0 0 5px 0; color: #4768A8;">Status</h4>';
        echo '<span class="status status-' . strtolower($request['status']) . '">';
        echo htmlspecialchars($request['status']);
        echo '</span>';
        echo '</div>';
        
        if (!empty($request['response_date'])) {
            echo '<div>';
            echo '<h4 style="margin: 0 0 5px 0; color: #4768A8;">Response Date</h4>';
            echo '<p>' . date('F j, Y H:i', strtotime($request['response_date'])) . '</p>';
            echo '</div>';
        }
        echo '</div>';
        
        if (!empty($request['response'])) {
            echo '<div>';
            echo '<h4 style="margin: 0 0 5px 0; color: #4768A8;">Response</h4>';
            echo '<p style="white-space: pre-wrap; background: #f8f9fa; padding: 10px; border-radius: 5px;">';
            echo htmlspecialchars($request['response']);
            echo '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>Request not found.</p>';
    }
} else {
    echo '<p>No request ID specified.</p>';
}
?>