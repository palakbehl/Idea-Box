<?php
require_once 'php/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match';
    } else {
        try {
            $existingUser = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            
            if ($existingUser) {
                $message = 'Email already registered';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->query("INSERT INTO users (name, email, password) VALUES (?, ?, ?)", [$name, $email, $hashedPassword]);
                
                if ($stmt) {
                    $userId = $db->lastInsertId();
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    header('Location: /IdeaBox/dashboard.php');
                    exit();
                } else {
                    $message = 'Registration failed';
                }
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Register for IdeaBox</h2>
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            
            <p><a href="login.php">Already have an account? Login</a></p>
        </div>
    </div>
</body>
</html>