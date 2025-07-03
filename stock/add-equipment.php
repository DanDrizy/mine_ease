<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        /* Original styles */
        .active-equipment-tracking {
            background-color: #7A4B9D;
            color: white;
        }

        .dashboard-cards-stock {
            width: 100%;  
            margin: 10px 0px 20px;
        }
        .card-stock {
            background: #D0E0FF;
            width: 100%;
            overflow: hidden;
        }
        .stock-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
        }
        .stock-in, .stock-out {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #4768A8;
            width: 40%;
            padding: 10px;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .stock-in.active, .stock-out.active {
            background: #7A4B9D;
        }
        .stock-in:hover, .stock-out:hover {
            background: #5a78b8;
        }
        .detail {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail img {
            width: 40px;
            height: 40px;
        }
        .bottom-cards-stock {
            background: #D0E0FF;
            width: 100%;
            height: 70vh;
            border-radius: 10px;
            padding: 20px;
            overflow: auto;
        }
        .search-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #4768A8;
        }
        .search-box .search {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .search-input {
            padding: 10px;
            border-radius: 5px;
            border: none;
            width: 300px;
            border: 2px solid transparent;
        }
        .search-input:focus {
            outline: none;
            border: 2px solid #7A4B9D;
        }
        .search-button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background:rgb(186, 95, 255);
            color: white;
            cursor: pointer;
        }
        .adding button {
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid #5a3e7c;
            background: white;
            color:rgb(186, 95, 255);
            cursor: pointer;
        }
        .stock-table {
            width: 100%;
            overflow: auto;
        }
        .stock-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .stock-table th, .stock-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .stock-table th {
            background: #4768A8;
            color: white;
        }
        .stock-table tr:hover {
            background: #f1f1f1;
        }
        .stock-table button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            background: #7A4B9D;
            color: white;
            cursor: pointer;
        }
        .stock-table button:hover {
            background: #5a3e7c;
        }
        
        .stock-in-table, .stock-out-table {
            display: none;
        }
        
        .stock-in-table.active, .stock-out-table.active {
            display: block;
        }
        
        .table-title {
            margin-top: 0;
            margin-bottom: 15px;
            color: #4768A8;
            font-size: 1.5rem;
        }
        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form input, .form select, .form textarea {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 2px solid transparent;
            width: 100%;
        }
        .form input:focus, .form select:focus, .form textarea:focus {
            outline: none;
            border: 2px solid #7A4B9D;
        }
        .form button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background: #7A4B9D;
            color: white;
            cursor: pointer;
        }
        .form button:hover {
            background: #5a3e7c;
        }
        .form button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .form button:disabled:hover {
            background: #ccc;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

   <!-- Sidebar -->
    <?php 
    
    $page_name = "Equipment Tracking / Add Equipment";
    include 'main/sidebar.php'; 
    
    // Process form submission
    $message = '';
    $messageClass = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Get form data
            $item_name = $_POST['item_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $serial_number = $_POST['serial_number'] ?? '';
            $location = $_POST['location'] ?? '';
            $department_id = $_POST['department_id'] ?? '';
            $notes = $_POST['notes'] ?? '';
            $date_added = $_POST['date_added'] ?? date('Y-m-d H:i:s');
            
            // Validate required fields
            if (empty($item_name) || empty($location) || empty($department_id)) {
                throw new Exception("Item name, location, and department are required fields.");
            }
            
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO equipment 
                                  (item_name, description, serial_number, location, department_id, notes, date_added) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            // Bind parameters
            $stmt->bind_param("ssssiss", $item_name, $description, $serial_number, $location, $department_id, $notes, $date_added);
            
            // Execute statement
            if ($stmt->execute()) {
                $message = "Equipment added successfully!";
                $messageClass = "success";
            } else {
                throw new Exception("Error adding equipment: " . $stmt->error);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageClass = "error";
        }
    }
    
    // Fetch departments for dropdown
    $departments = [];
    $result = $conn->query("SELECT department FROM users");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $departments[$row['department']] = $row['department'];
        }
        $result->free();
    }
    ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'main/header.php'; ?>
        <!-- Dashboard Cards -->
       
        <div class="bottom-cards-stock">
            
            <!-- Display message if any -->
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageClass; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Stock-In Table -->
            <div id="stockInTable" class="stock-table stock-in-table active">
                <h3 class="table-title">Add Equipment</h3>

                <div class="form">
                    <form action="" method="POST">
                        <input type="text" name="item_name" placeholder="Equipment Name*" required>
                        <textarea name="description" placeholder="Description"></textarea>
                        <input type="text" name="serial_number" placeholder="Serial Number">
                        <input type="text" name="location" placeholder="Location*" required>
                        
                        <select name="department_id" required>
                            <option value="">Select Department*</option>
                            <?php foreach ($departments as $id => $name): ?>
                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <textarea name="notes" placeholder="Notes"></textarea>
                        <input type="datetime-local" name="date_added" placeholder="Registered Date">
                        <button type="submit">Register Equipment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<?php
// Close database connection
$conn->close();
?>