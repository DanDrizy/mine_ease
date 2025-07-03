<?php 

include'../../config.php';

if(isset($_POST['publish'])){
    $title = $_POST['title'];
    $type = $_POST['type'];
    $loaction = $_POST['location'];
    $content = $_POST['content'];

    $insert = mysqli_query($conn,"INSERT INTO `announcements`(`title`, `type`, `location`, `content`) VALUES ('$title','$type','$loaction','$content')");

    if($insert){
        echo "<script>alert('Announcement added successfully')</script>";
        echo "<script>window.location.href='../announcements.php'</script>";
    }else{
        echo "<script>alert('Announcement not added')</script>";
        echo "<script>window.location.href='../announcements.php'</script>";
    }

}

    





?>