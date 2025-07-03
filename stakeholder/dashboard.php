<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stakeholders Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <style></style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
    .active_dashboard {
        background-color: #007bff;
        color: white;
    }
</style>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $page_name = "Dashboard";

        // Start session and include database connection
        session_start();
        require_once '../config.php';

        include 'main/sidebar.php';

        // Fetch financial data
        $invested = 0;
        $profit = 0;
        // $exported = 0;

        // Calculate total inventory value
        $inventory_query = "
        SELECT 
        stock_items.*,
        SUM(stockout.quantity) AS stockout_quantity,
        SUM(stockout.unit_price) AS stockout_total_price,
        SUM(stockout.unit_price) AS stockout_unit_price,
        SUM(stock_items.total_value) AS total_value,
        (SUM(stockout.quantity )* SUM(stockout.unit_price)) - SUM(stock_items.total_value) AS total_all
        FROM stock_items
        LEFT JOIN stockout ON stock_items.id = stockout.in_id
        ";
        $inventory_result = $conn->query($inventory_query);
        if ($inventory_result && $inventory_row = $inventory_result->fetch_assoc()) {
            $invested = $inventory_row['total_value'];
            $loss_profit = $inventory_row['total_all'];
            $exported = $inventory_row['stockout_total_price'];



        }

        // Calculate total salaries (simplified profit calculation)
        $salary_query = "SELECT SUM(salary) as total_salary FROM users";
        $salary_result = $conn->query($salary_query);
        if ($salary_result && $salary_row = $salary_result->fetch_assoc()) {
            $invested_total = $invested < 0 ? $invested - ($salary_row['total_salary'] ?? 0) : $invested + ($salary_row['total_salary'] ?? 0);
            $profit =  $exported - $invested_total;
        }

        // Fetch operation data
        $hr_count = 0;
        $dept_count = 0;
        $employee_count = 0;
        $location_count = 0;

        // Count HR (assuming user_type = 'HR' for human resources)
        $hr_query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'HR'";
        $hr_result = $conn->query($hr_query);
        if ($hr_result && $hr_row = $hr_result->fetch_assoc()) {
            $hr_count = $hr_row['count'];
        }

        // Count departments (assuming unique department names)
        $dept_query = "SELECT COUNT(DISTINCT department) as count FROM users";
        $dept_result = $conn->query($dept_query);
        if ($dept_result && $dept_row = $dept_result->fetch_assoc()) {
            $dept_count = $dept_row['count'];
        }

        // Count all employees
        $emp_query = "SELECT COUNT(*) as count FROM users";
        $emp_result = $conn->query($emp_query);
        if ($emp_result && $emp_row = $emp_result->fetch_assoc()) {
            $employee_count = $emp_row['count'];
        }

        // Count locations (assuming unique site names)
        $loc_query = "SELECT COUNT(DISTINCT site) as count FROM users";
        $loc_result = $conn->query($loc_query);
        if ($loc_result && $loc_row = $loc_result->fetch_assoc()) {
            $location_count = $loc_row['count'];
        }

        // Fetch investment data by category
        $investment_data = [];
        $inv_query = "SELECT category,location, SUM(total_value) as total FROM stock_items GROUP BY category ORDER BY total DESC";
        $inv_result = $conn->query($inv_query);
        if ($inv_result) {
            while ($row = $inv_result->fetch_assoc()) {
                $investment_data[] = $row;
            }
        }

        // Fetch latest announcement
        $announcement = [
            'content' => 'No announcements available.'
        ];
        $ann_query = "SELECT * FROM announcements WHERE type = 'stake' OR type = 'All'  ORDER BY created_at DESC Limit 2";
        $sql = mysqli_query($conn, $ann_query);

        // $ann_result = $conn->query($ann_query);
        // if ($ann_result && $ann_row = $ann_result->fetch_assoc()) {
        //     $announcement = $ann_row;
        // }
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'main/header.php'; ?>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Financial Status Card -->
                <div class="dashboard-card">
                    <div class="card-title">Financial Status</div>
                    <div class="financial-status">
                        <div class="financial-box invested">
                            <div>
                                <div class="financial-title">Invested:</div>
                                <div class="financial-value">Frw <?php echo number_format($invested_total,2); ?></div>
                            </div>
                            <div class="financial-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                        </div>

                        <div class="financial-box profit">
                            <div class="financial-title">Profit / Loss</div>
                            <div class="financial-value">Frw <?php echo number_format($profit,2); ?></div>
                            <div class="chart-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>

                        <div class="financial-box exported">
                            <div>
                                <div class="financial-title">Exported</div>
                                <div class="financial-value">Frw <?php echo number_format($exported,2); ?></div>
                            </div>
                            <div class="financial-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operation Status Card -->
                <div class="dashboard-card">
                    <div class="card-title">Operation Status</div>
                    <div class="operation-status">
                        <div class="operation-box">
                            <div class="operation-icon blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="operation-title">Human Resource</div>
                                <div class="operation-value"><?php echo $hr_count; ?> people</div>
                            </div>
                        </div>

                        <div class="operation-box">
                            <div class="operation-icon green">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div>
                                <div class="operation-title">Department</div>
                                <div class="operation-value"><?php echo $dept_count; ?> departments</div>
                            </div>
                        </div>

                        <div class="operation-box">
                            <div class="operation-icon green">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <div>
                                <div class="operation-title">Employees</div>
                                <div class="operation-value"><?php echo $employee_count; ?> people</div>
                            </div>
                        </div>

                        <div class="operation-box">
                            <div class="operation-icon green">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <div class="operation-title">Locations</div>
                                <div class="operation-value"><?php echo $location_count; ?> sites</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Investment Status Card -->
                <div class="dashboard-card">
                    <div class="card-title">Investment Location Status</div>
                    <table class="investment-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($investment_data as $investment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($investment['location']); ?></td>
                                    <td>Frw <?php echo number_format($investment['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Announcement Card -->
                <div class="dashboard-card">
                    <div class="card-title">Announcement</div>
                    <?php while($announcement = mysqli_fetch_assoc($sql)): ?>
                    <div class="announcement-content">
                        <div class="header"><h3> <?php echo $announcement['title'] ?? 'Noo' ?> </h3> <h5><?php echo htmlspecialchars($announcement['type']); ?></h5> </div>
                        <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add loading animation
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Simulate loading time
        //     const body = document.querySelector('body');
        //     body.style.opacity = '0';

        //     setTimeout(() => {
        //         body.style.transition = 'opacity 0.5s ease';
        //         body.style.opacity = '1';
        //     }, 500);
        // });

        // Notification bell functionality
        const notificationBell = document.querySelector('.notification-bell');
    </script>
</body>

</html>