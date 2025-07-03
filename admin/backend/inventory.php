<?php

if ($_POST) {
    if (isset($_POST['delete_item'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $table = mysqli_real_escape_string($conn, $_POST['table']);
        
        if ($table == 'stock_items') {
            $delete_query = "DELETE FROM `stock_items` WHERE id = '$id'";
        } else {
            $delete_query = "DELETE FROM `stockout` WHERE id = '$id'";
        }
        
        if (mysqli_query($conn, $delete_query)) {
            echo "<script>alert('Item deleted successfully!'); window.location.href = window.location.pathname;</script>";
        } else {
            echo "<script>alert('Error deleting item!');</script>";
        }
    }
    
    if (isset($_POST['update_item'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $in_id = mysqli_real_escape_string($conn, $_POST['in_id']);
        $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
        $quantity_out = mysqli_real_escape_string($conn, $_POST['quantity_out']);
        $unit_price = mysqli_real_escape_string($conn, $_POST['unit_price']);
        $table = mysqli_real_escape_string($conn, $_POST['table']);
        
        
        
        if ($table == 'stock_items') {

        
        

            $update_query = mysqli_query($conn,"UPDATE `stock_items` SET 
                            item_name = '$item_name', 
                            category = '$category', 
                            quantity = '$quantity', 
                            unit_price = '$unit_price' 
                            WHERE id = '$in_id'");

            echo "<script>alert('Item updated successfully! ');</script>";
            

           
            
        } else {


            $select_amount = mysqli_query($conn,"SELECT * FROM `stockout` WHERE id = '$id' ");
            $fetch = mysqli_fetch_assoc($select_amount);
            $amount = $fetch['quantity'];

            $select_amount = mysqli_query($conn,"SELECT * FROM `stock_items` WHERE id = '$in_id'");
            $fetch = mysqli_fetch_assoc($select_amount);
            $amount_in = $fetch['quantity'];

            $left_amount = $quantity_out - $amount;
            $new_amount = $amount - $left_amount;

            $in_ount = $amount_in - $left_amount;
        
            $update_query = mysqli_query($conn,"UPDATE `stockout` SET
                            quantity = '$quantity_out', 
                            unit_price = '$unit_price' 
                            WHERE id = '$id'");

            $update_query = mysqli_query($conn,"UPDATE `stock_items` SET 
                            item_name = '$item_name', 
                            category = '$category', 
                            quantity = '$in_ount', 
                            unit_price = '$unit_price' 
                            WHERE id = '$in_id'");

            echo "<script>alert('Item updated successfully! ');</script>";
           
        
        }
        
       
    }
}

// Get filter selection
$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : 'Stockin';

// Fetch data based on filter
if ($category_filter == 'Stockout') {
    $sele_items = mysqli_query($conn, "SELECT
    
     stockout.quantity as quantity_out,
     stock_items.quantity as quantity,
     stock_items.item_name as item_name,
     stock_items.category as category,
     stock_items.unit_price as unit_price,
     stockout.id as id,
     stockout.in_id as in_id,
     stockout.created_at as created_at
     FROM stockout,stock_items WHERE stockout.in_id = stock_items.id AND stockout.quantity > 0  ORDER BY stockout.created_at DESC ");
    $table_name = 'stockout';
    $table_title = 'Stock Out';
} else {
    $sele_items = mysqli_query($conn, "SELECT si.*, so.quantity as quantity_out,so.in_id as in_id
                    FROM stock_items si
                    LEFT JOIN stockout so ON si.id = so.in_id AND so.quantity > 0
                    ORDER BY si.created_at DESC

                                                        ");
    $table_name = 'stock_items';
    $table_title = 'Stock In';
}

$count = mysqli_num_rows($sele_items);

// Count stock in items
$select_stock_in = mysqli_query($conn, "SELECT * FROM `stock_items` WHERE quantity > 0 ");
$count_stock_in = mysqli_num_rows($select_stock_in);

// Count stock out items
$select_stock_out = mysqli_query($conn, "SELECT * FROM stockout WHERE quantity > 0");
$count_stock_out = mysqli_num_rows($select_stock_out);

// Calculate total sales from stock out
$sum_stockout = 0;
while($fetch_stock_out = mysqli_fetch_array($select_stock_out)){
    $sum_stockout += $fetch_stock_out['unit_price'] * $fetch_stock_out['quantity'];
}


?>