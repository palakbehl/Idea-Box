<?php
require_once 'config.php';
requireLogin();

$response = ['success' => false, 'message' => '', 'voteCount' => 0, 'userVoted' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ideaId = isset($_POST['idea_id']) ? (int)$_POST['idea_id'] : 0;
    $userId = $_SESSION['user_id'];
    
    if (!$ideaId) {
        $response['message'] = 'Invalid idea ID';
    } else {
        try {
            // Check if idea exists
            $idea = $db->fetchOne("SELECT id, user_id FROM ideas WHERE id = ?", [$ideaId]);
            
            if (!$idea) {
                $response['message'] = 'Idea not found';
            } elseif ($idea['user_id'] == $userId) {
                $response['message'] = 'You cannot vote for your own idea';
            } else {
                // Check if user has already voted
                $existingVote = $db->fetchOne(
                    "SELECT id FROM votes WHERE user_id = ? AND idea_id = ?", 
                    [$userId, $ideaId]
                );
                
                if ($existingVote) {
                    // Remove vote (toggle)
                    $db->query("DELETE FROM votes WHERE user_id = ? AND idea_id = ?", [$userId, $ideaId]);
                    $db->query("UPDATE ideas SET vote_count = vote_count - 1 WHERE id = ?", [$ideaId]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Vote removed';
                    $response['userVoted'] = false;
                } else {
                    // Add vote
                    $db->query("INSERT INTO votes (user_id, idea_id) VALUES (?, ?)", [$userId, $ideaId]);
                    $db->query("UPDATE ideas SET vote_count = vote_count + 1 WHERE id = ?", [$ideaId]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Vote added';
                    $response['userVoted'] = true;
                }
                
                // Get updated vote count
                $updatedIdea = $db->fetchOne("SELECT vote_count FROM ideas WHERE id = ?", [$ideaId]);
                $response['voteCount'] = $updatedIdea['vote_count'];
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

jsonResponse($response);
?>