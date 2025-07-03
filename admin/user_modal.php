<?php
// user_modal.php - Handles the modal content for user operations

// Database connection
require_once '../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validate request
$action = isset($_GET['action']) ? $_GET['action'] : '';
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userType = isset($_GET['type']) ? $_GET['type'] : '';

// Validate inputs
if (!in_array($action, ['view', 'edit']) || $userId <= 0 || !in_array($userType, ['admin', 'stakeholder', 'hr', 'employee'])) {
    echo '<div class="modal-overlay active">
            <div class="modal-content">
                <h2>Error</h2>
                <p>Invalid request parameters.</p>
                <button class="modal-close">&times;</button>
            </div>
          </div>';
    exit;
}

// Get user data
function getUserData($conn, $userId, $userType) {
    if ($userType === 'hr') {
        $sql = "SELECT u.*, h.specialization 
                FROM users u 
                LEFT JOIN hr_details h ON u.id = h.user_id 
                WHERE u.id = ? AND u.user_type = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $userType);
    } else {
        $sql = "SELECT * FROM users WHERE id = ? AND user_type = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $userType);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    $userData = $result->fetch_assoc();
    $stmt->close();
    
    return $userData;
}

$userData = getUserData($conn, $userId, $userType);

if (!$userData) {
    echo '<div class="modal-overlay active">
            <div class="modal-content">
                <h2>Error</h2>
                <p>User not found.</p>
                <button class="modal-close">&times;</button>
            </div>
          </div>';
    exit;
}

// Generate modal content based on action
if ($action === 'view') {
    // View user modal
    ?>
    <div class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            
            <div class="modal-header">
                <h2 class="modal-title"><?php echo htmlspecialchars($userData['name']); ?> (<?php echo ucfirst($userType); ?>)</h2>
            </div>
            
            <div class="modal-body">
                <table class="user-details-table">
                    <tr>
                        <th>Name</th>
                        <td><?php echo htmlspecialchars($userData['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($userData['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Site</th>
                        <td><?php echo htmlspecialchars($userData['site']); ?></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td><?php echo htmlspecialchars($userData['department']); ?></td>
                    </tr>
                    <tr>
                        <th>Registration Date</th>
                        <td><?php echo htmlspecialchars($userData['registration_date']); ?></td>
                    </tr>
                    <?php if ($userType === 'hr' && isset($userData['specialization'])): ?>
                    <tr>
                        <th>HR Specialization</th>
                        <td><?php echo htmlspecialchars($userData['specialization']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <div class="modal-actions">
                <button class="btn-edit" data-id="<?php echo $userId; ?>" data-type="<?php echo $userType; ?>">Edit</button>
            </div>
        </div>
    </div>
    <?php
} else if ($action === 'edit') {
    // Edit user modal
    ?>
    <div class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            
            <div class="modal-header">
                <h2 class="modal-title">Edit <?php echo ucfirst($userType); ?></h2>
            </div>
            
            <div class="modal-body">
                <form class="modal-form" id="edit-user-form">
                    <input type="hidden" name="id" value="<?php echo $userId; ?>">
                    <input type="hidden" name="user_type" value="<?php echo $userType; ?>">
                    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site">Site</label>
                        <input type="text" id="site" name="site" value="<?php echo htmlspecialchars($userData['site']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($userData['department']); ?>" required>
                    </div>
                    
                    <?php if ($userType === 'hr'): ?>
                    <div class="form-group">
                        <label for="specialization">HR Specialization</label>
                        <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($userData['specialization'] ?? ''); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="password">Password (leave blank to keep current)</label>
                        <input type="password" id="password" name="password">
                    </div>
                </form>
            </div>
            
            <div class="modal-actions">
                <button class="btn-submit" form="edit-user-form">Save Changes</button>
            </div>
        </div>
    </div>
    <?php
}
?>