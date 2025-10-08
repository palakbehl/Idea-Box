# IdeaBox - Idea Sharing Platform

A simple yet comprehensive web application for sharing and collaborating on ideas, built with vanilla HTML, CSS, JavaScript, PHP, and MySQL.

## 🚀 Features

### User System
- ✅ User registration with validation
- ✅ Secure login with PHP sessions
- ✅ Password reset functionality
- ✅ User profile management
- ✅ Edit profile and change password

### Idea Management
- ✅ Submit ideas with title, description, category
- ✅ Optional file attachments (images, documents)
- ✅ Browse and search ideas
- ✅ Filter by category and sort options
- ✅ Detailed idea view pages

### Interaction Features
- ✅ Upvote/downvote ideas
- ✅ Comment on ideas
- ✅ Real-time vote counting
- ✅ User engagement tracking

### Admin Panel
- ✅ Admin authentication
- ✅ Manage users (view, delete)
- ✅ Manage ideas (view, delete)
- ✅ Dashboard with statistics
- ✅ Search and filter capabilities

### API
- ✅ REST API endpoint for top ideas
- ✅ JSON responses with proper formatting
- ✅ API documentation page

### Design & UX
- ✅ Modern gradient-based design
- ✅ Responsive layout
- ✅ CSS animations and shadows
- ✅ Form validation with JavaScript
- ✅ Loading states and error handling

## 🛠️ Technical Stack

- **Frontend**: HTML5, CSS3 (with gradients & animations), Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache/Nginx with PHP support
- **No frameworks or external libraries used**

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PHP extensions: PDO, PDO_MySQL, GD (for file uploads)

## 🚀 Installation

### 1. Clone/Download the Project
```bash
git clone <repository-url> ideabox
# or download and extract the ZIP file
```

### 2. Set Up Database
1. Create a MySQL database named `ideabox`
2. Import the database schema:
```sql
mysql -u your_username -p ideabox < database_setup.sql
```

### 3. Configure Database Connection
Edit `php/config.php` and update the database settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ideabox');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Set Up File Permissions
Ensure the `uploads/` directory is writable:
```bash
chmod 755 uploads/
```

### 5. Configure Web Server
Point your web server document root to the project directory, or place the project in your web server's document root (e.g., `htdocs`, `www`, `public_html`).

### 6. Access the Application
Open your browser and navigate to:
```
http://localhost/ideabox/
```

## 👤 Default Admin Credentials
- **Username**: admin
- **Password**: admin123

Access admin panel at: `http://localhost/ideabox/admin/login.php`

## 📁 Project Structure

```
ideabox/
├── admin/                  # Admin panel files
│   ├── dashboard.php
│   ├── login.php
│   ├── users.php
│   └── ideas.php
├── api/                    # REST API endpoints
│   ├── index.php          # API documentation
│   └── top-ideas.php      # Top ideas endpoint
├── css/
│   └── style.css          # Main stylesheet with gradients
├── js/
│   ├── validation.js      # Form validation
│   └── voting.js         # Voting functionality
├── php/                   # Backend PHP files
│   ├── config.php         # Database configuration
│   ├── login.php          # Login handler
│   ├── register.php       # Registration handler
│   ├── submit-idea.php    # Idea submission
│   ├── vote.php          # Voting handler
│   ├── add-comment.php   # Comment handler
│   └── ...               # Other PHP handlers
├── uploads/              # File upload directory
├── database_setup.sql    # Database schema
├── index.php            # Homepage
├── login.php            # Login page
├── register.php         # Registration page
├── dashboard.php        # User dashboard
├── ideas.php           # Ideas listing
├── idea-detail.php     # Individual idea view
├── submit-idea.php     # Idea submission form
├── profile.php         # User profile
└── README.md           # This file
```

## 🎯 Usage Guide

### For Regular Users

1. **Registration/Login**
   - Visit the homepage
   - Click "Register" to create an account
   - Or click "Login" if you already have an account

2. **Submit Ideas**
   - After logging in, click "Submit New Idea"
   - Fill out the form with title, description, category
   - Optionally attach a file
   - Submit your idea

3. **Browse Ideas**
   - Click "Browse Ideas" to see all submitted ideas
   - Use search and filter options to find specific ideas
   - Sort by newest, oldest, most popular, or alphabetical

4. **Interact with Ideas**
   - Click on any idea to view details
   - Upvote ideas you like
   - Leave comments and engage in discussions

5. **Manage Profile**
   - Click "Profile" to edit your information
   - Change your password
   - View your account statistics

### For Administrators

1. **Access Admin Panel**
   - Go to `/admin/login.php`
   - Login with admin credentials

2. **Dashboard Overview**
   - View system statistics
   - See recent users and ideas
   - Monitor platform activity

3. **User Management**
   - View all registered users
   - Search users by name or email
   - Delete users if necessary (removes all their data)

4. **Idea Management**
   - View all submitted ideas
   - Filter by category or search
   - Delete inappropriate ideas
   - Monitor community content

## 🔧 API Usage

### Get Top Ideas
```bash
GET /api/top-ideas.php
```

**Response:**
```json
{
  "success": true,
  "message": "Top ideas retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Revolutionary App Idea",
      "description": "An innovative mobile application that...",
      "category": "Technology",
      "author": "John Doe",
      "votes": 25,
      "comments": 8,
      "created_at": "2024-01-15T10:30:00+00:00",
      "url": "http://localhost/ideabox/idea-detail.php?id=1"
    }
  ],
  "total": 5,
  "timestamp": "2024-01-15T12:00:00+00:00"
}
```

## 🔒 Security Features

- **Password Hashing**: Uses PHP's `password_hash()` function
- **SQL Injection Protection**: Prepared statements with PDO
- **XSS Prevention**: HTML sanitization on all outputs
- **File Upload Security**: Type and size validation
- **Session Management**: Secure PHP sessions
- **Input Validation**: Both client-side and server-side validation

## 🎨 Design Features

- **Modern Gradient Design**: Beautiful color schemes throughout
- **CSS Animations**: Smooth transitions and hover effects
- **Box Shadows**: Depth and modern card-based layouts
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Loading States**: Visual feedback for user actions
- **Form Validation**: Real-time validation with error messages

## 🧪 Testing

### Manual Testing Checklist

- [ ] User registration works
- [ ] User login/logout works
- [ ] Password reset functionality
- [ ] Profile editing
- [ ] Idea submission with and without files
- [ ] Idea browsing and filtering
- [ ] Voting system
- [ ] Commenting system
- [ ] Admin login and management
- [ ] API endpoint returns correct data
- [ ] Responsive design on different screen sizes

### Test User Accounts
You can create test accounts or use these for testing:
- Create multiple users to test interactions
- Submit various ideas in different categories
- Test voting and commenting between different users

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in `php/config.php`
   - Ensure MySQL service is running
   - Verify database exists and is accessible

2. **File Upload Issues**
   - Check `uploads/` directory permissions (755 or 777)
   - Verify PHP upload settings (`upload_max_filesize`, `post_max_size`)
   - Ensure directory exists and is writable

3. **Session Issues**
   - Check PHP session configuration
   - Ensure `session_start()` is called before any output
   - Verify session save path is writable

4. **CSS/JS Not Loading**
   - Check file paths are correct
   - Verify web server can serve static files
   - Check browser console for errors

### Debug Mode
To enable debug mode, add this to `php/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 🤝 Contributing

This is a educational project demonstrating vanilla web technologies. Feel free to:
- Fork the project
- Submit bug reports
- Suggest improvements
- Add new features following the same tech stack

## 📄 License

This project is for educational purposes. Feel free to use and modify as needed.

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review the project structure
3. Test with default admin credentials
4. Verify database setup and configuration

---

**Built with ❤️ using only vanilla HTML, CSS, JavaScript, PHP, and MySQL**"# Idea-Box" 
