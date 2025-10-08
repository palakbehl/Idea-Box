<?php
require_once 'config.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $categoryId = (int)$_POST['category_id'];
    $userId = $_SESSION['user_id'];
    $fileName = null;
    
    // Validation
    if (empty($title) || empty($description) || empty($categoryId)) {
        $response['message'] = 'Title, description, and category are required';
    } elseif (strlen($title) > 200) {
        $response['message'] = 'Title must be 200 characters or less';
    } elseif (strlen($description) < 10) {
        $response['message'] = 'Description must be at least 10 characters long';
    } else {
        try {
            // Handle file upload if provided
            if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $fileName = uploadFile($_FILES['file']);
                } catch (Exception $e) {
                    $response['message'] = 'File upload error: ' . $e->getMessage();
                }
            }
            
            if ($response['message'] === '') {
                // Insert idea into database
                $stmt = $db->query(
                    "INSERT INTO ideas (user_id, title, description, category_id, file_path) VALUES (?, ?, ?, ?, ?)",
                    [$userId, $title, $description, $categoryId, $fileName]
                );
                
                if ($stmt) {
                    $response['success'] = true;
                    $response['message'] = 'Idea submitted successfully!';
                } else {
                    $response['message'] = 'Failed to submit idea. Please try again.';
                    // Delete uploaded file if database insert failed
                    if ($fileName) {
                        deleteFile($fileName);
                    }
                }
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
            // Delete uploaded file if there was an error
            if ($fileName) {
                deleteFile($fileName);
            }
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
    header('Location: ../ideas.php');
} else {
    $_SESSION['error_message'] = $response['message'];
    header('Location: ../submit-idea.php');
}
exit();
?>