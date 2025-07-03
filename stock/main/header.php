<?php
error_reporting(0);
// Get notifications from database
$notificationCount = 0;
$notifications = [];

// Query to get latest notifications
if (isset($conn)) {
    $notifQuery = "SELECT id, content FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5";
    $notifResult = $conn->query($notifQuery);
    
    if ($notifResult && $notifResult->num_rows > 0) {
        $notificationCount = $notifResult->num_rows;
        while ($row = $notifResult->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
}
?>

<div class="header">
    <h1>Stock</h1>
    
    <div class="header-icons">
        <div class="bell" onclick="toggleNotifications()">
            <span id="show-up">🔔</span>
            <div class="badge"><?php echo $notificationCount; ?></div>
            <div class="notifications" id="notifications">
                <ul>
                    <?php 
                    if (count($notifications) > 0) {
                        foreach ($notifications as $notification) {
                            echo '<li>
                                <div class="notification-content">📢 ' . $notification['content'] . '</div>
                                <button class="mark-read-btn" data-id="' . $notification['id'] . '">Mark as read</button>
                            </li>';
                        }
                    } else {
                        echo "<li>No new notifications</li>";
                    }
                    ?>
                </ul>
                <?php if (count($notifications) > 0): ?>
                <div class="mark-all-read">
                    <button id="mark-all-read-btn">Mark all as read</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-icon">
            <a href="../log/logout.php" style="text-decoration: none;">
                <svg fill="CurrentColor" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title>logout</title>
                        <path d="M0 9.875v12.219c0 1.125 0.469 2.125 1.219 2.906 0.75 0.75 1.719 1.156 2.844 1.156h6.125v-2.531h-6.125c-0.844 0-1.5-0.688-1.5-1.531v-12.219c0-0.844 0.656-1.5 1.5-1.5h6.125v-2.563h-6.125c-1.125 0-2.094 0.438-2.844 1.188-0.75 0.781-1.219 1.75-1.219 2.875zM6.719 13.563v4.875c0 0.563 0.5 1.031 1.063 1.031h5.656v3.844c0 0.344 0.188 0.625 0.5 0.781 0.125 0.031 0.25 0.031 0.313 0.031 0.219 0 0.406-0.063 0.563-0.219l7.344-7.344c0.344-0.281 0.313-0.844 0-1.156l-7.344-7.313c-0.438-0.469-1.375-0.188-1.375 0.563v3.875h-5.656c-0.563 0-1.063 0.469-1.063 1.031z"></path>
                    </g>
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.header h1 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

.header-icons {
    display: flex;
    align-items: center;
}

.bell {
    position: relative;
    cursor: pointer;
    margin-right: 25px;
    font-size: 20px;
}

.badge {
    position: absolute;
    top: -5px;
    right: -10px;
    background-color: #e74c3c;
    color: white;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 50%;
    display: <?php echo $notificationCount > 0 ? 'block' : 'none'; ?>;
}

.notifications {
    position: absolute;
    top: 30px;
    right: 0;
    width: 300px;
    background-color: #fff;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    display: none;
    z-index: 100;
}

.notifications ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.notifications ul li {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-content {
    flex: 1;
    padding-right: 10px;
}

.mark-read-btn {
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.mark-read-btn:hover {
    background-color: #e0e0e0;
}

.notifications ul li:last-child {
    border-bottom: none;
}

.mark-all-read {
    text-align: center;
    padding: 10px;
    border-top: 1px solid #eee;
}

#mark-all-read-btn {
    background-color: #5dac4a;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 12px;
    font-size: 13px;
    cursor: pointer;
    transition: background-color 0.2s;
}

#mark-all-read-btn:hover {
    background-color: #4c9039;
}

.profile-icon {
    cursor: pointer;
    width: 24px;
    height: 24px;
}

.profile-icon svg {
    width: 100%;
    height: 100%;
    color: #333;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up event listeners when the page loads
    setupNotificationListeners();
});

function setupNotificationListeners() {
    // Mark individual notification as read
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        // Remove any existing event listeners first to prevent duplicates
        button.removeEventListener('click', markReadHandler);
        // Add the event listener
        button.addEventListener('click', markReadHandler);
    });

    // Mark all notifications as read
    const markAllBtn = document.getElementById('mark-all-read-btn');
    if (markAllBtn) {
        markAllBtn.removeEventListener('click', markAllReadHandler);
        markAllBtn.addEventListener('click', markAllReadHandler);
    }
}

function markReadHandler(e) {
    e.stopPropagation(); // Prevent the click from propagating
    const notificationId = this.getAttribute('data-id');
    markAsRead(notificationId, this.closest('li'));
}

function markAllReadHandler(e) {
    e.stopPropagation(); // Prevent the click from propagating
    markAllAsRead();
}

function toggleNotifications() {
    const box = document.getElementById('notifications');
    box.style.display = (box.style.display === 'block') ? 'none' : 'block';
}

// Close when clicking outside
document.addEventListener('click', function(e) {
    const bell = document.querySelector('.bell');
    const box = document.getElementById('notifications');
    if (!bell.contains(e.target)) {
        box.style.display = 'none';
    }
});

function markAsRead(notificationId, listItem) {
    // Send AJAX request to mark as read
    const xhr = new XMLHttpRequest();
    // Make sure this path is correct - adjust according to your file structure
    xhr.open('POST', 'mark_notification_read.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            console.log('Response:', this.responseText); // Debug response
            
            if (this.responseText.trim() === 'success') {
                // Remove the notification from the list
                listItem.remove();
                
                // Update notification count
                updateNotificationCount();
            } else {
                console.error('Error marking notification as read:', this.responseText);
            }
        }
    };
    xhr.send('id=' + notificationId);
}

// Function to mark all notifications as read
function markAllAsRead() {
    // Send AJAX request to mark all as read
    const xhr = new XMLHttpRequest();
    // Make sure this path is correct - adjust according to your file structure
    xhr.open('POST', 'mark_all_notifications_read.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            console.log('Response:', this.responseText); // Debug response
            
            if (this.responseText.trim() === 'success') {
                // Clear all notifications and update UI
                document.getElementById('notifications').innerHTML = '<ul><li>No new notifications</li></ul>';
                document.querySelector('.badge').style.display = 'none';
            } else {
                console.error('Error marking all notifications as read:', this.responseText);
            }
        }
    };
    xhr.send();
}

// Update notification count in badge
function updateNotificationCount() {
    const badge = document.querySelector('.badge');
    const notificationItems = document.querySelectorAll('.notifications .mark-read-btn');
    const count = notificationItems.length;
    
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
        document.querySelector('.notifications ul').innerHTML = '<li>No new notifications</li>';
        
        // Remove the "Mark all as read" button if it exists
        const markAllSection = document.querySelector('.mark-all-read');
        if (markAllSection) {
            markAllSection.remove();
        }
    }
}
</script>