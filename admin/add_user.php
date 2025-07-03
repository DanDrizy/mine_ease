<?php
// Start session at the beginning
session_start();

// Database connection details
require_once '../config.php';

// Check if there are any errors or form data stored in session
$errors = isset($_SESSION['registration_errors']) ? $_SESSION['registration_errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session variables after retrieving them
unset($_SESSION['registration_errors']);
unset($_SESSION['form_data']);

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to get form value with fallback
function get_form_value($field) {
    global $form_data;
    return isset($form_data[$field]) ? htmlspecialchars($form_data[$field]) : '';
}

// Process form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize response array
    $response = [
        "status" => false,
        "message" => "",
        "errors" => []
    ];
    
    // Validate and sanitize inputs
    $name = sanitize_input($_POST['full_name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = sanitize_input($_POST['role']);
    $site = sanitize_input($_POST['site']);
    $department = sanitize_input($_POST['department']); 
    $username = sanitize_input($_POST['username']);
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : "";
    
    // Set current date and time
    $current_datetime = date("Y-m-d H:i:s");
    
    // Validation
    $errors = [];
    
    // Check required fields
    if (empty($name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain both letters and numbers";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($user_type)) {
        $errors[] = "User role is required";
    }
    
    if (empty($site)) {
        $errors[] = "Site location is required";
    }
    
    if (empty($department)) {
        $errors[] = "Department is required";
    }
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists. Please choose another.";
        }
        $stmt->close();
    }
    
    // Process form if no errors
    if (empty($errors)) {
        // Handle profile image upload
        $profile_image_path = "uploads/default-profile.png"; // Default image
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['profile_image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = "uploads/profile_images/";
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('profile_') . '.' . $file_extension;
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $profile_image_path = $target_file;
                } else {
                    $errors[] = "Failed to upload image.";
                }
            } else {
                $errors[] = "Invalid file type. Only JPG, PNG and GIF are allowed.";
            }
        }
        
        // If still no errors after file upload
        if (empty($errors)) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, username, user_type, site, department, phone, profile_image, registration_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Bind parameters
            $stmt->bind_param("ssssssssssss", $name, $email, $hashed_password, $username, $user_type, $site, $department, $phone, $profile_image_path, $current_datetime, $current_datetime, $current_datetime);
            
            // Execute query
            if ($stmt->execute()) {
                $response["status"] = true;
                $response["message"] = "User registered successfully!";
                
                // Redirect to user list page
                header("Location: user.php?success=User registered successfully");
                exit();
            } else {
                $response["message"] = "Error: " . $stmt->error;
                $errors[] = "Database error: " . $stmt->error;
            }
            
            // Close statement
            $stmt->close();
        }
    }
    
    // If there are errors
    if (!empty($errors)) {
        $response["errors"] = $errors;
        
        // Store errors in session to display after redirect
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Store form data to repopulate form
        
        // Redirect back to form
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=1");
        exit();
    }
}

// Close database connection if it exists
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .active_user {
            background-color: var(--primary-color);
            color: white;
        }
        .page-container {
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .back-button {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            background-color: #eee;
            border: none;
            border-radius: 5px;
            color: #444;
            text-decoration: none;
        }
        
        .back-button:hover {
            background-color: #ddd;
        }
        
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 0 auto;
        }
        
        .form-header {
            margin-bottom: 15px;
        }
        
        .form-header h2 {
            color: #304c8e;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }
        
        .form-header p {
            color: #666;
            font-size: 0.85rem;
            margin: 0;
        }
        
        .form-layout {
            display: flex;
            gap: 20px;
        }
        
        .form-column {
            flex: 1;
        }
        
        .profile-column {
            flex: 0 0 200px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #444;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: #304c8e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(48, 76, 142, 0.15);
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-primary {
            background-color: #304c8e;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #263c71;
        }
        
        .btn-cancel {
            background-color: #eee;
            color: #333;
        }
        
        .btn-cancel:hover {
            background-color: #ddd;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
        
        .required-indicator {
            color: #e53935;
            margin-left: 3px;
        }
        
        .help-text {
            font-size: 0.75rem;
            color: #666;
            margin-top: 3px;
        }
        
        .tab-container {
            display: flex;
            background-color: #f5f5f5;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .tab-button {
            flex: 1;
            padding: 10px;
            text-align: center;
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .tab-button.active {
            background-color: #304c8e;
            color: white;
        }
        
        .tab-button:hover:not(.active) {
            background-color: #e0e0e0;
        }
        
        .form-section-title {
            font-size: 1rem;
            font-weight: 500;
            color: #304c8e;
            margin: 15px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .profile-image-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        
        .profile-image-preview i {
            font-size: 36px;
            color: #aaa;
        }
        
        .profile-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .upload-button {
            display: inline-block;
            padding: 6px 12px;
            background-color: #f0f7ff;
            color: #304c8e;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            text-align: center;
            width: 100%;
        }
        
        .upload-button:hover {
            background-color: #e0f0ff;
        }
        
        .upload-input {
            display: none;
        }
        
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .alert-danger {
            background-color: #ffebee;
            border-color: #e53935;
            color: #c62828;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            border-color: #4caf50;
            color: #2e7d32;
        }
        
        .error-list {
            margin: 5px 0 0;
            padding-left: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <?php include 'main/slidebar.php'; ?>
    <div class="main-content">
        <div class="page-container">
            <div class="page-header">
                <h1 class="page-title">Add New User</h1>
                <a href="user.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
            <?php endif; ?>
            
            <div class="form-container">
                <div class="form-header">
                    <h2>Register New User</h2>
                    <p>Fill out the form below to register a new user. Fields marked with an asterisk (*) are required.</p>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                    <div class="form-layout">
                        <!-- Left Column - Profile Image -->
                        <div class="profile-column">
                            <div class="profile-image-container">
                                <div class="profile-image-preview" id="imagePreview">
                                    <i class="fas fa-user"></i>
                                </div>
                                <label for="profile_image" class="upload-button">
                                    <i class="fas fa-camera"></i> Upload Photo
                                </label>
                                <input type="file" id="profile_image" name="profile_image" class="upload-input" accept="image/*">
                            </div>
                            
                            <div class="form-group">
                                <label for="role">Role <span class="required-indicator">*</span></label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="">Select a role</option>
                                    <option value="stakeholder" <?php echo (get_form_value('role') == 'stakeholder') ? 'selected' : ''; ?>>Stakeholder</option>
                                    <option value="hr" <?php echo (get_form_value('role') == 'hr') ? 'selected' : ''; ?>>HR</option>
                                    <option value="employee" <?php echo (get_form_value('role') == 'employee') ? 'selected' : ''; ?>>Employee</option>
                                    <option value="Admin" <?php echo (get_form_value('role') == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Middle Column - Personal Information -->
                        <div class="form-column">
                            <div class="form-section-title">Personal Information</div>
                            
                            <div class="form-group">
                                <label for="full_name">Full Name <span class="required-indicator">*</span></label>
                                <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo get_form_value('full_name'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address <span class="required-indicator">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo get_form_value('email'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo get_form_value('phone'); ?>">
                            </div>
                            
                            <div class="form-section-title">User Details</div>
                            
                            <div class="form-group">
                                <label for="department">Department <span class="required-indicator">*</span></label>
                                <select id="department" name="department" class="form-control" required>
                                    <option value="" hidden>Finance</option>
                                    <option value="finacce">Finance</option>
                                    <option value="management">Management</option>
                                    <option value="train Develope">Training and Development</option>
                                    <option value="exploration">Exploration</option>
                                    <option value="office">Office Support Services</option>
                                    <option value="environment">Environmental Management Department</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="site">Site Location <span class="required-indicator">*</span></label>
                                <select id="site" name="site" class="form-control" required>
                                    <option value="">Select a site</option>
                                    <option value="kigali" <?php echo (get_form_value('site') == 'kigali') ? 'selected' : ''; ?>>Kigali</option>
                                    <option value="kabuga" <?php echo (get_form_value('site') == 'kabuga') ? 'selected' : ''; ?>>Kabuga</option>
                                    <option value="huye" <?php echo (get_form_value('site') == 'huye') ? 'selected' : ''; ?>>Huye</option>
                                    <option value="gicumbi" <?php echo (get_form_value('site') == 'gicumbi') ? 'selected' : ''; ?>>Gicumbi</option>
                                    <option value="butare" <?php echo (get_form_value('site') == 'butare') ? 'selected' : ''; ?>>Butare</option>
                                    <option value="rwamagana" <?php echo (get_form_value('site') == 'rwamagana') ? 'selected' : ''; ?>>Rwamagana</option>
                                    <option value="nyamata" <?php echo (get_form_value('site') == 'nyamata') ? 'selected' : ''; ?>>Nyamata</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Right Column - Account Security -->
                        <div class="form-column">
                            <div class="form-section-title">Account Security</div>
                            
                            <div class="form-group">
                                <label for="username">Username <span class="required-indicator">*</span></label>
                                <input type="text" id="username" name="username" class="form-control" value="<?php echo get_form_value('username'); ?>" required>
                                <p class="help-text">Username must be unique</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password <span class="required-indicator">*</span></label>
                                <input type="password" id="password" name="password" class="form-control" required>
                                <p class="help-text">Min 8 characters with letters and numbers</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password <span class="required-indicator">*</span></label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                            
                            <br>
                            <br>
                            <br>
                            <br>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-cancel" onclick="window.location.href='user.php'">Cancel</button>
                                <button type="submit" class="btn btn-primary">Register User</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Preview profile image when selected
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.innerHTML = `<img src="${event.target.result}" alt="Profile Preview">`;
                }
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<i class="fas fa-user"></i>';
            }
        });
    </script>
</body>
</html>