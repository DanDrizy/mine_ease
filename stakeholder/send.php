<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Action - Stakeholders Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
    .active_news {
        background-color: #007bff;
        color: white;
    } 
    
    .email-container {
        width: 100%;
        height: 90%;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    
    .email-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    
    .email-title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    
    .email-action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .email-action-buttons button {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
    }
    
    .send-btn {
        background-color: #007bff;
        color: white;
    }
    
    .discard-btn {
        background-color: #dc3545;
        color: white;
    }
    
    .save-draft-btn {
        background-color: #6c757d;
        color: white;
    }
    
    .email-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .form-group label {
        font-weight: 500;
        color: #555;
    }
    
    .form-group input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: inherit;
        font-size: 14px;
    }
    
    .form-group input:focus {
        border-color: #007bff;
        outline: none;
    }
    
    .original-email {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border-left: 4px solid #007bff;
    }
    
    .original-email-header {
        margin-bottom: 10px;
        font-size: 12px;
        color: #666;
    }
    
    .formatting-toolbar {
        display: flex;
        gap: 10px;
        padding: 10px;
        background-color: #f0f2f5;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .formatting-toolbar button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 3px;
    }
    
    .formatting-toolbar button:hover {
        background-color: #e2e6ea;
    }
    
    .formatting-toolbar button.active {
        background-color: #d0d6df;
    }
    
    .attachments {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    
    .attachment {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background-color: #e9ecef;
        border-radius: 5px;
        font-size: 12px;
    }
    
    .attachment i {
        color: #6c757d;
    }
    
    .add-attachment {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        color: #007bff;
        cursor: pointer;
    }
    
    .add-attachment:hover {
        text-decoration: underline;
    }
    
    .email-body-editor {
        min-height: 250px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.5;
        overflow-y: auto;
    }
    
    .email-body-editor:focus {
        border-color: #007bff;
        outline: none;
    }
    
    /* Style for rich text editor */
    #richTextEditor {
        min-height: 250px;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        background-color: white;
        overflow-y: auto;
    }
    
    #richTextEditor:focus {
        outline: none;
        border-color: #007bff;
    }
    
    /* Style for hidden textarea */
    #emailBody {
        display: none;
    }
    
    /* Show notification */
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1000;
    }
    
    /* Responsive styles */
    @media (max-width: 768px) {
        .email-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .email-action-buttons {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Email";
        
        include 'main/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'main/header.php'; ?>
            
            <!-- Email Container -->
            <div class="email-container">
                <div class="email-header">
                    <div class="email-title" id="emailActionTitle">Reply to Message</div>
                    <div class="email-action-buttons">
                        <button class="save-draft-btn" id="saveDraftBtn">
                            <i class="fas fa-save"></i> Save Draft
                        </button>
                        <button class="discard-btn" id="discardBtn">
                            <i class="fas fa-trash"></i> Discard
                        </button>
                        <button class="send-btn" id="sendBtn">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
                
                <form class="email-form" id="emailForm">
                    <div class="form-group">
                        <label for="recipientEmail">To:</label>
                        <input type="email" id="recipientEmail" placeholder="recipient@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="emailSubject">Subject:</label>
                        <input type="text" id="emailSubject" required>
                    </div>
                    
                    <div class="formatting-toolbar">
                        <button type="button" title="Bold" id="boldBtn" onclick="formatText('bold')"><i class="fas fa-bold"></i></button>
                        <button type="button" title="Italic" id="italicBtn" onclick="formatText('italic')"><i class="fas fa-italic"></i></button>
                        <button type="button" title="Underline" id="underlineBtn" onclick="formatText('underline')"><i class="fas fa-underline"></i></button>
                        <button type="button" title="Bulleted List" id="bulletBtn" onclick="formatText('insertUnorderedList')"><i class="fas fa-list-ul"></i></button>
                        <button type="button" title="Numbered List" id="numberBtn" onclick="formatText('insertOrderedList')"><i class="fas fa-list-ol"></i></button>
                        <button type="button" title="Add Link" id="linkBtn" onclick="addLink()"><i class="fas fa-link"></i></button>
                        <button type="button" title="Align Left" id="alignLeftBtn" onclick="formatText('justifyLeft')"><i class="fas fa-align-left"></i></button>
                        <button type="button" title="Align Center" id="alignCenterBtn" onclick="formatText('justifyCenter')"><i class="fas fa-align-center"></i></button>
                        <button type="button" title="Align Right" id="alignRightBtn" onclick="formatText('justifyRight')"><i class="fas fa-align-right"></i></button>
                    </div>
                    
                    <div class="form-group">
                        <!-- Hidden textarea to store HTML content -->
                        <textarea id="emailBody" name="emailBody" required></textarea>
                        <!-- Editable div that works as rich text editor -->
                        <div id="richTextEditor" contenteditable="true" placeholder="Type your message here..."></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize the rich text editor
        document.addEventListener('DOMContentLoaded', function() {
            const richTextEditor = document.getElementById('richTextEditor');
            const emailBody = document.getElementById('emailBody');
            const emailForm = document.getElementById('emailForm');
            const saveDraftBtn = document.getElementById('saveDraftBtn');
            const discardBtn = document.getElementById('discardBtn');
            const sendBtn = document.getElementById('sendBtn');
            
            // Set placeholder for the rich text editor
            richTextEditor.dataset.placeholder = "Type your message here...";
            
            // Update hidden textarea with rich text content when the form is submitted
            emailForm.addEventListener('submit', function(e) {
                emailBody.value = richTextEditor.innerHTML;
            });
            
            // Update hidden textarea when focus leaves the editor
            richTextEditor.addEventListener('blur', function() {
                emailBody.value = richTextEditor.innerHTML;
            });
            
            // Add placeholder functionality
            richTextEditor.addEventListener('focus', function() {
                if (richTextEditor.innerHTML === '') {
                    richTextEditor.innerHTML = '';
                }
            });
            
            richTextEditor.addEventListener('blur', function() {
                if (richTextEditor.innerHTML === '') {
                    richTextEditor.innerHTML = '';
                }
            });
            
            // Save draft functionality
            saveDraftBtn.addEventListener('click', function(e) {
                e.preventDefault();
                emailBody.value = richTextEditor.innerHTML;
                showNotification('Email saved to drafts');
            });
            
            // Discard functionality
            discardBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to discard this email?')) {
                    richTextEditor.innerHTML = '';
                    emailBody.value = '';
                    document.getElementById('recipientEmail').value = '';
                    document.getElementById('emailSubject').value = '';
                    showNotification('Email discarded');
                }
            });
            
            // Send functionality
            sendBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Simple validation
                if (!document.getElementById('recipientEmail').value) {
                    alert('Please enter recipient email');
                    return;
                }
                
                if (!document.getElementById('emailSubject').value) {
                    alert('Please enter email subject');
                    return;
                }
                
                if (richTextEditor.innerHTML.trim() === '') {
                    alert('Please enter email body');
                    return;
                }
                
                emailBody.value = richTextEditor.innerHTML;
                showNotification('Email sent successfully!');
                
                // In real app, here you would submit the form or make AJAX request
                console.log('Email content:', emailBody.value);
                
                // Reset form after sending (in real app would redirect)
                setTimeout(() => {
                    richTextEditor.innerHTML = '';
                    emailBody.value = '';
                    document.getElementById('recipientEmail').value = '';
                    document.getElementById('emailSubject').value = '';
                }, 1500);
            });
        });
        
        // Function to format selected text
        function formatText(command, value = null) {
            // Apply the formatting command
            document.execCommand(command, false, value);
            
            // Focus back on the editor
            document.getElementById('richTextEditor').focus();
            
            // Toggle active state for the button
            if (['bold', 'italic', 'underline'].includes(command)) {
                const buttonId = command + 'Btn';
                const button = document.getElementById(buttonId);
                
                // Check if the command is currently active
                const isActive = document.queryCommandState(command);
                
                // Update button appearance
                if (isActive) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            }
            
            // Show notification for the action
            showNotification(`${command.charAt(0).toUpperCase() + command.slice(1)} formatting applied`);
        }
        
        // Function to add link
        function addLink() {
            const url = prompt('Enter URL:', 'http://');
            if (url) {
                document.execCommand('createLink', false, url);
                showNotification('Link added');
            }
        }
        
        // Function to show notification
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            
            // Add to document
            document.body.appendChild(notification);
            
            // Trigger animation
            setTimeout(() => {
                notification.style.opacity = '1';
            }, 10);
            
            // Remove after delay
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // Monitor formatting state to update buttons
        document.addEventListener('selectionchange', function() {
            const formatCommands = ['bold', 'italic', 'underline', 'justifyLeft', 'justifyCenter', 'justifyRight'];
            
            formatCommands.forEach(cmd => {
                try {
                    const button = document.getElementById(cmd.replace('justify', 'align').toLowerCase() + 'Btn');
                    if (button) {
                        if (document.queryCommandState(cmd)) {
                            button.classList.add('active');
                        } else {
                            button.classList.remove('active');
                        }
                    }
                } catch (e) {
                    console.log('Error checking command state for:', cmd);
                }
            });
        });
    </script>
</body>
</html>