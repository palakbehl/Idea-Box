<?php
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    
    // Validation
    if (empty($email)) {
        $response['message'] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $response['message'] = 'Invalid email format';
    } else {
        try {
            // Check if user exists
            $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            
            if ($user) {
                // Generate new temporary password
                $newPassword = generateRandomString(8);
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password in database
                $stmt = $db->query(
                    "UPDATE users SET password = ? WHERE email = ?",
                    [$hashedPassword, $email]
                );
                
                if ($stmt) {
                    $response['success'] = true;
                    $response['message'] = "Password reset successful! Your new password is: <strong>$newPassword</strong><br>Please login with this password and change it immediately.";
                } else {
                    $response['message'] = 'Failed to reset password. Please try again.';
                }
            } else {
                // Don't reveal if email exists or not for security
                $response['success'] = true;
                $response['message'] = 'If the email exists in our system, a password reset has been processed.';
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
header('Location: /IdeaBox/forgot-password.php');
exit();
?>