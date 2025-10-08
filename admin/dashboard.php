<?php
require_once '../config.php';

// Redirect to admin login if not logged in as admin
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

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
    <link rel="stylesheet" href="../style.css">
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
        
        .admin-nav {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
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
        
        .admin-dashboard {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-dashboard h1 {
            color: #333;
            margin-top: 0;
        }
        
        .admin-stats {
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
        
        .stat-link {
            display: inline-block;
            margin-top: 0.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.8rem;
        }
        
        .stat-link:hover {
            color: white;
            text-decoration: underline;
        }
        
        .admin-sections {
            margin: 2rem 0;
        }
        
        .admin-section {
            margin-bottom: 2rem;
        }
        
        .admin-section h2 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }
        
        .table-container {
            overflow-x: auto;
            margin: 1rem 0;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .admin-table th, .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .admin-table tr:hover {
            background: #f8f9fa;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #667eea;
            color: #667eea;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .text-center {
            text-align: center;
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