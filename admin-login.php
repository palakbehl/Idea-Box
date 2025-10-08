<?php
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($username) || empty($password)) {
        $response['message'] = 'Username and password are required';
    } else {
        try {
            // Check admin credentials
            $admin = $db->fetchOne("SELECT * FROM admins WHERE username = ?", [$username]);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                $response['success'] = true;
                $response['message'] = 'Admin login successful!';
            } else {
                $response['message'] = 'Invalid admin credentials';
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

// If it's an AJAX request, return JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    jsonResponse($response);
}

// If it's a regular form submission, redirect with message
if ($response['success']) {
    $_SESSION['success_message'] = $response['message'];
    header('Location: ../admin/dashboard.php');
} else {
    $_SESSION['error_message'] = $response['message'];
    header('Location: ../admin/login.php');
}
exit();
?>