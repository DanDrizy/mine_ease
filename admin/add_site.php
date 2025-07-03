<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Site Form</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #315770;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
            height: 88vh;
            margin-top: 10px;
        }
        
        .form-header {
            background-color: #7e57c2;
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        input:focus, select:focus {
            border-color: #7e57c2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(126, 87, 194, 0.2);
        }
        
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #43a047;
        }
        
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h2>Inventory Site Details</h2>
        </div>
        
        <form id="siteForm">
            <div class="form-group">
                <label for="siteName">Site Name</label>
                <input type="text" id="siteName" name="siteName" placeholder="Enter site name" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="Enter location" required>
            </div>
            
            <div class="form-group">
                <label for="manager">Manager</label>
                <input type="text" id="manager" name="manager" placeholder="Enter manager name" required>
            </div>
            
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="tel" id="contact" name="contact" placeholder="+250 XXX XXX XXX" pattern="\+250 [0-9]{3} [0-9]{3} [0-9]{3}" required>
            </div>
            
            <div class="form-group">
                <label for="inventoryValue">Inventory Value ($)</label>
                <input type="number" id="inventoryValue" name="inventoryValue" placeholder="0.00" step="0.01" min="0" required>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Site</button>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('siteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Form submission logic would go here
            alert('Site information saved successfully!');
        });
    </script>
</body>
</html>