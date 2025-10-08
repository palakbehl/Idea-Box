<?php
require_once 'config.php';
requireLogin();

$user = getCurrentUser();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $categoryId = (int)$_POST['category_id'];
    $userId = $_SESSION['user_id'];
    $fileName = null;
    
    // Validation
    if (empty($title) || empty($description) || empty($categoryId)) {
        $message = 'Title, description, and category are required';
        $messageType = 'error';
    } elseif (strlen($title) > 200) {
        $message = 'Title must be 200 characters or less';
        $messageType = 'error';
    } elseif (strlen($description) < 10) {
        $message = 'Description must be at least 10 characters long';
        $messageType = 'error';
    } else {
        try {
            // Handle file upload if provided
            if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $fileName = uploadFile($_FILES['file']);
                } catch (Exception $e) {
                    $message = 'File upload error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
            
            if (empty($message)) {
                // Insert idea into database
                $stmt = $db->query(
                    "INSERT INTO ideas (user_id, title, description, category_id, file_path) VALUES (?, ?, ?, ?, ?)",
                    [$userId, $title, $description, $categoryId, $fileName]
                );
                
                if ($stmt) {
                    $message = 'Idea submitted successfully!';
                    $messageType = 'success';
                    // Clear form data on success
                    $title = '';
                    $description = '';
                    $categoryId = '';
                } else {
                    $message = 'Failed to submit idea. Please try again.';
                    $messageType = 'error';
                    // Delete uploaded file if database insert failed
                    if ($fileName) {
                        deleteFile($fileName);
                    }
                }
            }
        } catch (Exception $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
            // Delete uploaded file if there was an error
            if ($fileName) {
                deleteFile($fileName);
            }
        }
    }
}

// Get categories for the form
$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Idea - IdeaBox</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
        }
        
        .nav-logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1rem;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .submit-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .submit-section h1 {
            color: #333;
            margin-top: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
        }
        
        .file-info {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="user-dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="user-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link active">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="submit-section">
            <h1>Submit New Idea</h1>
            
            <?php if ($message): ?>
                <div class="<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Idea Title</label>
                    <input type="text" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($categoryId) && $categoryId == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo sanitize($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="file">Attach File (Optional)</label>
                    <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                    <div class="file-info">
                        Supported formats: JPG, JPEG, PNG, GIF, PDF, DOC, DOCX. Max size: 5MB
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Idea</button>
                <a href="ideas.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>