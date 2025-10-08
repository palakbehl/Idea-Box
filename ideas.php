<?php
require_once 'php/config.php';
requireLogin();

// Get search and filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sortBy = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';

// Build SQL query
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

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Set order by clause
$orderBy = match($sortBy) {
    'oldest' => 'ORDER BY i.created_at ASC',
    'popular' => 'ORDER BY i.vote_count DESC, i.created_at DESC',
    'title' => 'ORDER BY i.title ASC',
    default => 'ORDER BY i.created_at DESC'
};

$sql = "SELECT i.*, u.name as author_name, c.name as category_name 
        FROM ideas i 
        JOIN users u ON i.user_id = u.id 
        JOIN categories c ON i.category_id = c.id 
        $whereClause 
        $orderBy";

$ideas = $db->fetchAll($sql, $params);
$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Ideas - IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link active">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="ideas-section">
            <div class="section-header">
                <h1>Browse Ideas</h1>
                <a href="submit-idea.php" class="btn btn-primary">Submit New Idea</a>
            </div>
            
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>
            
            <!-- Search and Filter Form -->
            <div class="search-filter-section">
                <form method="GET" action="ideas.php" class="search-form">
                    <div class="search-row">
                        <div class="search-group">
                            <input type="text" name="search" placeholder="Search ideas..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="category">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo sanitize($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="sort-group">
                            <select name="sort">
                                <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="oldest" <?php echo $sortBy == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                                <option value="popular" <?php echo $sortBy == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                                <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Title A-Z</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <?php if ($search || $categoryFilter || $sortBy != 'newest'): ?>
                            <a href="ideas.php" class="btn btn-outline">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Ideas Grid -->
            <div class="ideas-results">
                <p class="results-count">
                    <?php echo count($ideas); ?> idea<?php echo count($ideas) != 1 ? 's' : ''; ?> found
                </p>
                
                <?php if ($ideas): ?>
                    <div class="ideas-grid">
                        <?php foreach ($ideas as $idea): ?>
                            <div class="idea-card">
                                <div class="idea-header">
                                    <h3><a href="idea-detail.php?id=<?php echo $idea['id']; ?>">
                                        <?php echo sanitize($idea['title']); ?>
                                    </a></h3>
                                    <span class="idea-category"><?php echo sanitize($idea['category_name']); ?></span>
                                </div>
                                
                                <div class="idea-content">
                                    <p class="idea-description">
                                        <?php 
                                        $description = sanitize($idea['description']);
                                        echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="idea-meta">
                                    <div class="idea-stats">
                                        <span class="vote-count">👍 <?php echo $idea['vote_count']; ?></span>
                                        <?php
                                        $commentCount = $db->fetchOne("SELECT COUNT(*) as count FROM comments WHERE idea_id = ?", [$idea['id']]);
                                        ?>
                                        <span class="comment-count">💬 <?php echo $commentCount['count']; ?></span>
                                    </div>
                                    
                                    <div class="idea-author">
                                        <span>by <?php echo sanitize($idea['author_name']); ?></span>
                                        <span class="idea-date"><?php echo date('M j, Y', strtotime($idea['created_at'])); ?></span>
                                    </div>
                                </div>
                                
                                <?php if ($idea['file_path']): ?>
                                    <div class="idea-attachment">
                                        📎 Has attachment
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <h3>No ideas found</h3>
                        <p>Try adjusting your search criteria or <a href="submit-idea.php">submit the first idea</a>!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-submit form when filters change
        document.querySelectorAll('select[name="category"], select[name="sort"]').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>