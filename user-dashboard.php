<?php
require_once 'config.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get user information
$user = getCurrentUser();

// Get user's ideas
try {
    $userIdeas = $db->fetchAll(
        "SELECT i.*, c.name as category_name,
         (SELECT COUNT(*) FROM comments WHERE idea_id = i.id) as comment_count
         FROM ideas i 
         JOIN categories c ON i.category_id = c.id 
         WHERE i.user_id = ?
         ORDER BY i.created_at DESC 
         LIMIT 5", 
        [$user['id']]
    );
    
    // Get statistics
    $totalIdeas = $db->fetchOne("SELECT COUNT(*) as count FROM ideas WHERE user_id = ?", [$user['id']])['count'];
    $totalVotes = $db->fetchOne(
        "SELECT SUM(vote_count) as count FROM ideas WHERE user_id = ?", 
        [$user['id']]
    )['count'] ?? 0;
    
    $totalComments = $db->fetchOne(
        "SELECT COUNT(*) as count FROM comments c 
         JOIN ideas i ON c.idea_id = i.id 
         WHERE i.user_id = ?", 
        [$user['id']]
    )['count'] ?? 0;
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IdeaBox</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
        }
        
        .nav-logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1rem;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .dashboard {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .dashboard h1 {
            color: #333;
            margin-top: 0;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin: 0 0 0.5rem 0;
        }
        
        .stat-card p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0 1rem 0;
        }
        
        .section-header h2 {
            margin: 0;
            color: #333;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .ideas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 1rem 0;
        }
        
        .idea-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .idea-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .idea-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .idea-header h3 {
            margin: 0;
            color: #333;
        }
        
        .idea-header h3 a {
            color: #333;
            text-decoration: none;
        }
        
        .idea-header h3 a:hover {
            color: #667eea;
        }
        
        .category-tag {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .idea-card p {
            color: #666;
            line-height: 1.6;
            margin: 1rem 0;
        }
        
        .idea-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #888;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .icon {
            font-size: 1.2rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .empty-state p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .text-center {
            text-align: center;
            margin: 2rem 0;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="user-dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="user-dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard">
            <h1>Welcome, <?php echo sanitize($user['name']); ?>!</h1>
            <p>Here's what's happening with your ideas today.</p>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error">Error loading dashboard data: <?php echo $error; ?></div>
            <?php else: ?>
                <!-- Statistics Cards -->
                <div class="stats">
                    <div class="stat-card">
                        <h3><?php echo $totalIdeas; ?></h3>
                        <p>Your Ideas</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalVotes; ?></h3>
                        <p>Total Votes</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalComments; ?></h3>
                        <p>Comments Received</p>
                    </div>
                </div>
                
                <div class="dashboard-sections">
                    <!-- Your Recent Ideas -->
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h2>Your Recent Ideas</h2>
                            <a href="submit-idea.php" class="btn btn-primary">Submit New Idea</a>
                        </div>
                        
                        <?php if ($userIdeas): ?>
                            <div class="ideas-grid">
                                <?php foreach ($userIdeas as $idea): ?>
                                    <div class="idea-card">
                                        <div class="idea-header">
                                            <h3><a href="idea-detail.php?id=<?php echo $idea['id']; ?>"><?php echo sanitize($idea['title']); ?></a></h3>
                                            <span class="category-tag"><?php echo sanitize($idea['category_name']); ?></span>
                                        </div>
                                        <p><?php echo sanitize(substr($idea['description'], 0, 100)) . (strlen($idea['description']) > 100 ? '...' : ''); ?></p>
                                        <div class="idea-meta">
                                            <span class="meta-item">
                                                <span class="icon">👍</span>
                                                <?php echo $idea['vote_count']; ?> votes
                                            </span>
                                            <span class="meta-item">
                                                <span class="icon">💬</span>
                                                <?php echo $idea['comment_count']; ?> comments
                                            </span>
                                            <span class="meta-item">
                                                <?php echo date('M j, Y', strtotime($idea['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center">
                                <a href="ideas.php?user=<?php echo $user['id']; ?>" class="btn btn-secondary">View All Your Ideas</a>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>You haven't submitted any ideas yet.</p>
                                <a href="submit-idea.php" class="btn btn-primary">Submit Your First Idea</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>