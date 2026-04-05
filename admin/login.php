<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Fix the path to config.php
require_once __DIR__ . '/../config.php';

$error = '';

// Create admin users table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    admin_type ENUM('main', 'college', 'highschool') NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($create_table_sql);

// Insert default admin accounts if they don't exist
$check_admins = $conn->query("SELECT COUNT(*) as count FROM admin_users");
$admin_count = $check_admins->fetch_assoc()['count'];

if ($admin_count == 0) {
    // Default password: admin123 (hashed)
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $insert_admins = "INSERT INTO admin_users (username, password, admin_type, fullname, email) VALUES
        ('mainadmin', '$default_password', 'main', 'Main Administrator', 'mainadmin@coursereg.com'),
        ('collegeadmin', '$default_password', 'college', 'College Administrator', 'collegeadmin@coursereg.com'),
        ('hsadmin', '$default_password', 'highschool', 'High School Administrator', 'hsadmin@coursereg.com')";
    
    $conn->query($insert_admins);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        $sql = "SELECT * FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            // Use password_verify for proper password checking
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_type'] = $admin['admin_type'];
                $_SESSION['admin_name'] = $admin['fullname'];
                
                // Update last login
                $update = "UPDATE admin_users SET last_login = NOW() WHERE id = ?";
                $stmt2 = $conn->prepare($update);
                $stmt2->bind_param("i", $admin['id']);
                $stmt2->execute();
                
                // Redirect based on admin type
                switch($admin['admin_type']) {
                    case 'main':
                        header("Location: main_dashboard.php");
                        break;
                    case 'college':
                        header("Location: college_dashboard.php");
                        break;
                    case 'highschool':
                        header("Location: highschool_dashboard.php");
                        break;
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Course Registration System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2C3E50 0%, #3498DB 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
            padding: 50px 40px;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2C3E50;
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7F8C8D;
            font-size: 14px;
        }
        
        .admin-badges {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.main {
            background: #2C3E50;
            color: white;
        }
        
        .badge.college {
            background: #27AE60;
            color: white;
        }
        
        .badge.highschool {
            background: #E67E22;
            color: white;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }
        
        input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #3498DB;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
        }
        
        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2C3E50 0%, #3498DB 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #c62828;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .demo-credentials {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            font-size: 14px;
        }
        
        .demo-credentials h3 {
            color: #2C3E50;
            margin-bottom: 15px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #e0e0e0;
        }
        
        .credential-item:last-child {
            border-bottom: none;
        }
        
        .credential-item .role {
            color: #666;
        }
        
        .credential-item .value {
            background: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            color: #2C3E50;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #7F8C8D;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-link a:hover {
            color: #3498DB;
        }
        
        .loader {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        button.loading {
            color: transparent;
        }
        
        button.loading .loader {
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>🔐 Admin Portal</h1>
            <p>Course Registration System • Guimba, Nueva Ecija</p>
        </div>
        
        <div class="admin-badges">
            <span class="badge main">Main Admin</span>
            <span class="badge college">College Admin</span>
            <span class="badge highschool">HS Admin</span>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <i>👤</i>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           placeholder="Enter your username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i>🔒</i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password" 
                           required>
                </div>
            </div>
            
            <button type="submit" id="submitBtn">
                <span>Login to Admin Panel</span>
                <span class="loader"></span>
            </button>
        </form>
        
        <div class="demo-credentials">
            <h3>📋 Demo Credentials</h3>
            <div class="credential-item">
                <span class="role">👑 Main Admin</span>
                <span class="value">mainadmin / admin123</span>
            </div>
            <div class="credential-item">
                <span class="role">🎓 College Admin</span>
                <span class="value">collegeadmin / admin123</span>
            </div>
            <div class="credential-item">
                <span class="role">🏫 High School Admin</span>
                <span class="value">hsadmin / admin123</span>
            </div>
        </div>
        
        <div class="back-link">
            <a href="../index.php">
                <span>←</span>
                <span>Back to Main Site</span>
            </a>
        </div>
    </div>

    <script>
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please enter both username and password');
                return;
            }
            
            document.getElementById('submitBtn').classList.add('loading');
        });

        // Remove loading state when page loads (in case of back button)
        window.addEventListener('pageshow', function() {
            document.getElementById('submitBtn').classList.remove('loading');
        });

        // Auto-hide error after 5 seconds
        setTimeout(function() {
            const errorDiv = document.querySelector('.error');
            if (errorDiv) {
                errorDiv.style.transition = 'opacity 0.5s';
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>