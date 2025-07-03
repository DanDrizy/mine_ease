<?php
// Include database functions
include '../config.php';

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Helper function to format date
function formatDate($dateString) {
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
    
    $query = "SELECT * FROM announcements WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (title LIKE ? OR content LIKE ?)";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if (!empty($type)) {
        $query .= " AND type = ?";
        $params[] = $type;
    }
    
    $query .= " ORDER BY created_at DESC, created_at DESC LIMIT ? OFFSET ?";
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
    
    $query = "SELECT COUNT(*) as total FROM announcements WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (title LIKE ? OR content LIKE ?)";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if (!empty($type)) {
        $query .= " AND type = ?";
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
    <title>Employee Announcements | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/home-announce.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style></style>
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1>Updates Announcements</h1>
            <a href="home.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        <br>
        <br>
        
        <div class="filter-container">
            <div class="filter-box">
                
                
                <form action="" method="GET" style="display: flex; gap: 10px; width: 200px;  ">
                    <!-- Preserve search param when changing type -->
                    <?php if (!empty($search)): ?>
                    <input type="hidden"  name="search" value="<?php echo htmlspecialchars($search); ?>" style="" >
                    <?php endif; ?>
                    
                    <select name="type" class="filter-select" onchange="this.form.submit()" style=" width: 200px; " >
                        <option value="">All Types</option>
                        <option value="All" <?php echo ($type == 'General') ? 'selected' : ''; ?>>General</option>
                        <option value="admin" <?php echo ($type == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="stock" <?php echo ($type == 'stock') ? 'selected' : ''; ?>>Stock</option>
                        <option value="stake" <?php echo ($type == 'stake') ? 'selected' : ''; ?>>Stakeholder</option>
                        <option value="emp" <?php echo ($type == 'emp') ? 'selected' : ''; ?>>Employee</option>
                    </select>
                </form>
                <a href="add_announcement.php" >
                    <button class="filter-select" style="background: #304c8e; color:#fff; border: none; padding: 10px; cursor: pointer; ">
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
                        <div class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></div>
                        <div class="announcement-date"><?php echo formatDate($announcement['created_at']); ?></div>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                    </div>
                    <div class="announcement-footer">
                        <div class="announcement-category"><?php
                        
                        if( $announcement['type'] == 'hr') {
                            $priorityClass = 'Human Resources';
                        } else if( $announcement['type'] == 'admin') {
                            $priorityClass = 'Admin';
                        } else if( $announcement['type'] == 'stake') {
                            $priorityClass = 'Stakeholder';
                        } else if( $announcement['type'] == 'emp') {
                            $priorityClass = 'Employee';
                        } else if( $announcement['type'] == 'All') {
                            $priorityClass = 'All Users';
                        }else if( $announcement['type'] == 'stock'){
                            $priorityClass = 'Stock Manager';

                        }
                        echo htmlspecialchars($priorityClass); ?></div>
                        <div class="announcement-actions">
                            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $announcement['id']; ?>)"><i class="far fa-trash-alt"></i> Delete</a>
                            <?php endif; ?>
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