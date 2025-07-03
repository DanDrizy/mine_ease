<?php
// error_reporting(0);
// Database connection
include_once '../config.php';

$i = 1;
$sum = 0;


// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $unity_price = (float)$_POST['unity_price'];
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    // Validate inputs
    if (empty($item_name) || empty($category) || $quantity <= 0 || $unity_price <= 0 || empty($location)) {
        $message = 'Please fill all fields with valid values.';
        $messageType = 'error';
    } else {
        // Insert into database
        $sql = "INSERT INTO stock_items (item_name, category, quantity, unity_price, location) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssids", $item_name, $category, $quantity, $unity_price, $location);

            if (mysqli_stmt_execute($stmt)) {
                $message = 'Item added successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error adding item: ' . mysqli_error($conn);
                $messageType = 'error';
            }

            mysqli_stmt_close($stmt);
        } else {
            $message = 'Error preparing statement: ' . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}

// Fetch existing stock items for display
$stock_items = [];
$sql = "SELECT * FROM stock_items ORDER BY item_name ASC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stock_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="style/ad_style.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="style/inventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: white;
            padding: 20px;
            margin-top: 10rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            max-width: 800px;
            text-align: left;

        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
            margin: 20px 0 20px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .message {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }

    </style>
</head>

<body>
    <?php include 'main/slidebar.php'; ?>

    <div class="stock-dashboard">
        <!-- Add New Item Form -->
        <center>
            <div class="form-container">
            <h2><i class="fas fa-plus-circle"></i> Add New Stock Item</h2>
            <form method="POST" action="backend/stock-backend.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="item_name"><i class="fas fa-tag"></i> Item Name *</label>
                        <input type="text" id="item_name" name="item_name" required placeholder="Enter item name">
                    </div>
                    <div class="form-group">
                        <label for="category"><i class="fas fa-list"></i> Category *</label>
                        <select id="category" name="category" required>
                            <option value="" hidden>Select Category</option>
                            <option value="Diamonds">Diamonds</option>
                            <option value="Gold">Gold</option>
                            <option value="Precious Stones">Precious Stones</option>
                            <option value="Semi-Precious Stones">Semi-Precious Stones</option>
                            <option value="Ornamental Stones">Ornamental Stones</option>
                            <option value="Organic Gemstones">Organic Gemstones</option>
                            <option value="Colored Stones">Colored Stones</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity"><i class="fas fa-cubes"></i> Quantity *</label>
                        <input type="number" id="quantity" name="quantity" required min="1" placeholder="Enter quantity">
                    </div>
                    <div class="form-group">
                        <label for="unity_price"><i class="fas fa-dollar-sign"></i> Unit Price *</label>
                        <input type="number" id="unity_price" name="unity_price" required min="0" step="0.01" placeholder="Enter unit price">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location"><i class="fas fa-map-marker-alt"></i> Location *</label>
                        <input type="text" id="location" name="location" required placeholder="Enter storage location">
                    </div>
                </div>

                <button type="submit" name="add_item" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Item to Stock
                </button>
            </form>
        </div>
        </center>

        <!-- Stock Items Display -->
       

</body>

</html>

<?php mysqli_close($conn); ?>