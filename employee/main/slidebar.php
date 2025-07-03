<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Document</title>
</head>
<style>
    .log-icon
    {
        text-align: center;
        padding: 20px;
        margin: 0 0 30px;
        color: white;
        border-radius: 10px 10px 0 0;
    }
    a{
        text-decoration: none;
    }
    .sidebar-menu-slide
    {
        display: flex;
        flex-direction: column;
        font-size: 16px;
        /* background: red; */

    }
    .menu-item-slidebar
    {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        color: white;
        transition: background-color 0.3s ease;
    }
    .menu-item-slidebar a{
        color: white;
        display: flex;
        align-items: center;
        text-decoration: none;
        margin: 0 0 20px;
        width: 100%;
    }
    .menu-item-slidebar i{

        font-size: 20px;
        margin-right: 30px;
    }
    .menu-item-slidebar a:hover
    {
        color:rgb(206, 255, 127);
    }


</style>
<body>
<div class="sidebar">
        <div class="log-icon">
        <h1>Mine_Ease</h1>
        <p>Management System</p>
    </div>
        <div class="sidebar-menu-slide">
            <div  class="menu-item-slidebar">
            <a href="index.php">
                <i class="fas fa-tachometer-alt" ></i>
                <div><span>Dashboard</span></div>
            </a>
            </div>
            <div  class="menu-item-slidebar">
            <a href="attendance.php">
                <i class="fas fa-clipboard-check"></i>
                <div><span>Attendance</span></div>
            </a>
            </div>
            <div  class="menu-item-slidebar">
            <a href="announce.php">
                <i class ="fas fa-calendar-check"></i>
                <div><span>Announcement</span></div>
            </a>
            </div>
            <div  class="menu-item-slidebar">
            <a href="contact.php">
                <i class="fas fa-address-book"></i>
                <div><span>Contact</span></div>
            </a>
            </div>
            <div  class="menu-item-slidebar">
            <a href="request.php">
                <i class="fas fa-paper-plane"></i>
                <div><span>Request</span></div>
            </a>
            </div>
            

            
            
            
            
            
            
            
        </div>
    </div>
</body>
</html>