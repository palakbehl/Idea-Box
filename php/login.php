<?php
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required';
    } elseif (!validateEmail($email)) {
        $response['message'] = 'Invalid email format';
    } else {
        try {
            // Check if user exists
            $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                $response['success'] = true;
                $response['message'] = 'Login successful!';
            } else {
                $response['message'] = 'Invalid email or password';
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
    header('Location: /IdeaBox/dashboard.php');
} else {
    $_SESSION['error_message'] = $response['message'];
    header('Location: /IdeaBox/login.php');
}
exit();
?>