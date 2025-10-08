<?php
echo "<h2>MySQL Authentication Test</h2>";

// Common XAMPP passwords to try
$passwords = [
    '', // Empty password
    'root', // Password = root
    'mysql', // Password = mysql
    'xampp', // Password = xampp
    'admin', // Password = admin
    'password', // Password = password
    'palak@mysql', // Your original password
    '123456', // Common password
    'toor', // Reverse of root
    'vertrigo' // VertrigoServ default
];

foreach ($passwords as $index => $password) {
    echo "<h3>Test " . ($index + 1) . ": Testing password: '" . ($password === '' ? '(empty)' : $password) . "'</h3>";
    
    try {
        $pdo = new PDO("mysql:host=localhost", "root", $password);
        echo "✅ <strong>SUCCESS!</strong> Root password is: '" . ($password === '' ? '(empty)' : $password) . "'<br>";
        echo "Update your config.php with this password.<br><br>";
        
        // Test if ideabox database exists
        try {
            $stmt = $pdo->query("SHOW DATABASES LIKE 'ideabox'");
            $result = $stmt->fetch();
            if ($result) {
                echo "✅ Database 'ideabox' exists<br>";
            } else {
                echo "❌ Database 'ideabox' does not exist - please create it<br>";
            }
        } catch (Exception $e) {
            echo "Error checking database: " . $e->getMessage() . "<br>";
        }
        break; // Stop testing once we find the correct password
        
    } catch (PDOException $e) {
        echo "❌ Failed: " . $e->getMessage() . "<br><br>";
    }
}

echo "<hr><h3>Next Steps:</h3>";
echo "1. Use the successful password in your config.php<br>";
echo "2. Create 'ideabox' database if it doesn't exist<br>";
echo "3. Import database_setup.sql<br>";
?>