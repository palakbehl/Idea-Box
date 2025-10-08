<?php
require_once '../php/config.php';
requireAdmin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = (int)$_POST['user_id'];
    
    try {
        if ($action === 'delete' && $userId > 0) {
            // Delete user and all related data (cascading delete should handle this)
            $db->query("DELETE FROM users WHERE id = ?", [$userId]);
            $_SESSION['success_message'] = 'User deleted successfully!';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
    }
    
    header('Location: users.php');
    exit();
}

// Get users with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE name LIKE ? OR email LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$totalUsers = $db->fetchOne("SELECT COUNT(*) as count FROM users $whereClause", $params)['count'];
$totalPages = ceil($totalUsers / $perPage);

$users = $db->fetchAll(
    "SELECT u.*, 
     (SELECT COUNT(*) FROM ideas WHERE user_id = u.id) as idea_count,
     (SELECT COUNT(*) FROM votes WHERE user_id = u.id) as vote_count,
     (SELECT COUNT(*) FROM comments WHERE user_id = u.id) as comment_count
     FROM users u 
     $whereClause 
     ORDER BY u.created_at DESC 
     LIMIT $perPage OFFSET $offset", 
    $params
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - IdeaBox Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar admin-nav">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-logo">IdeaBox Admin</a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="users.php" class="nav-link active">Users</a></li>
                <li><a href="ideas.php" class="nav-link">Ideas</a></li>
                <li><a href="../index.php" class="nav-link">View Site</a></li>
                <li><a href="../logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="admin-section">
            <div class="section-header">
                <h1>Manage Users</h1>
                <div class="header-stats">
                    <span>Total Users: <?php echo $totalUsers; ?></span>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>
            
            <!-- Search Form -->
            <div class="search-section">
                <form method="GET" action="users.php" class="search-form">
                    <input type="text" name="search" placeholder="Search users by name or email..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <?php if ($search): ?>
                        <a href="users.php" class="btn btn-outline">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Ideas</th>
                            <th>Votes</th>
                            <th>Comments</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo sanitize($user['name']); ?></td>
                                    <td><?php echo sanitize($user['email']); ?></td>
                                    <td><?php echo $user['idea_count']; ?></td>
                                    <td><?php echo $user['vote_count']; ?></td>
                                    <td><?php echo $user['comment_count']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td class="actions">
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? This will also delete all their ideas, votes, and comments.')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline">Previous</a>
                    <?php endif; ?>
                    
                    <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>