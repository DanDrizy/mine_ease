<?php 

include '../../config.php';

// Check if the form is submitted
if(isset($_POST['signup'])){

    // Get the form data
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $user_type = $_POST['user_type'];
    $date_time = date("Y-m-d H:i:s");

    try{

         if($password !== $cpassword) { 
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
        echo "<script>window.location.href='../log.php';</script>";
        exit();
    }

    // Check if the email already exists
    $check_email_query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check_email_query);

    if(mysqli_num_rows($result) > 0){
        echo "<script>alert('Email already exists. Please use a different email.');</script>";
        echo "<script>window.location.href='../log.php';</script>";
    } else {
        // Insert the new user into the database
        $insert_query = "INSERT INTO users (name, username, email, password, phone, registration_date, department, user_type) VALUES ('$name', '$username', '$email', '$password', '$phone', '$date_time', '$department', '$user_type')";
        
        if(mysqli_query($conn, $insert_query)){
            echo "<script>alert('Registration successful! You can now log in.');</script>";
            echo "<script>window.location.href='../log.php';</script>";
        } else {
            echo "<script>alert('Error: Could not register user. Please try again later.');</script>";
            echo "<script>window.location.href='../log.php';</script>";
        }
    }

    } catch(Exception $e){
        echo "<script>alert('There was a problem in registering please try again');</script>";
        echo "<script>window.location.href='../log.php';</script>";
        exit();
    }

   
}

?>