<?php 

include '../config.php';

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');
$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;
$cur_date = date('Y-m-d H:i:s');

// if ($id && $status) {

    $status = ($status === 'pay') ? '1' : '0';

    if($month == date('m') && $year == date('Y')) {
    $status = $status ? '1' : '0';
    $stmt = mysqli_query($conn,"UPDATE payroll SET is_paid = '$status', updated_at = '$cur_date' WHERE user_id =  '$id'");   
    
    if (!$stmt) {
        die("Error updating payroll: " . mysqli_error($conn));
    }else {
        // Successfully updated
        echo " <script> alert('Payroll updated successfully'); window.location.href = 'payroll.php'; </script>";
    }

    }else{

    $month = date('m');
    $year = date('Y');

    $stmt = mysqli_query($conn,"INSERT INTO `payroll`
    (`user_id`, `month`, `year`, `is_paid`, `payment_date`)
     VALUES ('$id','$month','$year','$status','$cur_date')");   

    if (!$stmt) {
        die("Error inserting payroll: " . mysqli_error($conn));
    }else {
        // Successfully updated
        echo " <script> alert('Payroll updated successfully'); window.location.href = 'payroll.php'; </script>";
    }


    }
    


    
    
// }

?>