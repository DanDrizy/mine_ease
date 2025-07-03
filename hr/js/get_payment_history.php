<?php
// get_payment_history.php
header('Content-Type: application/json');
require_once '../config.php';

// Check if request is POST and has JSON data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || empty($input['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$user_id = intval($input['user_id']);

try {
    // Get current year and determine the range of months/years to check
    $current_year = date('Y');
    $current_month = date('n');
    $start_year = $current_year - 2; // Check last 2 years + current year
    
    // Generate all possible month/year combinations
    $all_periods = [];
    for ($year = $start_year; $year <= $current_year; $year++) {
        $start_month = ($year == $start_year) ? 1 : 1;
        $end_month = ($year == $current_year) ? $current_month : 12;
        
        for ($month = $start_month; $month <= $end_month; $month++) {
            $all_periods[] = [
                'month' => $month,
                'year' => $year,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1))
            ];
        }
    }
    
    // Get actual payment records for this user
    $payment_query = "SELECT month, year, is_paid, payment_date, created_at 
                      FROM payroll 
                      WHERE user_id = ? 
                      AND year >= ?  AND is_paid = 1
                      ORDER BY year DESC, month DESC";
    
    $stmt = mysqli_prepare($conn, $payment_query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $start_year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $existing_payments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        $existing_payments[$key] = $row;
    }
    
    // Combine all periods with actual payment data
    $payment_history = [];
    $summary = ['total' => 0, 'paid' => 0, 'unpaid' => 0];
    
    foreach ($all_periods as $period) {
        $key = $period['year'] . '-' . str_pad($period['month'], 2, '0', STR_PAD_LEFT);
        
        $payment_record = [
            'month' => $period['month'],
            'year' => $period['year'],
            'month_name' => $period['month_name'],
            'exists' => false,
            'is_paid' => 0,
            'payment_date' => null
        ];
        
        if (isset($existing_payments[$key])) {
            $payment_record['exists'] = true;
            $payment_record['is_paid'] = $existing_payments[$key]['is_paid'];
            $payment_record['payment_date'] = $existing_payments[$key]['payment_date'];
        }
        
        $payment_history[] = $payment_record;
        $summary['total']++;
        
        if ($payment_record['is_paid'] == 1) {
            $summary['paid']++;
        } else {
            $summary['unpaid']++;
        }
    }
    
    // Sort by year and month (most recent first)
    usort($payment_history, function($a, $b) {
        if ($a['year'] != $b['year']) {
            return $b['year'] - $a['year'];
        }
        return $b['month'] - $a['month'];
    });
    
    echo json_encode([
        'success' => true,
        'payments' => $payment_history,
        'summary' => $summary
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>