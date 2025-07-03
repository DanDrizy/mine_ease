<?php
// Include database functions
include '../config.php';

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper function to format date
function formatDate($dateString) {
    if (empty($dateString) || $dateString == '0000-00-00 00:00:00') {
        return 'Not replied yet';
    }
    $date = new DateTime($dateString);
    return $date->format('F j, Y \a\t g:i a');
}

// Pagination settings
$resultsPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;

// Get search parameters with proper sanitization
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';
$type = isset($_GET['type']) ? htmlspecialchars($_GET['type'], ENT_QUOTES, 'UTF-8') : '';

// Function to get announcements based on filters
function getAnnouncements($search, $type, $limit, $offset) {
    global $conn; // Assuming $conn is defined in config.php
    
    $query = "SELECT * FROM announcement_forwards WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (forward_type LIKE ? OR user_comment LIKE ?)";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if (!empty($type)) {
        $query .= " AND priority = ?";
        $params[] = $type;
    }
    
    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($query);
    
    // Bind parameters dynamically
    if (!empty($params)) {
        $types = str_repeat('s', count($params) - 2) . 'ii'; // String params + limit and offset as integers
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $announcements = [];
    
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
    
    return $announcements;
}

// Count total announcements for pagination
function countAnnouncements($search, $type) {
    global $conn;
    
    $query = "SELECT COUNT(*) as total FROM announcement_forwards WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (forward_type LIKE ? OR user_comment LIKE ?)";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if (!empty($type)) {
        $query .= " AND priority = ?";
        $params[] = $type;
    }
    
    $stmt = $conn->prepare($query);
    
    // Bind parameters dynamically
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

// Get announcements based on filters with pagination
$announcements = getAnnouncements($search, $type, $resultsPerPage, $offset);

// Get total count for pagination
$totalAnnouncements = countAnnouncements($search, $type);
$totalPages = ceil($totalAnnouncements / $resultsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Stakeholder Reply | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/home-announce.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reply-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            margin-left: 10px;
        }
        .reply-button:hover {
            background: #218838;
        }
        .replied-status {
            background: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
        .admin-response {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .admin-response-header {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1>Stakeholder Message</h1>
            <a href="home.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        <br>
        <br>
        
        <div class="filter-container">
            <div class="filter-box">
                
                <form action="" method="GET" style="display: flex; gap: 10px; width: 200px;">
                    <!-- Preserve search param when changing type -->
                    <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>" style="">
                    <?php endif; ?>
                    
                    <select name="type" class="filter-select" onchange="this.form.submit()" style="width: 200px;">
                        <option value="">All Types</option>
                        <option value="high" <?php echo ($type == 'high') ? 'selected' : ''; ?>>High</option>
                        <option value="medium" <?php echo ($type == 'medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="low" <?php echo ($type == 'low') ? 'selected' : ''; ?>>Low</option>
                    </select>
                </form>
                <a href="add_announcement.php">
                    <button class="filter-select" style="background: #304c8e; color:#fff; border: none; padding: 10px; cursor: pointer;">
                        <i class="fas fa-plus"></i> Add New
                    </button>
                </a>
            </div>
            
            <div class="search-box">
                <form action="" method="GET" style="display: flex;">
                    <!-- Preserve type param when searching -->
                    <?php if (!empty($type)): ?>
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                    <?php endif; ?>
                    
                    <input type="text" name="search" class="search-input" placeholder="Search announcements..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
        
        <?php if (empty($announcements)): ?>
            <div class="no-announcements">
                <p>No announcements found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($announcements as $announcement): ?>
                
                <div class="announcement-container">
                    <div class="announcement-header">
                        <div class="announcement-title"><?php echo htmlspecialchars($announcement['forward_type']); ?></div>
                        <div class="announcement-date"><?php echo formatDate($announcement['created_at']); ?></div>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($announcement['user_comment'])); ?>
                    </div>
                    
                    <!-- Display admin response if exists -->
                    <?php if (!empty($announcement['admin_response'])): ?>
                    <div class="admin-response">
                        <div class="admin-response-header">
                            <i class="fas fa-reply"></i> Admin Response - <?php echo formatDate($announcement['admin_response_date']); ?>
                        </div>
                        <div><?php echo nl2br(htmlspecialchars($announcement['admin_response'])); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="announcement-footer">
                        <div class="announcement-category"><?php echo htmlspecialchars($announcement['priority']); ?></div>
                        <div class="announcement-actions">
                            <?php if (empty($announcement['admin_response'])): ?>
                                <a href="reply_announcement.php?id=<?php echo $announcement['id']; ?>" class="reply-button">
                                    <i class="fas fa-reply"></i> Reply
                                </a>
                            <?php else: ?>
                                <span class="replied-status">
                                    <i class="fas fa-check"></i> Replied
                                </span>
                            <?php endif; ?>
                            
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $announcement['id']; ?>)">
                                <i class="far fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Dynamic pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?><?php echo (!empty($search) ? '&search=' . urlencode($search) : ''); ?><?php echo (!empty($type) ? '&type=' . urlencode($type) : ''); ?>">Previous</a>
                <?php endif; ?>
                
                <?php
                // Calculate range of pages to show
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                // Always show first page
                if ($startPage > 1) {
                    echo '<a href="?page=1' . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($type) ? '&type=' . urlencode($type) : '') . '">1</a>';
                    if ($startPage > 2) {
                        echo '<span>...</span>';
                    }
                }
                
                // Show page links
                for ($i = $startPage; $i <= $endPage; $i++) {
                    $activeClass = ($i == $page) ? 'active' : '';
                    echo '<a href="?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($type) ? '&type=' . urlencode($type) : '') . '" class="' . $activeClass . '">' . $i . '</a>';
                }
                
                // Always show last page
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) {
                        echo '<span>...</span>';
                    }
                    echo '<a href="?page=' . $totalPages . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($type) ? '&type=' . urlencode($type) : '') . '">' . $totalPages . '</a>';
                }
                ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo ($page + 1); ?><?php echo (!empty($search) ? '&search=' . urlencode($search) : ''); ?><?php echo (!empty($type) ? '&type=' . urlencode($type) : ''); ?>">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this announcement?')) {
                window.location.href = 'delete_announcement.php?id=' + id;
            }
        }
        
        // Add active class to the current type filter
        document.addEventListener('DOMContentLoaded', function() {
            const currentType = '<?php echo $type; ?>';
            if (currentType) {
                const typeOptions = document.querySelectorAll('.filter-select option');
                typeOptions.forEach(option => {
                    if (option.value === currentType) {
                        option.selected = true;
                    }
                });
            }
        });
    </script>
</body>
</html>