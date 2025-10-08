<?php
require_once '../php/config.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Get top 5 most upvoted ideas
    $topIdeas = $db->fetchAll(
        "SELECT i.id, i.title, i.description, i.vote_count, i.created_at,
                u.name as author_name, c.name as category_name,
                (SELECT COUNT(*) FROM comments WHERE idea_id = i.id) as comment_count
         FROM ideas i 
         JOIN users u ON i.user_id = u.id 
         JOIN categories c ON i.category_id = c.id 
         ORDER BY i.vote_count DESC, i.created_at DESC 
         LIMIT 5"
    );
    
    // Format the response
    $response = [
        'success' => true,
        'message' => 'Top ideas retrieved successfully',
        'data' => [],
        'total' => count($topIdeas),
        'timestamp' => date('c') // ISO 8601 format
    ];
    
    foreach ($topIdeas as $idea) {
        $response['data'][] = [
            'id' => (int)$idea['id'],
            'title' => $idea['title'],
            'description' => strlen($idea['description']) > 200 
                ? substr($idea['description'], 0, 200) . '...' 
                : $idea['description'],
            'category' => $idea['category_name'],
            'author' => $idea['author_name'],
            'votes' => (int)$idea['vote_count'],
            'comments' => (int)$idea['comment_count'],
            'created_at' => date('c', strtotime($idea['created_at'])),
            'url' => SITE_URL . '/idea-detail.php?id=' . $idea['id']
        ];
    }
    
    // Return JSON response
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage(),
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT);
}
?>