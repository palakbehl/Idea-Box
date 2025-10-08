<?php
require_once 'php/config.php';
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IdeaBox</title>
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
        <div class="dashboard">
            <h1>Welcome back, <?php echo sanitize($user['name']); ?>!</h1>
            
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>
            
            <div class="dashboard-stats">
                <?php
                try {
                    $userIdeas = $db->fetchAll("SELECT COUNT(*) as count FROM ideas WHERE user_id = ?", [$user['id']]);
                    $userVotes = $db->fetchAll("SELECT COUNT(*) as count FROM votes WHERE user_id = ?", [$user['id']]);
                    $userComments = $db->fetchAll("SELECT COUNT(*) as count FROM comments WHERE user_id = ?", [$user['id']]);
                    $totalVotesReceived = $db->fetchAll("SELECT SUM(vote_count) as total FROM ideas WHERE user_id = ?", [$user['id']]);
                ?>
                <div class="stat-card">
                    <h3><?php echo $userIdeas[0]['count']; ?></h3>
                    <p>Ideas Submitted</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $userVotes[0]['count']; ?></h3>
                    <p>Votes Cast</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $userComments[0]['count']; ?></h3>
                    <p>Comments Made</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalVotesReceived[0]['total'] ?? 0; ?></h3>
                    <p>Votes Received</p>
                </div>
                <?php } catch (Exception $e) { echo '<p>Error loading stats</p>'; } ?>
            </div>
            
            <div class="dashboard-actions">
                <a href="submit-idea.php" class="btn btn-primary">Submit New Idea</a>
                <a href="ideas.php" class="btn btn-secondary">Browse Ideas</a>
                <a href="profile.php" class="btn btn-outline">Edit Profile</a>
            </div>
            
            <div class="recent-ideas">
                <h2>Your Recent Ideas</h2>
                <?php
                try {
                    $recentIdeas = $db->fetchAll(
                        "SELECT i.*, c.name as category_name FROM ideas i 
                         JOIN categories c ON i.category_id = c.id 
                         WHERE i.user_id = ? 
                         ORDER BY i.created_at DESC LIMIT 5", 
                        [$user['id']]
                    );
                    
                    if ($recentIdeas) {
                        echo '<div class="ideas-grid">';
                        foreach ($recentIdeas as $idea) {
                            echo '<div class="idea-card">';
                            echo '<h3><a href="idea-detail.php?id=' . $idea['id'] . '">' . sanitize($idea['title']) . '</a></h3>';
                            echo '<p class="idea-category">' . sanitize($idea['category_name']) . '</p>';
                            echo '<p class="idea-meta">Votes: ' . $idea['vote_count'] . ' | ' . date('M j, Y', strtotime($idea['created_at'])) . '</p>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p>You haven\'t submitted any ideas yet. <a href="submit-idea.php">Submit your first idea!</a></p>';
                    }
                } catch (Exception $e) {
                    echo '<p>Error loading ideas</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>