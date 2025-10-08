<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Get top 5 ideas with most votes
    $topIdeas = $db->fetchAll(
        "SELECT i.id, i.title, i.description, i.created_at, i.vote_count,
                u.name as author, c.name as category,
                (SELECT COUNT(*) FROM comments WHERE idea_id = i.id) as comment_count
         FROM ideas i
         JOIN users u ON i.user_id = u.id
         JOIN categories c ON i.category_id = c.id
         ORDER BY i.vote_count DESC, i.created_at DESC
         LIMIT 5"
    );
    
    // Format the response
    $responseData = [
        'success' => true,
        'message' => 'Top ideas retrieved successfully',
        'data' => [],
        'total' => count($topIdeas),
        'timestamp' => date('c')
    ];
    
    // Process each idea
    foreach ($topIdeas as $idea) {
        $responseData['data'][] = [
            'id' => (int)$idea['id'],
            'title' => $idea['title'],
            'description' => $idea['description'],
            'category' => $idea['category'],
            'author' => $idea['author'],
            'votes' => (int)$idea['vote_count'],
            'comments' => (int)$idea['comment_count'],
            'created_at' => $idea['created_at'],
            'url' => 'http://localhost/Idea-Box/idea-detail.php?id=' . $idea['id']
        ];
    }
    
    echo json_encode($responseData);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => [],
        'total' => 0,
        'timestamp' => date('c')
    ]);
}
?>