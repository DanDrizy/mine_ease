<?php
require_once '../config.php';
// session_start();
$user_id = $_SESSION['user_id'] ?? null;
// Get announcement details
$announcement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$announcement = null;

if ($announcement_id > 0) {
    $query = "SELECT * FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $announcement = $result->fetch_assoc();
    }
    $stmt->close();
}

// Handle form submission
if ($_POST && isset($_POST['submit_forward'])) {
    $comment = trim($_POST['comment']);
    $priority = $_POST['priority'];
    $forward_type = $_POST['forward_type'];
    $status = 'pending';
    $cur_date = date('Y-m-d H:i:s');
    
    if (!empty($comment)) {
        // Insert into forwards table (you might need to create this table)
        $insert_query = "INSERT INTO announcement_forwards (announcement_id,user_id, user_comment, priority, forward_type, status, created_at) VALUES ('$announcement_id', '$user_id', '$comment', '$priority', '$forward_type', '$status', '$cur_date')";
        $insert_stmt = mysqli_query($conn,$insert_query);

    } else {
        $error_message = "Please enter your comment or question.";
    }
}

if (!$announcement) {
    header("Location: news_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forward Announcement - Ask Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .main-content-forwad
        {
            width: 90%;
            height: 100vh;
            overflow-y: auto;
        }
        
        .forward-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .forward-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .forward-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .forward-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .announcement-preview {
            background: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #667eea;
            margin: 20px;
            border-radius: 5px;
        }
        
        .announcement-preview h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .announcement-meta {
            display: flex;
            gap: 20px;
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }
        
        .announcement-content {
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        
        .forward-form {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            padding: 20px;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .priority-high { color: #dc3545; }
        .priority-medium { color: #ffc107; }
        .priority-low { color: #28a745; }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Forward to Admin";
        include 'main/sidebar.php'; 
        ?>
        
        <!-- Main Content -->
        <div class="main-content-forwad ">
            
            <div class="forward-container">
                <div class="forward-header">
                    <h2><i class="fas fa-paper-plane"></i> Forward to Admin</h2>
                    <p>Ask the admin about this announcement or report an issue</p>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Announcement Preview -->
                <div class="announcement-preview">
                    <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <div class="announcement-meta">
                        <span><i class="fas fa-tag"></i> <strong>Type:</strong> <?php echo htmlspecialchars(ucfirst($announcement['type'])); ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars(ucfirst($announcement['location'])); ?></span>
                        <span><i class="fas fa-calendar"></i> <strong>Date:</strong> <?php echo date('F j, Y', strtotime($announcement['created_at'])); ?></span>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                    </div>
                </div>
                
                <!-- Forward Form -->
                <form method="POST" class="forward-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="forward_type">
                                <i class="fas fa-question-circle"></i> What do you want to ask about?
                            </label>
                            <select name="forward_type" id="forward_type" required>
                                <option value="">Select reason...</option>
                                <option value="clarification">Need Clarification</option>
                                <option value="concern">I have a concern</option>
                                <option value="question">General Question</option>
                                <option value="issue">Report an Issue</option>
                                <option value="suggestion">Make a Suggestion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="priority">
                                <i class="fas fa-exclamation-triangle"></i> Priority Level
                            </label>
                            <select name="priority" id="priority" required>
                                <option value="low">Low - General inquiry</option>
                                <option value="medium" selected>Medium - Needs attention</option>
                                <option value="high">High - Urgent matter</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">
                            <i class="fas fa-comment"></i> Your Message to Admin
                        </label>
                        <textarea name="comment" id="comment" placeholder="Please explain what you want to know or discuss about this announcement..." required></textarea>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" name="submit_forward" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send to Admin
                        </button>
                        <a href="news_dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to News
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            const prioritySelect = document.getElementById('priority');
            const forwardTypeSelect = document.getElementById('forward_type');
            const commentTextarea = document.getElementById('comment');
            
            // Update placeholder based on forward type
            forwardTypeSelect.addEventListener('change', function() {
                const type = this.value;
                let placeholder = "Please explain what you want to know or discuss about this announcement...";
                
                switch(type) {
                    case 'clarification':
                        placeholder = "What parts of this announcement need clarification? What specific information are you looking for?";
                        break;
                    case 'concern':
                        placeholder = "What concerns do you have about this announcement? Please be specific about your worries.";
                        break;
                    case 'question':
                        placeholder = "What questions do you have about this announcement or its implementation?";
                        break;
                    case 'issue':
                        placeholder = "What issues have you identified with this announcement? Please provide details.";
                        break;
                    case 'suggestion':
                        placeholder = "What suggestions do you have regarding this announcement? How can it be improved?";
                        break;
                    case 'other':
                        placeholder = "Please describe what you want to discuss with the admin about this announcement.";
                        break;
                }
                
                commentTextarea.placeholder = placeholder;
            });
            
            // Character counter
            const maxLength = 1000;
            const counter = document.createElement('div');
            counter.style.cssText = 'text-align: right; font-size: 12px; color: #666; margin-top: 5px;';
            commentTextarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - commentTextarea.value.length;
                counter.textContent = `${commentTextarea.value.length}/${maxLength} characters`;
                counter.style.color = remaining < 50 ? '#dc3545' : '#666';
            }
            
            commentTextarea.addEventListener('input', updateCounter);
            commentTextarea.setAttribute('maxlength', maxLength);
            updateCounter();
        });
    </script>
</body>
</html>