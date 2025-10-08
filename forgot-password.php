<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Reset Your Password</h2>
            <p>Enter your email address and we'll help you reset your password.</p>
            
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
            
            <form id="forgotPasswordForm" action="php/forgot-password.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-text" id="email-error"></span>
                </div>
                
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
            
            <p class="auth-link">
                Remember your password? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
    
    <script src="js/validation.js"></script>
</body>
</html>