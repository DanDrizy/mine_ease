<?php
// includes/functions.php

/**
 * Get all employees from the database
 */
function getAllEmployees($conn) {
    $employees = [];
    
    $sql = "SELECT e.*, 
            p.salary, p.providence, p.tax, p.loan 
            FROM employees e 
            LEFT JOIN payroll p ON e.id = p.employee_id 
            ORDER BY e.name";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
    
    return $employees;
}

/**
 * Format MySQL date to readable format
 */
function formatDate($date) {
    return date('d F Y', strtotime($date));
}

/**
 * Get employee by ID
 */
function getEmployeeById($conn, $id) {
    $sql = "SELECT e.*, 
            p.salary, p.providence, p.tax, p.loan 
            FROM employees e 
            LEFT JOIN payroll p ON e.id = p.employee_id 
            WHERE e.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Update employee payroll information
 */
function updateEmployeePayroll($conn, $employeeId, $salary, $providence, $tax, $loan) {
    // First check if employee has payroll record
    $sql = "SELECT id FROM payroll WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // Update existing record
        $sql = "UPDATE payroll SET 
                salary = ?, 
                providence = ?, 
                tax = ?, 
                loan = ?,
                updated_at = NOW()
                WHERE employee_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddddi", $salary, $providence, $tax, $loan, $employeeId);
    } else {
        // Create new record
        $sql = "INSERT INTO payroll 
                (employee_id, salary, providence, tax, loan, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idddd", $employeeId, $salary, $providence, $tax, $loan);
    }
    
    return $stmt->execute();
}

/**
 * Calculate net pay
 */
function calculateNetPay($salary, $providence, $tax, $loan) {
    return $salary - $providence - $tax - $loan;
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}