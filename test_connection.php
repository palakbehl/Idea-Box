<?php
// Database Connection Test for IdeaBox
echo "<h2>IdeaBox Database Connection Test</h2>";

// Test basic MySQL connection
echo "<h3>1. Testing MySQL Connection</h3>";
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    echo "✅ MySQL connection successful<br>";
} catch (PDOException $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "<br>";
    exit();
}

// Test if ideabox database exists
echo "<h3>2. Checking if 'ideabox' database exists</h3>";
try {
    $stmt = $pdo->query("SHOW DATABASES LIKE 'ideabox'");
    $result = $stmt->fetch();
    if ($result) {
        echo "✅ Database 'ideabox' exists<br>";
    } else {
        echo "❌ Database 'ideabox' does not exist<br>";
        echo "<strong>Solution:</strong> Create the database using phpMyAdmin or run this SQL:<br>";
        echo "<code>CREATE DATABASE ideabox;</code><br>";
        exit();
    }
} catch (PDOException $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "<br>";
}

// Test connection to ideabox database
echo "<h3>3. Testing connection to 'ideabox' database</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ideabox", "root", "");
    echo "✅ Connected to 'ideabox' database successfully<br>";
} catch (PDOException $e) {
    echo "❌ Failed to connect to 'ideabox' database: " . $e->getMessage() . "<br>";
    exit();
}

// Test if tables exist
echo "<h3>4. Checking if tables exist</h3>";
try {
    $tables = ['users', 'categories', 'ideas', 'votes', 'comments', 'admins'];
    $missingTables = [];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if (!$stmt->fetch()) {
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        echo "✅ All required tables exist<br>";
    } else {
        echo "❌ Missing tables: " . implode(', ', $missingTables) . "<br>";
        echo "<strong>Solution:</strong> Import the database_setup.sql file in phpMyAdmin<br>";
    }
} catch (PDOException $e) {
    echo "❌ Error checking tables: " . $e->getMessage() . "<br>";
}

// Test uploads directory
echo "<h3>5. Checking uploads directory permissions</h3>";
$uploadsDir = __DIR__ . '/uploads/';
if (is_dir($uploadsDir)) {
    if (is_writable($uploadsDir)) {
        echo "✅ Uploads directory is writable<br>";
    } else {
        echo "❌ Uploads directory is not writable<br>";
        echo "<strong>Solution:</strong> Run: <code>icacls uploads /grant Everyone:F</code><br>";
    }
} else {
    echo "❌ Uploads directory does not exist<br>";
    echo "<strong>Solution:</strong> Create the uploads directory<br>";
}

echo "<h3>6. XAMPP Information</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Script Path: " . __DIR__ . "<br>";

echo "<h3>Test Complete</h3>";
echo "If all tests pass, your IdeaBox should work properly.<br>";
echo "Access your main application at: <a href='index.php'>index.php</a>";
?>