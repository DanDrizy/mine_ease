<?php

    // Database connection
    require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager - Add Stock In</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Original styles */
        .active-stock-levels {
            background-color: #7A4B9D;
            color: white;
        }

        .bottom-cards-stock {
            background: #D0E0FF;
            width: 100%;
            height: 75vh;
            border-radius: 10px;
            padding: 20px;
            overflow: auto;
        }
        
        .table-title {
            margin-top: 0;
            margin-bottom: 15px;
            color: #4768A8;
            font-size: 1.5rem;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .form label {
            font-weight: bold;
            color: #4768A8;
        }
        
        .form input, .form select {
            padding: 12px;
            border-radius: 5px;
            border: 2px solid #ddd;
            width: 100%;
            font-size: 16px;
        }
        
        .form input:focus, .form select:focus {
            outline: none;
            border: 2px solid #7A4B9D;
        }
        
        .form button {
            padding: 12px 20px;
            border-radius: 5px;
            border: none;
            background: #7A4B9D;
            color: white;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            transition: background 0.3s;
        }
        
        .form button:hover {
            background: #5a3e7c;
        }
        
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php 
    $page_name = "Stock Level / Stock in";
    include 'main/sidebar.php'; 
    
    // Initialize variables
    $message = '';
    $message_type = '';
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        
        // Sanitize and validate input
        $item_name = $conn->real_escape_string(trim($_POST['item_name']));
        $category = $conn->real_escape_string(trim($_POST['category']));
        $location = $conn->real_escape_string(trim($_POST['location']));
        $quantity = (int)$_POST['quantity'];
        $unit_price = (float)$_POST['unit_price'];
        $total_value = $quantity * $unit_price;
        $status = 'in-stock'; // Stock-in items
        
        // Validate required fields
        if (empty($item_name) || empty($category) || empty($location) || $quantity <= 0 || $unit_price <= 0) {
            $message = 'Please fill in all required fields with valid values.';
            $message_type = 'error';
        } else {
            // Insert into database
            $sql = "INSERT INTO stock_items (item_name, category, quantity, unit_price, total_value, location, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiddss", $item_name, $category, $quantity, $unit_price, $total_value, $location, $status);
            
            if ($stmt->execute()) {
                $message = 'Stock item added successfully!';
                $message_type = 'success';
                
                // Clear form fields
                $_POST = array();
            } else {
                $message = 'Error adding stock item: ' . $conn->error;
                $message_type = 'error';
            }
            
            $stmt->close();
        }
        
        $conn->close();
    }
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <div class="bottom-cards-stock">
            <div id="stockInTable" class="stock-table stock-in-table active">
                <h3 class="table-title">Add STOCK-IN</h3>
                
                <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <form class="form" method="POST" action="">
                        <div class="form-group">
                            <label for="item_name">Item Name*</label>
                            <input type="text" id="item_name" name="item_name" placeholder="Enter item name" 
                                   value="<?php echo isset($_POST['item_name']) ? htmlspecialchars($_POST['item_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category*</label>
                            <input type="text" id="category" name="category" placeholder="Enter category" 
                                   value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location*</label>
                            <input type="text" id="location" name="location" placeholder="Enter storage location" 
                                   value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Quantity*</label>
                            <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" min="1" 
                                   value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="unit_price">Unit Price*</label>
                            <input type="number" id="unit_price" name="unit_price" placeholder="Enter unit price" min="0.01" step="0.01" 
                                   value="<?php echo isset($_POST['unit_price']) ? htmlspecialchars($_POST['unit_price']) : ''; ?>" required>
                        </div>
                        
                        <button type="submit">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const quantity = document.getElementById('quantity').value;
            const unitPrice = document.getElementById('unit_price').value;
            
            if (quantity <= 0 || unitPrice <= 0) {
                alert('Quantity and unit price must be greater than zero.');
                e.preventDefault();
            }
        });
        
        // Calculate total value in real-time
        document.getElementById('quantity').addEventListener('input', calculateTotal);
        document.getElementById('unit_price').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
            // You could display this somewhere if you add a total value field
            // document.getElementById('total_value').value = (quantity * unitPrice).toFixed(2);
        }
    </script>
</body>
</html>