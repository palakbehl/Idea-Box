<?php
require_once '../config.php';
// Allow both users and admins
if (!isLoggedIn() && !isAdmin()) {
    header('Location: login.php');
    exit();
}

// Set appropriate navigation based on user type
$is_admin = isAdmin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $ideaId = (int)$_POST['idea_id'];
    
    try {
        if ($action === 'delete' && $ideaId > 0) {
            // Get file path before deleting
            $idea = $db->fetchOne("SELECT file_path FROM ideas WHERE id = ?", [$ideaId]);
            
            // Delete idea and all related data (cascading delete should handle this)
            $db->query("DELETE FROM ideas WHERE id = ?", [$ideaId]);
            
            // Delete associated file if exists
            if ($idea && $idea['file_path']) {
                deleteFile($idea['file_path']);
            }
            
            $_SESSION['success_message'] = 'Idea deleted successfully!';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
    }
    
    header('Location: ideas.php');
    exit();
}

// Get ideas with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

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
    <title>Manage Ideas - IdeaBox Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav class="navbar <?php echo $is_admin ? 'admin-nav' : ''; ?>">
        <div class="nav-container">
            <a href="<?php echo $is_admin ? 'dashboard.php' : '../user-dashboard.php'; ?>" class="nav-logo">IdeaBox <?php echo $is_admin ? 'Admin' : ''; ?></a>
            <ul class="nav-menu">
                <?php if ($is_admin): ?>
                    <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><a href="users.php" class="nav-link">Users</a></li>
                    <li><a href="ideas.php" class="nav-link active">Ideas</a></li>
                    <li><a href="../index.php" class="nav-link">View Site</a></li>
                    <li><a href="../logout.php" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="../user-dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><a href="../ideas.php" class="nav-link active">Browse Ideas</a></li>
                    <li><a href="../submit-idea.php" class="nav-link">Submit Idea</a></li>
                    <li><a href="../profile.php" class="nav-link">Profile</a></li>
                    <li><a href="../logout.php" class="nav-link">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="admin-section">
            <div class="section-header">
                <h1>Manage Ideas</h1>
                <div class="header-stats">
                    <span>Total Ideas: <?php echo $totalIdeas; ?></span>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>
            
            <!-- Search and Filter Form -->
            <div class="search-section">
                <form method="GET" action="ideas.php" class="search-form admin-search">
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
            
            <!-- Ideas Table -->
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Votes</th>
                            <th>Comments</th>
                            <th>File</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ideas): ?>
                            <?php foreach ($ideas as $idea): ?>
                                <tr>
                                    <td><?php echo $idea['id']; ?></td>
                                    <td>
                                        <div class="idea-title">
                                            <?php echo sanitize(substr($idea['title'], 0, 50)) . (strlen($idea['title']) > 50 ? '...' : ''); ?>
                                        </div>
                                    </td>
                                    <td><?php echo sanitize($idea['author_name']); ?></td>
                                    <td><?php echo sanitize($idea['category_name']); ?></td>
                                    <td><?php echo $idea['vote_count']; ?></td>
                                    <td><?php echo $idea['comment_count']; ?></td>
                                    <td>
                                        <?php if ($idea['file_path']): ?>
                                            <a href="../uploads/<?php echo sanitize($idea['file_path']); ?>" target="_blank" class="file-link">📎</a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($idea['created_at'])); ?></td>
                                    <td class="actions">
                                        <a href="../idea-detail.php?id=<?php echo $idea['id']; ?>" target="_blank" class="btn btn-sm btn-outline">View</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this idea? This will also delete all votes and comments.')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No ideas found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?>" class="btn btn-outline">Previous</a>
                    <?php endif; ?>
                    
                    <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?>" class="btn btn-outline">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>