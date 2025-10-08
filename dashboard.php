<?php
require_once '../php/config.php';
requireAdmin();

// Get statistics
try {
    $totalUsers = $db->fetchOne("SELECT COUNT(*) as count FROM users")['count'];
    $totalIdeas = $db->fetchOne("SELECT COUNT(*) as count FROM ideas")['count'];
    $totalVotes = $db->fetchOne("SELECT COUNT(*) as count FROM votes")['count'];
    $totalComments = $db->fetchOne("SELECT COUNT(*) as count FROM comments")['count'];
    
    // Recent activity
    $recentUsers = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $recentIdeas = $db->fetchAll(
        "SELECT i.*, u.name as author_name, c.name as category_name 
         FROM ideas i 
         JOIN users u ON i.user_id = u.id 
         JOIN categories c ON i.category_id = c.id 
         ORDER BY i.created_at DESC LIMIT 5"
    );
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IdeaBox</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar admin-nav">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-logo">IdeaBox Admin</a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="users.php" class="nav-link">Users</a></li>
                <li><a href="ideas.php" class="nav-link">Ideas</a></li>
                <li><a href="../index.php" class="nav-link">View Site</a></li>
                <li><a href="../logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="admin-dashboard">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo sanitize($_SESSION['admin_username']); ?>!</p>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error">Error loading dashboard data: <?php echo $error; ?></div>
            <?php else: ?>
                <!-- Statistics Cards -->
                <div class="admin-stats">
                    <div class="stat-card">
                        <h3><?php echo $totalUsers; ?></h3>
                        <p>Total Users</p>
                        <a href="users.php" class="stat-link">Manage Users</a>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalIdeas; ?></h3>
                        <p>Total Ideas</p>
                        <a href="ideas.php" class="stat-link">Manage Ideas</a>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalVotes; ?></h3>
                        <p>Total Votes</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalComments; ?></h3>
                        <p>Total Comments</p>
                    </div>
                </div>
                
                <div class="admin-sections">
                    <!-- Recent Users -->
                    <div class="admin-section">
                        <h2>Recent Users</h2>
                        <div class="table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentUsers as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo sanitize($user['name']); ?></td>
                                            <td><?php echo sanitize($user['email']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <a href="users.php?action=view&id=<?php echo $user['id']; ?>" class="btn btn-sm">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="users.php" class="btn btn-secondary">View All Users</a>
                    </div>
                    
                    <!-- Recent Ideas -->
                    <div class="admin-section">
                        <h2>Recent Ideas</h2>
                        <div class="table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Votes</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentIdeas as $idea): ?>
                                        <tr>
                                            <td><?php echo $idea['id']; ?></td>
                                            <td><?php echo sanitize(substr($idea['title'], 0, 30)) . (strlen($idea['title']) > 30 ? '...' : ''); ?></td>
                                            <td><?php echo sanitize($idea['author_name']); ?></td>
                                            <td><?php echo sanitize($idea['category_name']); ?></td>
                                            <td><?php echo $idea['vote_count']; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($idea['created_at'])); ?></td>
                                            <td>
                                                <a href="../idea-detail.php?id=<?php echo $idea['id']; ?>" class="btn btn-sm" target="_blank">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="ideas.php" class="btn btn-secondary">View All Ideas</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>