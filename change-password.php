<?php
require_once 'config.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $userId = $_SESSION['user_id'];
    
    // Validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $response['message'] = 'All password fields are required';
    } elseif (strlen($newPassword) < 6) {
        $response['message'] = 'New password must be at least 6 characters long';
    } elseif ($newPassword !== $confirmPassword) {
        $response['message'] = 'New passwords do not match';
    } else {
        try {
            // Get current user data
            $user = $db->fetchOne("SELECT password FROM users WHERE id = ?", [$userId]);
            
            if ($user && password_verify($currentPassword, $user['password'])) {
                // Hash new password and update
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $stmt = $db->query(
                    "UPDATE users SET password = ? WHERE id = ?",
                    [$hashedPassword, $userId]
                );
                
                if ($stmt) {
                    $response['success'] = true;
                    $response['message'] = 'Password changed successfully!';
                } else {
                    $response['message'] = 'Failed to change password. Please try again.';
                }
            } else {
                $response['message'] = 'Current password is incorrect';
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