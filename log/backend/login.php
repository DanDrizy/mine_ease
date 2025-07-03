<?php
session_start();

include '../../config.php';


if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    echo $email;
    echo $password;

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    $fetch = mysqli_fetch_array($result);
    $num = mysqli_num_rows($result);

    echo "<script>console.log('Login attempt with email: $email');</script>";
    if ($num > 0) {
        // $user = mysqli_fetch_assoc($result);
        // session_start();
        // $_SESSION['username'] = $user['username'] ?? '';
        // $_SESSION['email'] = $user['email'] ?? '';

        if ($fetch['user_type'] == 'admin') {


            echo "<script>window.location.href='../../admin/home.php';</script>";
            echo "<script>alert('Login successful');</script>";
            $_SESSION['user_id'] = $fetch['id'] ?? 0;


        } elseif ($fetch['user_type'] == 'hr') {


            echo "<script>window.location.href='../../hr/dashboard.php';</script>";
            echo "<script>alert('Login successful');</script>";
            $_SESSION['user_id'] = $fetch['id'] ?? 0;


        } elseif ($fetch['user_type'] == 'employee') {


            echo "<script>window.location.href='../../employee/index.php';</script>";
            echo "<script>alert('Login successful');</script>";
            $_SESSION['user_id'] = $fetch['id'] ?? 0;


        } elseif ($fetch['user_type'] == 'stock') {


            echo "<script>window.location.href='../../stock/dashboard.php';</script>";
            echo "<script>alert('Login successful');</script>";
            $_SESSION['user_id'] = $fetch['id'] ?? 0;



        } elseif ($fetch['user_type'] == 'stakeholder') {


            echo "<script>window.location.href='../../stakeholder/dashboard.php';</script>";
            echo "<script>alert('Login successful');</script>";


        } else {


            echo "<script>alert('Access Denied');</script>";
            echo "<script>window.location.href='../log.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password');</script>";
        echo "<script>window.location.href='../log.php';</script>";
    }
}
