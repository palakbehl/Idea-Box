<?php
// Don't check for login status on the main page to avoid redirect loops
// Just show the main page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to IdeaBox - Share Your Ideas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .hero {
            text-align: center;
            padding: 4rem 1rem;
            color: white;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: #fff;
            color: #667eea;
        }
        
        .btn-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .features {
            background: white;
            padding: 3rem 1rem;
            border-top-left-radius: 2rem;
            border-top-right-radius: 2rem;
            margin-top: 2rem;
        }
        
        .features h2 {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-card h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .footer {
            text-align: center;
            padding: 2rem;
            color: white;
            background: rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 80%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Welcome to IdeaBox</h1>
        <p>Share your innovative ideas with the community, get feedback, and collaborate with like-minded individuals.</p>
        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
            <a href="admin/login.php" class="btn btn-secondary">Admin Login</a>
        </div>
    </div>
    
    <div class="features">
        <h2>Why Choose IdeaBox?</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <h3>Share Ideas</h3>
                <p>Submit your creative ideas and share them with a community of innovators and thinkers.</p>
            </div>
            <div class="feature-card">
                <h3>Get Feedback</h3>
                <p>Receive valuable feedback and suggestions from other users through comments and votes.</p>
            </div>
            <div class="feature-card">
                <h3>Collaborate</h3>
                <p>Connect with others who share similar interests and collaborate on bringing ideas to life.</p>
            </div>
            <div class="feature-card">
                <h3>Track Progress</h3>
                <p>Monitor the popularity of your ideas through our voting system and analytics.</p>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 IdeaBox. Share Your Ideas, Change the World.</p>
    </div>
</body>
</html>