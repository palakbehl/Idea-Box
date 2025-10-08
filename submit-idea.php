<?php
require_once 'php/config.php';
requireLogin();

$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Idea - IdeaBox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-logo">IdeaBox</a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="ideas.php" class="nav-link">Browse Ideas</a></li>
                <li><a href="submit-idea.php" class="nav-link active">Submit Idea</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="submit-idea-section">
            <h1>Submit Your Idea</h1>
            <p>Share your innovative idea with the community and get feedback!</p>
            
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
            
            <form id="ideaForm" action="php/submit-idea.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Idea Title:</label>
                    <input type="text" id="title" name="title" maxlength="200" required>
                    <span class="error-text" id="title-error"></span>
                    <small>Maximum 200 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="6" required></textarea>
                    <span class="error-text" id="description-error"></span>
                    <small>Provide a detailed description of your idea</small>
                </div>
                
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo sanitize($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-text" id="category-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="file">Attachment (Optional):</label>
                    <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                    <span class="error-text" id="file-error"></span>
                    <small>Allowed formats: JPG, PNG, GIF, PDF, DOC, DOCX (Max 5MB)</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Idea</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/validation.js"></script>
    <script>
        document.getElementById('ideaForm').addEventListener('submit', function(e) {
            if (!validateIdeaForm()) {
                e.preventDefault();
            }
        });
        
        // Character counter for title
        document.getElementById('title').addEventListener('input', function() {
            const maxLength = 200;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            if (remaining < 0) {
                this.value = this.value.substring(0, maxLength);
            }
        });
        
        // File size validation
        document.getElementById('file').addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 5 * 1024 * 1024) { // 5MB
                alert('File size must be less than 5MB');
                this.value = '';
            }
        });
    </script>
</body>
</html>