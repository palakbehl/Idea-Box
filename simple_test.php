<?php
// Simple registration test without redirects
require_once 'php/config.php';

echo "<h2>Simple Registration Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Form Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    echo "<h3>Processing Registration:</h3>";
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        echo "❌ All fields are required<br>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Invalid email format<br>";
    } elseif (strlen($password) < 6) {
        echo "❌ Password must be at least 6 characters<br>";
    } else {
        try {
            // Check if email exists
            $existingUser = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            
            if ($existingUser) {
                echo "❌ Email already registered<br>";
            } else {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $db->query(
                    "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
                    [$name, $email, $hashedPassword]
                );
                
                if ($stmt) {
                    $userId = $db->lastInsertId();
                    echo "✅ User created successfully! User ID: " . $userId . "<br>";
                    
                    // Set session
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    echo "✅ Session set successfully!<br>";
                    echo "<a href='/IdeaBox/dashboard.php'>Go to Dashboard</a><br>";
                } else {
                    echo "❌ Failed to create user<br>";
                }
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "<form method='POST'>";
    echo "<p>Name: <input type='text' name='name' required></p>";
    echo "<p>Email: <input type='email' name='email' required></p>";
    echo "<p>Password: <input type='password' name='password' required></p>";
    echo "<p><input type='submit' value='Register'></p>";
    echo "</form>";
}

echo "<h3>Current Session:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Links:</h3>";
echo "<a href='/IdeaBox/'>Home</a> | ";
echo "<a href='/IdeaBox/login.php'>Login</a> | ";
echo "<a href='/IdeaBox/dashboard.php'>Dashboard</a>";
?>