<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - IdeaBox</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Admin Login</h2>
            <p>Access the IdeaBox administration panel</p>
            
            <?php
            session_start();
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>
            
            <form id="adminLoginForm" action="../php/admin-login.php" method="POST">
                <div class="form-group">
                    <label for="username">Admin Username:</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error-text" id="username-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Admin Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-text" id="password-error"></span>
                </div>
                
                <button type="submit" class="btn btn-primary">Login as Admin</button>
            </form>
            
            <div class="auth-links">
                <p><a href="../index.php">← Back to IdeaBox</a></p>
                <p><em>Default admin credentials: admin / admin123</em></p>
            </div>
        </div>
    </div>
    
    <script src="../js/validation.js"></script>
</body>
</html>