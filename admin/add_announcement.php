<?php 

include'../config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Announcement | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/announce.css">
    <style></style>
</head>
<body>
    <?php include 'main/slidebar.php'; 
    
    
    $sel_location = mysqli_query($conn, "SELECT site FROM users group by site ");
    $fetch = mysqli_fetch_array($sel_location);
    
    ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1>Add New Announcement</h1>
            <a href="announcements.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Announcements</a>
        </div> 
        <div class="form-container">
            <div class="form-header">
            </div>
            
            <form action="backend/process-announcement.php" method="post">
                <div class="form-group">
                    <label for="title" class="form-label">Announcement Title*</label>
                    <input type="text" id="title" name="title" class="form-input" placeholder="Enter a clear and concise title"   required>
                </div>
                
                <div class="form-group">
                    <label for="category" class="form-label">Location*</label>
                    <select id="category" name="location" class="form-select" required>
                        <option value="" hidden>Select a location</option>
                        <option value="all">All Locations</option>
                        <?php while($fetch = mysqli_fetch_array($sel_location)){ ?>

                        <option value="<?php echo   $fetch['site'] ; ?>"> <?php echo   $fetch['site'] ; ?> </option>

                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category" class="form-label">Type*</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="" hidden>Select a Type</option>
                        <option value="All">All</option>
                        <option value="admin">Admin</option>
                        <option value="hr">Human Resource</option>
                        <option value="stake">StakeHolder</option>
                        <option value="stock">Stock</option>
                        <option value="emp">Employee</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="content" class="form-label">Announcement Content*</label>
                    <div class="editor-toolbar">
                        <!-- Editor toolbar is empty in the original form -->
                    </div>
                    <textarea id="content" name="content" 
                              class="form-textarea form-textarea-editor" 
                              placeholder="Enter the announcement content here..." 
                              required style="resize: none;"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='announcements.php'">Cancel</button>
                    <div>
                        <button type="submit" name="publish" value="1" class="btn btn-primary">Publish Announcement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
   
</body>
</html>