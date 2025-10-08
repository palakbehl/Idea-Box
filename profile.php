<?php
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - IdeaBox</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link active">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="profile-section">
            <h1>My Profile</h1>
            
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
            
            <div class="profile-content">
                <div class="profile-info">
                    <h2>Account Information</h2>
                    <form id="profileForm" action="update-profile.php" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo sanitize($user['name']); ?>" required>
                            <span class="error-text" id="name-error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email" value="<?php echo sanitize($user['email']); ?>" required>
                            <span class="error-text" id="email-error"></span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
                
                <div class="password-change">
                    <h2>Change Password</h2>
                    <form id="passwordForm" action="change-password.php" method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" required>
                            <span class="error-text" id="current-password-error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <span class="error-text" id="new-password-error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <span class="error-text" id="confirm-password-error"></span>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">Change Password</button>
                    </form>
                </div>
                
                <div class="account-info">
                    <h2>Account Details</h2>
                    <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Last updated:</strong> <?php echo date('F j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="validation.js"></script>
    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            if (!validateProfileForm()) {
                e.preventDefault();
            }
        });
        
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            if (!validatePasswordChangeForm()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>