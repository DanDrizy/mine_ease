<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal</title>
  <link rel="stylesheet" href="../style/log.css">
  <style> </style>
</head>
<body>
  <div class="container">
    <div class="blue-bg">
      <div class="particles"></div>
      <h2>Welcome Back</h2>
      <p>Access your admin dashboard and manage your platform with ease</p>
      <button class="toggle-btn">Sign Up</button>
    </div>
    
    <div class="form-container login-container">
      <form id="login-form">
        <h1>Admin Login</h1>
        <div class="input-group">
          <input type="text" id="login-username" required>
          <label for="login-username">Username</label>
        </div>
        <div class="input-group">
          <input type="password" id="login-password" required>
          <label for="login-password">Password</label>
        </div>
        <a href="#" class="forgot-pass">Forgot Password?</a>
        <div id="login-message"></div>
        <button type="submit" class="submit-btn">Login</button>
      </form>
    </div>
    
    <div class="form-container signup-container">
      <form id="signup-form">
        <h1>Create Account</h1>
        <div class="input-group">
          <input type="text" id="signup-username" required>
          <label for="signup-username">Username</label>
        </div>
        <div class="input-group">
          <input type="email" id="signup-email" required>
          <label for="signup-email">Email</label>
        </div>
        <div class="input-group">
          <input type="password" id="signup-password" required>
          <label for="signup-password">Password</label>
        </div>
        <div class="input-group">
          <input type="password" id="confirm-password" required>
          <label for="confirm-password">Confirm Password</label>
        </div>
        <div id="signup-message"></div>
        <button type="submit" class="submit-btn">Sign Up</button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get elements
      const container = document.querySelector('.container');
      const blueBg = document.querySelector('.blue-bg');
      const toggleBtn = document.querySelector('.toggle-btn');
      const loginForm = document.getElementById('login-form');
      const signupForm = document.getElementById('signup-form');
      const loginMessage = document.getElementById('login-message');
      const signupMessage = document.getElementById('signup-message');
      
      // Toggle between login and signup
      toggleBtn.addEventListener('click', function() {
        container.classList.toggle('signup-mode');
        blueBg.classList.toggle('signup-mode');
        
        // Clear any existing messages
        loginMessage.innerHTML = '';
        signupMessage.innerHTML = '';
        
        if (container.classList.contains('signup-mode')) {
          toggleBtn.textContent = 'Login';
          blueBg.querySelector('h2').textContent = 'Hello, Admin!';
          blueBg.querySelector('p').textContent = 'Register with your details to join our admin panel';
        } else {
          toggleBtn.textContent = 'Sign Up';
          blueBg.querySelector('h2').textContent = 'Welcome Back';
          blueBg.querySelector('p').textContent = 'Access your admin dashboard and manage your platform with ease';
        }
      });
      
      // Create animated background particles
      const particlesContainer = document.querySelector('.particles');
      const particleCount = 20;
      
      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        // Random position
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        particle.style.left = `${posX}%`;
        particle.style.top = `${posY}%`;
        
        // Random size
        const size = Math.random() * 5 + 3;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        
        // Random animation duration and delay
        const duration = Math.random() * 3 + 2;
        const delay = Math.random() * 2;
        particle.style.animationDuration = `${duration}s`;
        particle.style.animationDelay = `${delay}s`;
        
        particlesContainer.appendChild(particle);
      }
      
      // Helper functions for showing messages
      function showMessage(element, message, type) {
        element.innerHTML = `<div class="alert ${type}">${message}</div>`;
        setTimeout(() => {
          element.innerHTML = '';
        }, 3000);
      }
      
      function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
      }
      
      // Login form submission
      loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;
        
        // Simple validation
        if (username.length < 3) {
          showMessage(loginMessage, 'Username must be at least 3 characters long', 'error');
          return;
        }
        
        if (password.length < 6) {
          showMessage(loginMessage, 'Password must be at least 6 characters long', 'error');
          return;
        }
        
        // Show success message (in a real app, you would handle authentication here)
        showMessage(loginMessage, 'Login successful! Redirecting to dashboard...', 'success');
        
        // Reset form after delay
        setTimeout(() => {
          loginForm.reset();
          // In a real application, you would redirect here
          // window.location.href = "dashboard.html";
        }, 2000);
      });
      
      // Signup form submission
      signupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('signup-username').value;
        const email = document.getElementById('signup-email').value;
        const password = document.getElementById('signup-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Simple validation
        if (username.length < 3) {
          showMessage(signupMessage, 'Username must be at least 3 characters long', 'error');
          return;
        }
        
        if (!validateEmail(email)) {
          showMessage(signupMessage, 'Please enter a valid email address', 'error');
          return;
        }
        
        if (password.length < 6) {
          showMessage(signupMessage, 'Password must be at least 6 characters long', 'error');
          return;
        }
        
        if (password !== confirmPassword) {
          showMessage(signupMessage, 'Passwords do not match', 'error');
          return;
        }
        
        // Show success message
        showMessage(signupMessage, 'Account created successfully! Please login.', 'success');
        
        // Reset form and switch back to login after delay
        setTimeout(() => {
          signupForm.reset();
          container.classList.remove('signup-mode');
          blueBg.classList.remove('signup-mode');
          toggleBtn.textContent = 'Sign Up';
          blueBg.querySelector('h2').textContent = 'Welcome Back';
          blueBg.querySelector('p').textContent = 'Access your admin dashboard and manage your platform with ease';
        }, 2000);
      });
      
      // Remove the problematic code that was trying to add 'focused' class
      // This was causing issues with the toggle functionality
      
      // Prevent default on forgot password link
      document.querySelector('.forgot-pass').addEventListener('click', function(e) {
        e.preventDefault();
        showMessage(loginMessage, 'Password reset link sent to your email!', 'success');
      });
    });
  </script>
</body>
</html>