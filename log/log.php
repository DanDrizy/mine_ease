<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/log.css">
    <title>MinEase - Welcome</title>
    <style></style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <div class="logo-icon">⛏️</div>
                <div class="logo-text">MinEase</div>
            </div>
            
            <div class="step-indicator">
                <div class="step-number">1</div>
                <div>
                    <div class="step-text">Member verification</div>
                    <div class="step-description">Check if you are already member of our system</div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <h1 class="welcome-title">Welcome</h1>
                
                <div class="form-tabs">
                    <button class="tab-button active" onclick="switchTab('login')">Login</button>
                    <button class="tab-button" onclick="switchTab('signup')">Sign Up</button>
                </div>

                <!-- Login Form -->
                <div id="login-form" class="form-section active">
                    <form action="backend/login.php" method="POST">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <input type="email" name="email" id="login-email" class="form-input" placeholder="Enter Here..." required>
                        </div>

                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <input type="password" name="password" id="login-password" class="form-input" placeholder="Enter Here..." required>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="remember-me">
                            <label for="remember-me">Remember me</label>
                        </div>

                        <div class="forgot-password">
                            <a href="#" onclick="showForgotPassword()">Forgot your password?</a>
                        </div>

                        <button type="submit" name="login" class="submit-btn">Login</button>
                    </form>
                </div>

                <!-- Signup Form -->
                <div id="signup-form" class="form-section">
                    <form action="backend/signup.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="signup-name">Full Name</label>
                            <input type="text" id="signup-name" name="name" class="form-input" placeholder="Enter your full name..." required>
                        </div>

                        <div class="form-group">
                            <label for="signup-username">Username</label>
                            <input type="text" id="signup-username" name="username" class="form-input" placeholder="Choose a username..." required>
                        </div>

                        <div class="form-group">
                            <label for="signup-email">Email</label>
                            <input type="email" id="signup-email" name="email" class="form-input" placeholder="Enter your email..." required>
                        </div>

                        <div class="form-group">
                            <label for="user-type">User Type</label>
                            <select id="user-type" name="user_type" class="form-input" required>
                                <option value="">Select user type...</option>
                                <option value="admin">Admin</option>
                                <option value="employee">Employee</option>
                                <option value="hr">HR</option>
                                <option value="stock">Stock</option>
                                <option value="stakeholder">Stakeholder</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="department">Department</label>
                            <select id="department" name="department" class="form-input" required>
                                <option value="">Select department...</option>
                                <option value="Administration">Administration</option>
                                <option value="Processing">Processing</option>
                                <option value="HR">HR</option>
                                <option value="Mining">Mining</option>
                                <option value="Stock">Stock</option>
                                <option value="Finance">Finance</option>
                                <option value="Head Office">Head Office</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="Enter phone number..." required>
                        </div>

                        <!-- <div class="form-group">
                            <label for="profile-image">Profile Image</label>
                            <div class="file-input-container">
                                <input type="file" id="profile-image" accept="image/*" style="display: none;">
                                <button type="button" class="file-input-btn" onclick="document.getElementById('profile-image').click()">
                                    <span id="file-name">Choose Profile Image</span>
                                    <span class="upload-icon">📁</span>
                                </button>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <input type="password" name="password" id="signup-password" class="form-input" placeholder="Create a password..." required>
                        </div>

                        <div class="form-group">
                            <label for="confirm-password">Confirm Password</label>
                            <input type="password" name="cpassword" id="confirm-password" class="form-input" placeholder="Confirm your password..." required>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" required>
                            <label for="terms">I agree to the Terms and Conditions</label>
                        </div>

                        <button type="submit" name="signup" class="submit-btn">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="js/logs.js"></script>
</html>