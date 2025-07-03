<?php
// Include database functions
include '../config.php';

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (optional - customize based on your auth system)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

// Get announcement ID from URL
$announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($announcement_id == 0) {
    header('Location: stake-reply-announcements.php');
    exit();
}

// Get announcement details
function getAnnouncementById($id) {
    global $conn;
    
    $query = "SELECT * FROM announcement_forwards WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_response = isset($_POST['admin_response']) ? trim($_POST['admin_response']) : '';
    
    if (!empty($admin_response)) {
        // Update the announcement with admin response
        $query = "UPDATE announcement_forwards SET admin_response = ?, admin_response_date = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $admin_response, $announcement_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Reply sent successfully!";
            header('Location: stake-reply-announcements.php');
            exit();
        } else {
            $error_message = "Error sending reply. Please try again.";
        }
    } else {
        $error_message = "Please enter a response.";
    }
}

$announcement = getAnnouncementById($announcement_id);

if (!$announcement) {
    header('Location: stake-reply-announcements.php');
    exit();
}

// Helper function to format date
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('F j, Y \a\t g:i a');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Announcement | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/home-announce.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reply-form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .original-message {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .message-title {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }
        
        .message-date {
            color: #666;
            font-size: 14px;
        }
        
        .message-priority {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: #212529; }
        .priority-low { background: #28a745; color: white; }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-textarea {
            width: 100%;
            min-height: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
        }
        
        .form-textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1>Reply to Stakeholder Message</h1>
            <a href="stake-reply-announcements.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Messages</a>
        </div>
        
        <div class="reply-form-container">
            <!-- Display error message if exists -->
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Original Message -->
            <div class="original-message">
                <h3>Original Message</h3>
                <div class="message-header">
                    <div class="message-title"><?php echo htmlspecialchars($announcement['forward_type']); ?></div>
                    <div class="message-date"><?php echo formatDate($announcement['created_at']); ?></div>
                </div>
                <div style="margin-bottom: 10px;">
                    <span class="message-priority priority-<?php echo strtolower($announcement['priority']); ?>">
                        <?php echo strtoupper($announcement['priority']); ?> Priority
                    </span>
                </div>
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($announcement['user_comment'])); ?>
                </div>
            </div>
            
            <!-- Reply Form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="admin_response" class="form-label">
                        <i class="fas fa-reply"></i> Your Response
                    </label>
                    <textarea 
                        name="admin_response" 
                        id="admin_response" 
                        class="form-textarea" 
                        placeholder="Type your response here..."
                        required
                    ><?php echo isset($_POST['admin_response']) ? htmlspecialchars($_POST['admin_response']) : ''; ?></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                    <a href="stake-reply-announcements.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Auto-resize textarea
        document.getElementById('admin_response').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Confirm before leaving if content exists
        let originalContent = '';
        window.addEventListener('beforeunload', function(e) {
            const currentContent = document.getElementById('admin_response').value;
            if (currentContent !== originalContent && currentContent.trim() !== '') {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        
        // Update original content when form is submitted
        document.querySelector('form').addEventListener('submit', function() {
            window.removeEventListener('beforeunload', arguments.callee);
        });
</script>
</body>
</html>