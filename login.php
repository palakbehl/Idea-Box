<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Login to IdeaBox</h2>
            <div id="error-message" class="error hidden"></div>
            <div id="success-message" class="success hidden"></div>
            
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
            
            <form id="loginForm" action="/IdeaBox/php/login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-text" id="email-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-text" id="password-error"></span>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="auth-links">
                <p><a href="register.php">Don't have an account? Register here</a></p>
                <p><a href="forgot-password.php">Forgot your password?</a></p>
                <p><a href="admin/login.php">Admin Login</a></p>
            </div>
        </div>
    </div>
    
    <script src="js/validation.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>