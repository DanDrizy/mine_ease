<?php
// users.php - Main user management page

// Database connection
require_once '../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get users by type
// function getUsersByType($conn, $userType) {
//     $sql = "SELECT * FROM users WHERE user_type = ?";

//     // Add special column for HR users
//     if ($userType === 'hr') {
//         $sql = "SELECT u.*, h.specialization 
//                 FROM users u 
//                 LEFT JOIN hr_details h ON u.id = h.user_id 
//                 WHERE u.user_type = ?";
//     }

//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $userType);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $users = array();
//     while ($row = $result->fetch_assoc()) {
//         $users[] = $row;
//     }

//     $stmt->close();
//     return $users;
// }

// Modified getUsersByType function to filter out incomplete records
function getUsersByType($conn, $userType)
{
    // Base SQL with condition to exclude empty names
    $sql = "SELECT * FROM users WHERE user_type = ? AND name IS NOT NULL AND name != ''";

    // Add special column for HR users
    if ($userType === 'hr') {
        $sql = "SELECT u.*, h.specialization 
                FROM users u 
                LEFT JOIN hr_details h ON u.id = h.user_id 
                WHERE u.user_type = ? AND u.name IS NOT NULL AND u.name != ''";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userType);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $stmt->close();
    return $users;
}

// Handle search functionality
function searchUsers($conn, $userType, $searchTerm)
{
    if ($userType === 'hr') {
        $sql = "SELECT u.*, h.specialization 
                FROM users u 
                LEFT JOIN hr_details h ON u.id = h.user_id 
                WHERE u.user_type = ? AND 
                (u.name LIKE ? OR u.site LIKE ? OR u.department LIKE ? OR h.specialization LIKE ?)";

        $searchPattern = "%$searchTerm%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $userType, $searchPattern, $searchPattern, $searchPattern, $searchPattern);
    } else {
        $sql = "SELECT * FROM users 
                WHERE user_type = ? AND 
                (name LIKE ? OR site LIKE ? OR department LIKE ?)";

        $searchPattern = "%$searchTerm%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $userType, $searchPattern, $searchPattern, $searchPattern);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $stmt->close();
    return $users;
}

// Check if search was performed
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$currentUserType = isset($_GET['user_type']) ? $_GET['user_type'] : 'admin';

// Get users based on search or default view
if (!empty($searchTerm)) {
    $users = searchUsers($conn, $currentUserType, $searchTerm);
} else {
    $users = getUsersByType($conn, $currentUserType);
}
$i = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/user.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style></style>
</head>

<body><?php
        include 'main/slidebar.php';
        ?>

    <div class="main-content">

        <?php include 'main/header.php'; ?>
        <div class="user-grid">
            <div class="user-records">
                <h2>User Records</h2>

                <!-- User Type Selection Buttons -->
                <div class="user-type-selector">
                    <a href="?user_type=admin">
                        <button class="user-type-btn <?php echo $currentUserType === 'admin' ? 'active' : ''; ?>" data-type="admin">Admin</button>
                    </a>
                    <a href="?user_type=stakeholder">
                        <button class="user-type-btn <?php echo $currentUserType === 'stakeholder' ? 'active' : ''; ?>" data-type="stakeholder">Stakeholder</button>
                    </a>
                    <a href="?user_type=stock">
                        <button class="user-type-btn <?php echo $currentUserType === 'stock' ? 'active' : ''; ?>" data-type="stock">Stock</button>
                    </a>
                    <a href="?user_type=hr">
                        <button class="user-type-btn <?php echo $currentUserType === 'hr' ? 'active' : ''; ?>" data-type="hr">HR</button>
                    </a>
                    <a href="?user_type=employee">
                        <button class="user-type-btn <?php echo $currentUserType === 'employee' ? 'active' : ''; ?>" data-type="employee">Employee</button>
                    </a>
                </div>

                <!-- User Content -->
                <div class="user-content active">
                    <div class="top-bar">
                        <form class="search" method="GET" action="">
                            <input type="hidden" name="user_type" value="<?php echo $currentUserType; ?>">
                            <input type="search" name="search" placeholder="Search <?php echo ucfirst($currentUserType); ?>" value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                        </form>
                        <a href="add_user.php?type=<?php echo $currentUserType; ?>" class="add_user_btn"><i class="fas fa-plus"></i> Add new</a>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Names</th>
                                    <th>Site</th>
                                    <th>Department</th>
                                    <th>Registered Date</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Replace the table row section in your HTML with this: -->
                                <?php if (count($users) > 0): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr data-id="<?php echo $user['id']; ?>">
                                            <td><?php echo $i;
                                                $i++; ?></td>
                                            <td><?php echo !empty($user['name']) ? htmlspecialchars($user['name']) : 'No Name'; ?></td>
                                            <td><?php echo !empty($user['site']) ? htmlspecialchars($user['site']) : 'No Site'; ?></td>
                                            <td><?php echo !empty($user['department']) ? htmlspecialchars($user['department']) : 'No Department'; ?></td>
                                            <td><?php echo !empty($user['registration_date']) ? htmlspecialchars($user['registration_date']) : 'No Date'; ?></td>
                                            <td>
                                                <button class="btn-view" data-id="<?php echo $user['id']; ?>" data-type="<?php echo $currentUserType; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-edit" data-id="<?php echo $user['id']; ?>" data-type="<?php echo $currentUserType; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-delete" data-id="<?php echo $user['id']; ?>" data-type="<?php echo $currentUserType; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="no-results">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal container -->
    <div id="modal-container" class="modal-container"></div>

    <script>
        // When searching, keep current user type
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const userType = urlParams.get('user_type') || 'admin';

            document.querySelectorAll('.user-type-btn').forEach(button => {
                const btnType = button.getAttribute('data-type');
                if (btnType === userType) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });

            // Create modal container if it doesn't exist
            if (!document.getElementById('modal-container')) {
                const modalContainer = document.createElement('div');
                modalContainer.id = 'modal-container';
                modalContainer.className = 'modal-container';
                document.body.appendChild(modalContainer);
            }

            // Register event listeners for all action buttons
            registerModalEvents();
        });

        function registerModalEvents() {
            // View user buttons
            document.querySelectorAll('.btn-view').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-id');
                    const userType = this.getAttribute('data-type');
                    loadUserModal('view', userId, userType);
                });
            });

            // Edit user buttons
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-id');
                    const userType = this.getAttribute('data-type');
                    loadUserModal('edit', userId, userType);
                });
            });

            // Delete user buttons
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-id');
                    const userType = this.getAttribute('data-type');
                    confirmDelete(userId, userType);
                });
            });
        }

        function loadUserModal(action, userId, userType) {
            const modalContainer = document.getElementById('modal-container');

            // Show loading state
            modalContainer.innerHTML = '<div class="modal-overlay active"><div class="modal-content"><div class="loader"></div></div></div>';

            // Fetch user data
            fetch(`user_modal.php?action=${action}&id=${userId}&type=${userType}`)
                .then(response => response.text())
                .then(html => {
                    modalContainer.innerHTML = html;

                    // Add event listeners to the new modal
                    const modal = modalContainer.querySelector('.modal-overlay');
                    modal.classList.add('active');

                    // Close button functionality
                    const closeBtn = modal.querySelector('.modal-close');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', closeModal);
                    }

                    // Close on overlay click
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeModal();
                        }
                    });

                    // Handle form submission for edit mode
                    const form = modal.querySelector('form');
                    if (form && action === 'edit') {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitUserForm(form);
                        });

                        // Add click event for the submit button
                        const submitBtn = modal.querySelector('.btn-submit');
                        if (submitBtn) {
                            submitBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                submitUserForm(form);
                            });
                        }
                    }

                    // Add event listener for the Edit button in view mode
                    const editBtn = modal.querySelector('.btn-edit');
                    if (editBtn && action === 'view') {
                        editBtn.addEventListener('click', function() {
                            const userId = this.getAttribute('data-id');
                            const userType = this.getAttribute('data-type');
                            loadUserModal('edit', userId, userType);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading modal:', error);
                    modalContainer.innerHTML = `
                        <div class="modal-overlay active">
                            <div class="modal-content">
                                <h2>Error</h2>
                                <p>Failed to load user data. Please try again.</p>
                                <button class="modal-close">Close</button>
                            </div>
                        </div>`;
                    modalContainer.querySelector('.modal-close').addEventListener('click', closeModal);
                });
        }

        function submitUserForm(form) {
            const formData = new FormData(form);

            fetch('update_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showMessage('User updated successfully', 'success');

                        // Close modal
                        closeModal();

                        // Refresh the table (reload the page after a short delay)
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Show error message
                        showMessage(data.message || 'Error updating user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error updating user:', error);
                    showMessage('An unexpected error occurred', 'error');
                });
        }

        function confirmDelete(userId, userType) {
            const modalContainer = document.getElementById('modal-container');

            modalContainer.innerHTML = `
                <div class="modal-overlay active">
                    <div class="modal-content">
                        <h2>Confirm Delete</h2>
                        <p>Are you sure you want to delete this ${userType}?</p>
                        <div class="modal-actions">
                            <button class="btn-cancel">Cancel</button>
                            <button class="btn-confirm-delete">Delete</button>
                        </div>
                    </div>
                </div>`;

            // Add event listeners
            modalContainer.querySelector('.btn-cancel').addEventListener('click', closeModal);
            modalContainer.querySelector('.btn-confirm-delete').addEventListener('click', function() {
                deleteUser(userId, userType);
            });
        }

        function deleteUser(userId, userType) {
            fetch(`delete_user.php?id=${userId}&type=${userType}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    closeModal();

                    if (data.success) {
                        showMessage('User deleted successfully', 'success');

                        // Remove the row from the table
                        const row = document.querySelector(`tr[data-id="${userId}"]`);
                        if (row) {
                            row.remove();
                        } else {
                            // Reload the page if row can't be found
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        showMessage(data.message || 'Error deleting user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting user:', error);
                    showMessage('An unexpected error occurred', 'error');
                    closeModal();
                });
        }

        function closeModal() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                // First remove the active class to trigger the fade-out animation
                modal.classList.remove('active');

                // Use the transitionend event instead of setTimeout for more reliable timing
                modal.addEventListener('transitionend', function handler() {
                    // Remove the event listener to prevent memory leaks
                    modal.removeEventListener('transitionend', handler);

                    // Clear the container
                    const modalContainer = document.getElementById('modal-container');
                    if (modalContainer) {
                        modalContainer.innerHTML = '';
                    }
                });

                // Fallback timeout in case the transition event doesn't fire
                setTimeout(() => {
                    const modalContainer = document.getElementById('modal-container');
                    if (modalContainer && modalContainer.innerHTML !== '') {
                        modalContainer.innerHTML = '';
                    }
                }, 500); // A bit longer than the CSS transition time (300ms)
            }
        }

        function showMessage(message, type = 'info') {
            // Create message element if it doesn't exist
            let messageElement = document.getElementById('message-toast');
            if (!messageElement) {
                messageElement = document.createElement('div');
                messageElement.id = 'message-toast';
                document.body.appendChild(messageElement);
            }

            // Set message content and type
            messageElement.textContent = message;
            messageElement.className = `message-toast ${type}`;

            // Show message
            messageElement.classList.add('active');

            // Hide message after 3 seconds
            setTimeout(() => {
                messageElement.classList.remove('active');
            }, 3000);
        }
    </script>
</body>

</html>