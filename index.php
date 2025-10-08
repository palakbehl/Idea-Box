<?php
require_once 'php/config.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to IdeaBox</h1>
                <p>Share your brilliant ideas and connect with innovative minds worldwide.</p>
                <div class="hero-buttons">
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="features-section">
        <div class="container">
            <h2>Why Choose IdeaBox?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>💡 Share Ideas</h3>
                    <p>Submit your innovative ideas and get feedback from the community.</p>
                </div>
                <div class="feature-card">
                    <h3>👍 Vote & Support</h3>
                    <p>Upvote the best ideas and help bring them to life.</p>
                </div>
                <div class="feature-card">
                    <h3>💬 Collaborate</h3>
                    <p>Comment and discuss ideas with fellow innovators.</p>
                </div>
                <div class="feature-card">
                    <h3>📊 Track Progress</h3>
                    <p>See how your ideas perform and gain insights.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>