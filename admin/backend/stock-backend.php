<?php 

include'../../config.php';

if(isset($_POST['add_item'])){
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unity_price = $_POST['unity_price'];
    $location = $_POST['location'];
    
    $sql = "INSERT INTO stock_items (item_name, category, quantity, unit_price, location) VALUES ('$item_name', '$category', '$quantity', '$unity_price', '$location')";
    $result = mysqli_query($conn, $sql);
    if($result){

        echo"<script>  alert('Item added successfully'); window.location.href='../inventory.php'; </script>";
        
    }else{
                echo"<script>  alert('Error'); window.location.href='../inventory.php'; </script>";
    }
}




?>