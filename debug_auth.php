<?php
require_once 'php/config.php';

echo "<h2>Authentication Debug</h2>";

echo "<h3>1. Session Status</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";

echo "<h3>2. Session Data</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>3. Database Connection Test</h3>";
try {
    $db = new Database();
    echo "✅ Database connection successful<br>";
    
    // Test if admin user exists
    $admin = $db->fetchOne("SELECT * FROM admins WHERE username = 'admin'");
    if ($admin) {
        echo "✅ Default admin user exists<br>";
    } else {
        echo "❌ Default admin user missing - run database setup<br>";
    }
    
    // Test user table
    $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    echo "Users in database: " . $userCount['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h3>4. Test User Registration</h3>";
echo "<form method='POST'>";
echo "Name: <input type='text' name='test_name' value='Test User'><br>";
echo "Email: <input type='email' name='test_email' value='test@example.com'><br>";
echo "Password: <input type='password' name='test_password' value='123456'><br>";
echo "<input type='submit' name='test_register' value='Test Register'>";
echo "</form>";

if (isset($_POST['test_register'])) {
    try {
        $name = $_POST['test_name'];
        $email = $_POST['test_email'];
        $password = password_hash($_POST['test_password'], PASSWORD_DEFAULT);
        
        $stmt = $db->query("INSERT INTO users (name, email, password) VALUES (?, ?, ?)", [$name, $email, $password]);
        echo "<p style='color: green;'>✅ Test user created successfully!</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Registration failed: " . $e->getMessage() . "</p>";
    }
}
?>