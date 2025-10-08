<?php
require_once 'config.php';
requireLogin();

$ideaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$ideaId) {
    header('Location: ideas.php');
    exit();
}

// Get idea details
$idea = $db->fetchOne(
    "SELECT i.*, u.name as author_name, c.name as category_name 
     FROM ideas i 
     JOIN users u ON i.user_id = u.id 
     JOIN categories c ON i.category_id = c.id 
     WHERE i.id = ?", 
    [$ideaId]
);

if (!$idea) {
    header('Location: ideas.php');
    exit();
}

// Get comments for this idea
$comments = $db->fetchAll(
    "SELECT c.*, u.name as author_name 
     FROM comments c 
     JOIN users u ON c.user_id = u.id 
     WHERE c.idea_id = ? 
     ORDER BY c.created_at DESC", 
    [$ideaId]
);

// Check if current user has voted
$userVote = null;
if (isLoggedIn()) {
    $userVote = $db->fetchOne(
        "SELECT id FROM votes WHERE user_id = ? AND idea_id = ?", 
        [$_SESSION['user_id'], $ideaId]
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($idea['title']); ?> - IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="idea-detail">
            <div class="idea-header">
                <a href="ideas.php" class="back-link">← Back to Ideas</a>
                <div class="idea-category"><?php echo sanitize($idea['category_name']); ?></div>
            </div>
            
            <div class="idea-content">
                <h1><?php echo sanitize($idea['title']); ?></h1>
                
                <div class="idea-meta">
                    <span class="author">by <?php echo sanitize($idea['author_name']); ?></span>
                    <span class="date"><?php echo date('F j, Y g:i A', strtotime($idea['created_at'])); ?></span>
                </div>
                
                <div class="idea-description">
                    <?php echo nl2br(sanitize($idea['description'])); ?>
                </div>
                
                <?php if ($idea['file_path']): ?>
                    <div class="idea-attachment">
                        <h3>Attachment</h3>
                        <a href="uploads/<?php echo sanitize($idea['file_path']); ?>" target="_blank" class="attachment-link">
                            📎 <?php echo sanitize($idea['file_path']); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="idea-actions">
                    <div class="vote-section">
                        <button id="voteBtn" class="vote-btn <?php echo $userVote ? 'voted' : ''; ?>" 
                                data-idea-id="<?php echo $idea['id']; ?>">
                            👍 <span id="voteCount"><?php echo $idea['vote_count']; ?></span>
                        </button>
                        <span class="vote-text"><?php echo $userVote ? 'You voted' : 'Vote for this idea'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="comments-section">
                <h2>Comments (<?php echo count($comments); ?>)</h2>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>
                
                <!-- Add Comment Form -->
                <form id="commentForm" action="add-comment.php" method="POST" class="comment-form">
                    <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
                    <div class="form-group">
                        <textarea name="comment" placeholder="Add your comment..." rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
                
                <!-- Comments List -->
                <div class="comments-list">
                    <?php if ($comments): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-header">
                                    <span class="comment-author"><?php echo sanitize($comment['author_name']); ?></span>
                                    <span class="comment-date"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <div class="comment-content">
                                    <?php echo nl2br(sanitize($comment['comment'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-comments">No comments yet. Be the first to comment!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/voting.js"></script>
</body>
</html>