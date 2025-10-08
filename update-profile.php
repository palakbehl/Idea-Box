<?php
require_once 'config.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $userId = $_SESSION['user_id'];
    
    // Validation
    if (empty($name) || empty($email)) {
        $response['message'] = 'Name and email are required';
    } elseif (!validateEmail($email)) {
        $response['message'] = 'Invalid email format';
    } else {
        try {
            // Check if email is already taken by another user
            $existingUser = $db->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $userId]);
            
            if ($existingUser) {
                $response['message'] = 'Email already taken by another user';
            } else {
                // Update user profile
                $stmt = $db->query(
                    "UPDATE users SET name = ?, email = ? WHERE id = ?",
                    [$name, $email, $userId]
                );
                
                if ($stmt) {
                    // Update session data
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    $response['success'] = true;
                    $response['message'] = 'Profile updated successfully!';
                } else {
                    $response['message'] = 'Failed to update profile. Please try again.';
                }
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
} else {
    $_SESSION['error_message'] = $response['message'];
}
header('Location: profile.php');
exit();
?>