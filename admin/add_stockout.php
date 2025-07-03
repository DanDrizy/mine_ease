<?php 

include'../config.php';


if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    
    $sql = "SELECT id,item_name, category, quantity, unit_price, location 
            FROM stock_items 
            WHERE item_name LIKE '%$search%' 
            AND quantity > 0 
            ORDER BY item_name ASC 
            LIMIT 10";
    
    $result = $conn->query($sql);
    $items = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($items);
    exit;
}

// Handle form submission
if ($_POST && isset($_POST['submit_stockout'])) {
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $quantity_out = (int)$_POST['quantity_out'];
    $id = (int)$_POST['id'];
    $unit_price = (float)$_POST['unit_price'];
    $location = $conn->real_escape_string($_POST['location']);
    $reason = $conn->real_escape_string($_POST['reason']);
    
    // Check if item exists and has enough quantity
    $check_sql = "SELECT quantity FROM stock_items WHERE id = '$id'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $current_quantity = $row['quantity'];
        
        if ($current_quantity >= $quantity_out) {
            // Update stock quantity
            $new_quantity = $current_quantity - $quantity_out;
            $update_sql = "UPDATE stock_items SET quantity = $new_quantity WHERE id = '$id'";

            $insert_stockout = "INSERT INTO stockout (in_id,quantity,unit_price, reason,created_at) VALUES ('$id', $quantity_out, $unit_price,'$reason', NOW())";
            
            if ($conn->query($update_sql) === TRUE) {
                $sql = mysqli_query($conn, $insert_stockout);
                $success_message = "Stock-out processed successfully! $quantity_out units of $item_name removed from stock.";
            } else {
                $error_message = "Error updating stock: " . $conn->error;
            }
        } else {
            $error_message = "Insufficient stock! Available quantity: $current_quantity";
        }
    } else {
        $error_message = "Item not found in stock!";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add StockOut | Admin Department</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/stockout.css">
    <style>
        .active_inventory {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'main/slidebar.php';  ?>
    <?php include 'backend/add-stockout.php'; ?>

    
    <div class="main-content">

    <div class="container">
        <div class="header">
            <h1> <i class=" fa fa-box "></i> Stock-Out Form</h1>
        </div>

        <div class="form-container">
            

            <form method="POST" action="">
                <div class="form-group">
                    <label for="item_search">Search Item Name</label>
                    <div class="search-container">
                        <input type="text" id="item_search" class="form-control" 
                               placeholder="Start typing to search for items..." autocomplete="off">
                        <div id="search_results" class="search-results"></div>
                    </div>
                </div>

                <input type="hidden" name="item_name" id="selected_item_name">
                <input type="hidden" name="location" id="selected_location">
                <input type="hidden" name="id" id="id">

                <div id="selected_item_info" class="selected-item-info">
                    <h4>Selected Item Details</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">ID:</span> <span id="info_id"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Item:</span> <span id="info_name"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Category:</span> <span id="info_category"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Available:</span> <span id="info_quantity"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Location:</span> <span id="info_location"></span>
                        </div>
                    </div>
                </div>
                <br>
                <br>

                <div class="form-row">
                    <div class="form-group">
                        <label for="unit_price">Unit Price ($)</label>
                        <input type="number" name="unit_price" id="unit_price" class="form-control" 
                               step="0.01" min="0" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="quantity_out">Quantity to Remove</label>
                        <input type="number" name="quantity_out" id="quantity_out" class="form-control" 
                               min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Stock-Out</label>
                    <select name="reason" id="reason" class="form-control" required>
                        <option value="">Select reason...</option>
                        <option value="sale">Sale</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                        <option value="transfer">Transfer to another location</option>
                        <option value="return">Return to supplier</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <button type="submit" name="submit_stockout" class="btn">
                    Process Stock-Out
                </button>
            </form>
        </div>
    </div>

    </div>
   
</body>
<script src="js/stockout.js"></script>
</html>