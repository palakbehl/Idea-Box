<?php
require_once 'config.php';
requireLogin();

// Get user information
$user = getCurrentUser();

// Get ideas with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$userFilter = isset($_GET['user']) ? (int)$_GET['user']) : 0;

$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(i.title LIKE ? OR i.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($categoryFilter > 0) {
    $whereConditions[] = "i.category_id = ?";
    $params[] = $categoryFilter;
}

if ($userFilter > 0) {
    $whereConditions[] = "i.user_id = ?";
    $params[] = $userFilter;
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

$totalIdeas = $db->fetchOne("SELECT COUNT(*) as count FROM ideas i $whereClause", $params)['count'];
$totalPages = ceil($totalIdeas / $perPage);

$ideas = $db->fetchAll(
    "SELECT i.*, u.name as author_name, c.name as category_name,
     (SELECT COUNT(*) FROM comments WHERE idea_id = i.id) as comment_count
     FROM ideas i 
     JOIN users u ON i.user_id = u.id 
     JOIN categories c ON i.category_id = c.id 
     $whereClause 
     ORDER BY i.created_at DESC 
     LIMIT $perPage OFFSET $offset", 
    $params
);

$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Ideas - IdeaBox</title>
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
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .section-header h1 {
            margin: 0;
            color: #333;
        }
        
        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .search-form input, .search-form select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .search-form input {
            flex: 1;
            min-width: 200px;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
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
        
        .btn-outline {
            background: transparent;
            border: 1px solid #667eea;
            color: #667eea;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .ideas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .idea-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .idea-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
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
            justify-content: space-between;
            font-size: 0.9rem;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .page-info {
            display: flex;
            align-items: center;
            color: #666;
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
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .search-form input, .search-form select, .search-form button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="user-dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="user-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link active">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="section-header">
            <h1>Browse Ideas</h1>
            <a href="submit-idea.php" class="btn btn-primary">Submit New Idea</a>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        
        <!-- Search and Filter Form -->
        <div class="search-section">
            <form method="GET" action="ideas.php" class="search-form">
                <input type="text" name="search" placeholder="Search ideas by title or description..." value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-secondary">Filter</button>
                <?php if ($search || $categoryFilter): ?>
                    <a href="ideas.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Ideas Grid -->
        <?php if ($ideas): ?>
            <div class="ideas-grid">
                <?php foreach ($ideas as $idea): ?>
                    <div class="idea-card">
                        <div class="idea-header">
                            <h3><a href="idea-detail.php?id=<?php echo $idea['id']; ?>"><?php echo sanitize($idea['title']); ?></a></h3>
                            <span class="category-tag"><?php echo sanitize($idea['category_name']); ?></span>
                        </div>
                        <p><?php echo sanitize(substr($idea['description'], 0, 150)) . (strlen($idea['description']) > 150 ? '...' : ''); ?></p>
                        <div class="idea-meta">
                            <span class="meta-item">
                                By <?php echo sanitize($idea['author_name']); ?>
                            </span>
                            <span class="meta-item">
                                <?php echo date('M j, Y', strtotime($idea['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?><?php echo $userFilter ? '&user=' . $userFilter : ''; ?>" class="btn btn-outline">Previous</a>
                    <?php endif; ?>
                    
                    <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?><?php echo $userFilter ? '&user=' . $userFilter : ''; ?>" class="btn btn-outline">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="idea-card">
                <p>No ideas found. <a href="submit-idea.php">Submit your first idea!</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>