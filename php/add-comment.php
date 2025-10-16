<?php
require_once 'config.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ideaId = isset($_POST['idea_id']) ? (int)$_POST['idea_id'] : 0;
    $comment = sanitize($_POST['comment']);
    $userId = $_SESSION['user_id'];
    
    // Validation
    if (!$ideaId) {
        $response['message'] = 'Invalid idea ID';
    } elseif (empty($comment)) {
        $response['message'] = 'Comment cannot be empty';
    } elseif (strlen($comment) < 3) {
        $response['message'] = 'Comment must be at least 3 characters long';
    } elseif (strlen($comment) > 1000) {
        $response['message'] = 'Comment must be less than 1000 characters';
    } else {
        try {
            // Check if idea exists
            $idea = $db->fetchOne("SELECT id FROM ideas WHERE id = ?", [$ideaId]);
            
            if (!$idea) {
                $response['message'] = 'Idea not found';
            } else {
                // Insert comment
                $stmt = $db->query(
                    "INSERT INTO comments (user_id, idea_id, comment) VALUES (?, ?, ?)",
                    [$userId, $ideaId, $comment]
                );
                
                if ($stmt) {
                    $response['success'] = true;
                    $response['message'] = 'Comment added successfully!';
                } else {
                    $response['message'] = 'Failed to add comment. Please try again.';
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
header('Location: ../idea-detail.php?id=' . $ideaId);
exit();
?>