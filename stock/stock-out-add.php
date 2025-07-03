<?php
    // Database connection
    require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager - Add Stock Out</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your original styles remain unchanged */
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
        
        .form input, .form select, .form textarea {
            padding: 12px;
            border-radius: 5px;
            border: 2px solid #ddd;
            width: 100%;
            font-size: 16px;
        }
        
        .form input:focus, .form select:focus, .form textarea:focus {
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
        
        .stock-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #7A4B9D;
        }
        
        .stock-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php 
    $page_name = "Stock Level / Stock out";
    include 'main/sidebar.php'; 
    
    // Initialize variables
    $message = '';
    $message_type = '';
    $stock_items = array();
    
    // Fetch existing stock items for dropdown
    $sql = "SELECT id, item_name, category, quantity, unit_price, location FROM stock_items WHERE status = 'in-stock'";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stock_items[] = $row;
        }
    }
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input
        $item_id = (int)$_POST['item_id'];
        $quantity_out = (int)$_POST['quantity'];
        
        // Validate required fields
        if ($item_id <= 0 || $quantity_out <= 0 ) {
            $message = 'Please fill in all required fields with valid values.';
            $message_type = 'error';
        } else {
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // 1. Get current stock item details
                $sql = "SELECT * FROM stock_items WHERE id = ? FOR UPDATE";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $current_item = $result->fetch_assoc();
                $stmt->close();
                
                if (!$current_item) {
                    throw new Exception("Selected item not found in stock.");
                }
                
                // Check if enough stock is available
                if ($current_item['quantity'] < $quantity_out) {
                    throw new Exception("Insufficient stock available. Current stock: " . $current_item['quantity']);
                }
                
                // Calculate values
                $unit_price = $current_item['unit_price'];
                $total_value = $quantity_out * $unit_price;
                
                // 2. Update original stock item quantity
                $new_quantity = $current_item['quantity'] - $quantity_out;
                $new_status = ($new_quantity > 0) ? 'in-stock' : 'out-of-stock';
                
                $update_sql = "UPDATE stock_items 
                              SET quantity = ?, status = ?, updated_at = NOW()
                              WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("isi", $new_quantity, $new_status, $item_id);
                $stmt->execute();
                $stmt->close();
                
                // 3. Create new stock-out record (negative quantity)
                $insert_sql = "INSERT INTO stock_items 
                              (item_name, category, quantity, unit_price, total_value, location, status, created_at, updated_at)
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $out_quantity = -$quantity_out; // Negative quantity for stock out
                $out_status = 'out-of-stock';
                $out_item_name = $current_item['item_name'] . ' (OUT)';
                
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param(
                    "ssiddss",
                    $out_item_name,
                    $current_item['category'],
                    $out_quantity,
                    $unit_price,
                    $total_value,
                    $current_item['location'],
                    $out_status
                );
                $stmt->execute();
                $stmt->close();
                
                // Commit transaction
                $conn->commit();
                
                $message = 'Stock out recorded successfully! New stock-out entry created.';
                $message_type = 'success';
                
                // Clear form fields
                $_POST = array();
            } catch (Exception $e) {
                $conn->rollback();
                $message = 'Error: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        
        <div class="bottom-cards-stock">
            <div id="stockOutTable" class="stock-table stock-out-table active">
                <h3 class="table-title">Add STOCK-OUT</h3>
                
                <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <form class="form" method="POST" action="">
                        <div class="form-group">
                            <label for="item_id">Item Name*</label>
                            <select id="item_id" name="item_id" required>
                                <option value="">-- Select Item --</option>
                                <?php foreach ($stock_items as $item): ?>
                                <option value="<?php echo $item['id']; ?>" 
                                    data-category="<?php echo htmlspecialchars($item['category']); ?>"
                                    data-location="<?php echo htmlspecialchars($item['location']); ?>"
                                    data-price="<?php echo $item['unit_price']; ?>"
                                    <?php echo (isset($_POST['item_id']) && $_POST['item_id'] == $item['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['item_name']); ?> 
                                    (Available: <?php echo $item['quantity']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="stockDetails" class="stock-info" style="display: none;">
                            <p><strong>Current Stock:</strong> <span id="currentStock">0</span></p>
                            <p><strong>Category:</strong> <span id="itemCategory">-</span></p>
                            <p><strong>Location:</strong> <span id="itemLocation">-</span></p>
                            <p><strong>Unit Price:</strong> Frw <span id="unitPrice">0.00</span></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Quantity to Release*</label>
                            <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" min="1" 
                                   value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Total Value</label>
                            <input type="text" id="total_value" readonly value="Frw 0.00">
                        </div>
                        
                        <button type="submit">Record Stock Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get item details when selection changes
        document.getElementById('item_id').addEventListener('change', function() {
            const itemId = this.value;
            const stockDetails = document.getElementById('stockDetails');
            
            if (itemId) {
                const selectedOption = this.options[this.selectedIndex];
                const itemText = selectedOption.text;
                
                // Extract current stock (format: "Item Name (Available: X)")
                const stockMatch = itemText.match(/\(Available: (\d+)\)/);
                const currentStock = stockMatch ? stockMatch[1] : '0';
                
                // Get other item details from data attributes
                const unitPrice = selectedOption.dataset.price || '0.00';
                const category = selectedOption.dataset.category || '-';
                const location = selectedOption.dataset.location || '-';
                
                // Update display
                document.getElementById('currentStock').textContent = currentStock;
                document.getElementById('unitPrice').textContent = unitPrice;
                document.getElementById('itemCategory').textContent = category;
                document.getElementById('itemLocation').textContent = location;
                
                stockDetails.style.display = 'block';
                
                // Update max quantity validation
                document.getElementById('quantity').max = currentStock;
            } else {
                stockDetails.style.display = 'none';
            }
            
            calculateTotal();
        });
        
        // Calculate total value in real-time
        document.getElementById('quantity').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const unitPriceText = document.getElementById('unitPrice').textContent || '0';
            const unitPrice = parseFloat(unitPriceText.replace(/[^0-9.-]/g, ''));
            
            document.getElementById('total_value').value = 'Frw ' + (quantity * unitPrice).toFixed(2);
        }
        
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const quantity = parseInt(document.getElementById('quantity').value);
            const maxQuantity = parseInt(document.getElementById('quantity').max) || 0;
            
            if (quantity <= 0) {
                alert('Quantity must be greater than zero.');
                e.preventDefault();
            }
            
            if (quantity > maxQuantity) {
                alert('Quantity cannot exceed available stock.');
                e.preventDefault();
            }
        });
        
        // Initialize if form was submitted with errors
        <?php if (isset($_POST['item_id'])): ?>
        document.getElementById('item_id').dispatchEvent(new Event('change'));
        <?php endif; ?>
    </script>
</body>
</html>