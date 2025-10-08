<?php
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $response['message'] = 'All fields are required';
    } elseif (!validateEmail($email)) {
        $response['message'] = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $response['message'] = 'Passwords do not match';
    } else {
        try {
            // Check if email already exists
            $existingUser = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            
            if ($existingUser) {
                $response['message'] = 'Email already registered';
            } else {
                // Hash password and create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $db->query(
                    "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
                    [$name, $email, $hashedPassword]
                );
                
                if ($stmt) {
                    $response['success'] = true;
                    $response['message'] = 'Registration successful! You can now login.';
                    
                    // Auto-login the user
                    $userId = $db->lastInsertId();
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                } else {
                    $response['message'] = 'Registration failed. Please try again.';
                }
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Only return JSON when explicitly requested via query param
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    jsonResponse($response);
}

// If it's a regular form submission, redirect with message
if ($response['success']) {
    $_SESSION['success_message'] = $response['message'];
    header('Location: /IdeaBox/dashboard.php');
} else {
    $_SESSION['error_message'] = $response['message'];
    header('Location: /IdeaBox/register.php');
}
exit();
?>