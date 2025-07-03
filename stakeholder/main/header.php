<?php 
error_reporting(0); 
// Get notifications from database 
$notificationCount = 0; 
$notifications = [];  

// Query to get latest notifications 
if (isset($conn)) {     
    $notifQuery = "SELECT id, content FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5";     
    $notifResult = $conn->query($notifQuery);          
    
    if ($notifResult && $notifResult->num_rows > 0) {         
        $notificationCount = $notifResult->num_rows;         
        while ($row = $notifResult->fetch_assoc()) {             
            $notifications[] = $row;         
        }     
    } 
} 
?>  

<div class="header">     
    <h1>Stakeholder</h1>          
    
    <div class="header-icons">                  
        <div class="profile-icon">             
            <a href="#" onclick="showLogoutDialog()" style="text-decoration: none;">                 
                <svg fill="CurrentColor" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">                     
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>                     
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>                     
                    <g id="SVGRepo_iconCarrier">                         
                        <title>logout</title>                         
                        <path d="M0 9.875v12.219c0 1.125 0.469 2.125 1.219 2.906 0.75 0.75 1.719 1.156 2.844 1.156h6.125v-2.531h-6.125c-0.844 0-1.5-0.688-1.5-1.531v-12.219c0-0.844 0.656-1.5 1.5-1.5h6.125v-2.563h-6.125c-1.125 0-2.094 0.438-2.844 1.188-0.75 0.781-1.219 1.75-1.219 2.875zM6.719 13.563v4.875c0 0.563 0.5 1.031 1.063 1.031h5.656v3.844c0 0.344 0.188 0.625 0.5 0.781 0.125 0.031 0.25 0.031 0.313 0.031 0.219 0 0.406-0.063 0.563-0.219l7.344-7.344c0.344-0.281 0.313-0.844 0-1.156l-7.344-7.313c-0.438-0.469-1.375-0.188-1.375 0.563v3.875h-5.656c-0.563 0-1.063 0.469-1.063 1.031z"></path>                     
                    </g>                 
                </svg>             
            </a>         
        </div>     
    </div> 
</div>

<!-- Custom Logout Dialog -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="logout-icon">
                <svg fill="currentColor" viewBox="0 0 24 24" width="32" height="32">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
            </div>
            <h3>Confirm Logout</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to logout? You will need to sign in again to access your account.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="hideLogoutDialog()">Cancel</button>
            <button class="btn-confirm" onclick="confirmLogout()">Yes, Logout</button>
        </div>
    </div>
</div>

<style> 
.header {     
    display: flex;     
    justify-content: space-between;     
    align-items: center;     
    padding: 15px 20px;     
    background-color: #fff;     
    box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
}  

.header h1 {     
    margin: 0;     
    font-size: 24px;     
    color: #333; 
}  

.header-icons {     
    display: flex;     
    align-items: center; 
}   

.profile-icon {     
    cursor: pointer;     
    width: 24px;     
    height: 24px;
    transition: transform 0.2s ease;
}

.profile-icon:hover {
    transform: scale(1.1);
}

.profile-icon svg {     
    width: 100%;     
    height: 100%;     
    color: #333; 
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background-color: #ffffff;
    margin: 10% auto;
    border-radius: 12px;
    width: 90%;
    max-width: 420px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease;
    overflow: hidden;
}

.modal-header {
    padding: 24px 24px 16px;
    text-align: center;
    border-bottom: 1px solid #f0f0f0;
}

.logout-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
}

.modal-body {
    padding: 20px 24px;
    text-align: center;
}

.modal-body p {
    margin: 0;
    color: #666;
    font-size: 15px;
    line-height: 1.5;
}

.modal-footer {
    padding: 16px 24px 24px;
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn-cancel, .btn-confirm {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 100px;
}

.btn-cancel {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

.btn-cancel:hover {
    background-color: #e9ecef;
    transform: translateY(-1px);
}

.btn-confirm {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    box-shadow: 0 2px 8px rgba(238, 90, 82, 0.3);
}

.btn-confirm:hover {
    background: linear-gradient(135deg, #ee5a52, #dc4c64);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(238, 90, 82, 0.4);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-30px) scale(0.9);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsive design */
@media (max-width: 480px) {
    .modal-content {
        margin: 20% auto;
        width: 95%;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .btn-cancel, .btn-confirm {
        width: 100%;
    }
}
</style>

<script>
function showLogoutDialog() {
    document.getElementById('logoutModal').style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function hideLogoutDialog() {
    document.getElementById('logoutModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
}

function confirmLogout() {
    // Redirect to logout page
    window.location.href = '../log/logout.php';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target === modal) {
        hideLogoutDialog();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideLogoutDialog();
    }
});
</script>