
        function switchTab(tab) {
            // Remove active class from all tabs and forms
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.form-section').forEach(section => section.classList.remove('active'));
            
            // Add active class to clicked tab
            event.target.classList.add('active');
            
            // Show corresponding form
            document.getElementById(tab + '-form').classList.add('active');
        }

        function showForgotPassword() {
            alert('Forgot password functionality would redirect to password reset page.');
        }

        // Handle file input
        document.getElementById('profile-image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose Profile Image';
            document.getElementById('file-name').textContent = fileName;
        });

        // Form submission handlers
        // document.getElementById('login-form').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     const email = document.getElementById('login-email').value;
        //     const password = document.getElementById('login-password').value;
            
        //     // if (email && password) {

        //     //     window.location.href = 'backend/login.php';

        //     // }
        // });

        // document.getElementById('signup-form').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     const name = document.getElementById('signup-name').value;
        //     const username = document.getElementById('signup-username').value;
        //     const email = document.getElementById('signup-email').value;
        //     const userType = document.getElementById('user-type').value;
        //     const department = document.getElementById('department').value;
        //     const phone = document.getElementById('phone').value;
        //     const password = document.getElementById('signup-password').value;
        //     const confirmPassword = document.getElementById('confirm-password').value;
            
        //     if (password !== confirmPassword) {
        //         alert('Passwords do not match!');
        //         return;
        //     }
            
            // if (name && username && email && userType && department && phone && password) {
            //     alert('Account created successfully! (This is a demo)\n\nUser Details:\n- Name: ' + name + '\n- Username: ' + username + '\n- User Type: ' + userType + '\n- Department: ' + department);
            // } else {
            //     alert('Please fill in all required fields!');
            // }
        // });
    