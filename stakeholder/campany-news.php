<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company News Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/news.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Company News";
        include 'main/sidebar.php'; 
        
        // Initialize filter variables
        $type_filter = isset($_GET['type']) ? $_GET['type'] : '';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $search_term = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Build base query
        $query = "SELECT * FROM announcements";
        $conditions = [];
        
        // Apply filters
        if (!empty($type_filter)) {
            $conditions[] = "type = '" . $conn->real_escape_string($type_filter) . "'";
        }
        
        if (!empty($start_date)) {
            $conditions[] = "created_at >= '" . $conn->real_escape_string($start_date) . "'";
        }
        
        if (!empty($end_date)) {
            $conditions[] = "created_at <= '" . $conn->real_escape_string($end_date) . " 23:59:59'";
        }
        
        if (!empty($search_term)) {
            $conditions[] = "(title LIKE '%" . $conn->real_escape_string($search_term) . "%' 
                      OR content LIKE '%" . $conn->real_escape_string($search_term) . "%')";
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY created_at DESC";
        
        // Execute query
        $result = $conn->query($query);
        
        // Get unique announcement types for dropdown
        $types_query = "SELECT DISTINCT type FROM announcements ORDER BY type";
        $types_result = $conn->query($types_query);
        $types = [];
        while ($row = $types_result->fetch_assoc()) {
            $types[] = $row['type'];
        }
        ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'main/header.php'; ?>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid-finance">
                <!-- Search and Filter Section -->
                <form method="GET" action="" class="search">
                    <select name="type">
                        <option value="">All Types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $type_filter == $type ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    
                    <div class="field">
                        <input type="search" name="search" placeholder="Search announcements" value="<?php echo htmlspecialchars($search_term); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                
                <!-- Announcements Table -->
                <div class="finance-table">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php $counter = 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="clickable-row" data-announcement-id="<?php echo $row['id']; ?>">
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['type'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td class="priority-<?php echo htmlspecialchars($row['location']); ?>">
                                            <?php echo htmlspecialchars(ucfirst($row['location'])); ?>
                                        </td>
                                        <td><?php echo date('m-d-Y', strtotime($row['created_at'])); ?></td>
                                        
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No announcements found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div id="announcementModal" class="email-modal">
        <div class="email-modal-content">
            <span class="close-modal">&times;</span>
            <div class="email-header">
                <div class="email-subject" id="announcementSubject"></div>
                <div class="email-info" id="announcementInfo"></div>
            </div>
            <div class="email-body" id="announcementBody"></div>
            <div class="email-actions">
                <button class="forward-btn">Forward</button>
            </div>
        </div>
    </div>

    <script>
        // Updated JavaScript section for the modal
document.addEventListener('DOMContentLoaded', function() {
    // Simulate loading time
    // const body = document.querySelector('body');
    // body.style.opacity = '0';
    
    // setTimeout(() => {
    //     body.style.transition = 'opacity 0.5s ease';
    //     body.style.opacity = '1';
    // }, 500);
    
    // Event listeners for clickable rows
    const clickableRows = document.querySelectorAll('.clickable-row');
    const announcementModal = document.getElementById('announcementModal');
    const closeModal = document.querySelector('.close-modal');
    const announcementSubject = document.getElementById('announcementSubject');
    const announcementInfo = document.getElementById('announcementInfo');
    const announcementBody = document.getElementById('announcementBody');
    
    let currentAnnouncementId = null;
    
    clickableRows.forEach(row => {
        row.addEventListener('click', function() {
            const announcementId = this.getAttribute('data-announcement-id');
            currentAnnouncementId = announcementId;
            
            // Show loading state
            announcementSubject.textContent = 'Loading...';
            announcementInfo.textContent = '';
            announcementBody.textContent = 'Loading announcement details...';
            announcementModal.style.display = 'block';
            
            // Fetch announcement details via AJAX
            fetch('get_announcement.php?id=' + announcementId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    // Populate modal with announcement data
                    announcementSubject.textContent = data.title || 'No Title';
                    
                    // Format the info section
                    const createdDate = new Date(data.created_at).toLocaleDateString();
                    const updatedDate = data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'N/A';
                    
                    // Check if there's a forward message or admin response
                    let responseStatusHtml = '';
                    if (data.has_forward) {
                        if (data.has_response && data.admin_response && data.admin_response.trim() !== '') {
                            const responseDate = data.admin_response_date ? new Date(data.admin_response_date).toLocaleDateString() : 'N/A';
                            responseStatusHtml = `<br><strong>Status:</strong> <span style="color: green;">Responded</span> | <strong>Response Date:</strong> ${responseDate}`;
                        } else {
                            responseStatusHtml = `<br><strong>Status:</strong> <span style="color: orange;">No Reply</span>`;
                        }
                    } else {
                        responseStatusHtml = `<br><strong>Status:</strong> <span style="color: gray;">Not Forwarded</span>`;
                    }
                    
                    announcementInfo.innerHTML = `
                        <strong>Type:</strong> ${data.type || 'N/A'} | 
                        <strong>Location:</strong> <span class="priority-${data.location || ''}">${data.location || 'N/A'}</span><br>
                        <strong>Created:</strong> ${createdDate} | 
                        <strong>Updated:</strong> ${updatedDate}
                        ${responseStatusHtml}
                    `;
                    
                    // Set content with proper HTML formatting
                    let bodyContent = data.content || 'No content available.';
                    
                    // Add forward message section if it exists
                    if (data.has_forward && data.user_comment) {
                        bodyContent += `
                            <div style="margin-top: 30px; padding: 15px; background-color: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px;">
                                <h4 style="color: #1976d2; margin: 0 0 10px 0;">
                                    <i class="fas fa-paper-plane"></i> Your Forward Message
                                </h4>
                                <div style="color: #333;">
                                    ${data.user_comment}
                                </div>
                                ${data.forward_date ? `
                                    <div style="margin-top: 10px; font-size: 0.9em; color: #6c757d;">
                                        <i class="fas fa-clock"></i> Sent on: ${new Date(data.forward_date).toLocaleString()}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }
                    
                    // Add admin response section if it exists
                    if (data.has_response && data.admin_response && data.admin_response.trim() !== '') {
                        bodyContent += `
                            <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #28a745; border-radius: 4px;">
                                <h4 style="color: #28a745; margin: 0 0 10px 0;">
                                    <i class="fas fa-reply"></i> Admin Response
                                </h4>
                                <div style="color: #333;">
                                    ${data.admin_response}
                                </div>
                                ${data.admin_response_date ? `
                                    <div style="margin-top: 10px; font-size: 0.9em; color: #6c757d;">
                                        <i class="fas fa-clock"></i> Responded on: ${new Date(data.admin_response_date).toLocaleString()}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    } else if (data.has_forward && !data.has_response) {
                        // Show "Waiting for response" message if forwarded but no response yet
                        bodyContent += `
                            <div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                <h4 style="color: #856404; margin: 0 0 10px 0;">
                                    <i class="fas fa-hourglass-half"></i> Admin Response
                                </h4>
                                <div style="color: #856404; font-style: italic;">
                                    Waiting for admin response...
                                </div>
                            </div>
                        `;
                    }
                    
                    announcementBody.innerHTML = bodyContent;
                })
                .catch(error => {
                    console.error('Error fetching announcement:', error);
                    announcementSubject.textContent = 'Error Loading Announcement';
                    announcementInfo.textContent = '';
                    announcementBody.textContent = 'Could not load announcement details: ' + error.message;
                });
        });
    });
    
    // Close modal when clicking the close button
    closeModal.addEventListener('click', function() {
        announcementModal.style.display = 'none';
        currentAnnouncementId = null;
    });
    
    // Close modal when clicking outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target === announcementModal) {
            announcementModal.style.display = 'none';
            currentAnnouncementId = null;
        }
    });
    
    document.querySelector('.forward-btn').addEventListener('click', function() {
        if (!currentAnnouncementId) {
            alert('No announcement selected');
            return;
        }
        window.location.href = 'forward_announcement.php?id=' + currentAnnouncementId;
    });
});
    </script>
</body>
</html>