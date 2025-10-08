<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - IdeaBox</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <div class="api-docs">
            <h1>IdeaBox REST API</h1>
            <p>Simple REST API for accessing IdeaBox data.</p>
            
            <div class="api-section">
                <h2>Available Endpoints</h2>
                
                <div class="endpoint">
                    <h3>GET /api/top-ideas.php</h3>
                    <p>Returns the top 5 most upvoted ideas.</p>
                    
                    <h4>Response Format:</h4>
                    <pre><code>{
  "success": true,
  "message": "Top ideas retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Idea Title",
      "description": "Idea description...",
      "category": "Technology",
      "author": "Author Name",
      "votes": 15,
      "comments": 8,
      "created_at": "2024-01-01T12:00:00+00:00",
      "url": "http://localhost/idea-detail.php?id=1"
    }
  ],
  "total": 5,
  "timestamp": "2024-01-01T12:00:00+00:00"
}</code></pre>
                    
                    <h4>Try it out:</h4>
                    <button onclick="testAPI()" class="btn btn-primary">Test API Endpoint</button>
                    <div id="api-result" class="api-result"></div>
                </div>
            </div>
            
            <div class="api-section">
                <h2>Usage Examples</h2>
                
                <h3>JavaScript (Fetch API)</h3>
                <pre><code>fetch('/api/top-ideas.php')
  .then(response => response.json())
  .then(data => {
    console.log('Top Ideas:', data.data);
  })
  .catch(error => {
    console.error('Error:', error);
  });</code></pre>
                
                <h3>cURL</h3>
                <pre><code>curl -X GET "http://localhost/api/top-ideas.php"</code></pre>
                
                <h3>PHP</h3>
                <pre><code>$response = file_get_contents('http://localhost/api/top-ideas.php');
$data = json_decode($response, true);
print_r($data['data']);</code></pre>
            </div>
            
            <div class="api-section">
                <h2>Response Codes</h2>
                <ul>
                    <li><strong>200 OK</strong> - Request successful</li>
                    <li><strong>500 Internal Server Error</strong> - Database or server error</li>
                </ul>
            </div>
            
            <p class="back-link">
                <a href="../dashboard.php">← Back to Dashboard</a>
            </p>
        </div>
    </div>
    
    <script>
        function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<p>Loading...</p>';
            
            fetch('../api/top-ideas.php')
                .then(response => response.json())
                .then(data => {
                    resultDiv.innerHTML = '<h4>API Response:</h4><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    resultDiv.innerHTML = '<p class="error">Error: ' + error.message + '</p>';
                });
        }
    </script>
</body>
</html>