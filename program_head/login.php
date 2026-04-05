<?php
session_start();
require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = '';

// Create program_heads table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS program_heads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    program_code VARCHAR(20) NOT NULL,
    program_type ENUM('college', 'highschool') NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($create_table_sql)) {
    error_log("Failed to create program_heads table: " . $conn->error);
}

// Insert default program heads if they don't exist
$check_heads = $conn->query("SELECT COUNT(*) as count FROM program_heads");
if ($check_heads) {
    $head_count = $check_heads->fetch_assoc()['count'];

    if ($head_count == 0) {
        // Default password: head123 (hashed)
        $default_password = password_hash('head123', PASSWORD_DEFAULT);
        
        $insert_heads = "INSERT INTO program_heads (username, password, program_code, program_type, fullname, email) VALUES
            -- College Program Heads
            ('bsit_head', '$default_password', 'BSIT', 'college', 'BSIT Program Head', 'bsit.head@coursereg.com'),
            ('hm_head', '$default_password', 'HM', 'college', 'HM Program Head', 'hm.head@coursereg.com'),
            ('oad_head', '$default_password', 'OAD', 'college', 'OAD Program Head', 'oad.head@coursereg.com'),
            ('crim_head', '$default_password', 'CRIM', 'college', 'CRIM Program Head', 'crim.head@coursereg.com'),
            ('educ_head', '$default_password', 'EDUC', 'college', 'EDUC Program Head', 'educ.head@coursereg.com'),
            
            -- High School Program Heads
            ('stem_head', '$default_password', 'STEM', 'highschool', 'STEM Program Head', 'stem.head@coursereg.com'),
            ('humms_head', '$default_password', 'HUMMS', 'highschool', 'HUMMS Program Head', 'humms.head@coursereg.com'),
            ('techvoc_head', '$default_password', 'TECHVOC', 'highschool', 'TECHVOC Program Head', 'techvoc.head@coursereg.com')";
        
        if (!$conn->query($insert_heads)) {
            error_log("Failed to insert program heads: " . $conn->error);
        }
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Use prepared statement to prevent SQL injection
        $sql = "SELECT * FROM program_heads WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $head = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $head['password'])) {
                    // Set session variables
                    $_SESSION['program_head_id'] = $head['id'];
                    $_SESSION['program_code'] = $head['program_code'];
                    $_SESSION['program_type'] = $head['program_type'];
                    $_SESSION['head_name'] = $head['fullname'];
                    $_SESSION['head_email'] = $head['email'];
                    
                    // Update last login
                    $update = "UPDATE program_heads SET last_login = NOW() WHERE id = ?";
                    $stmt2 = $conn->prepare($update);
                    if ($stmt2) {
                        $stmt2->bind_param("i", $head['id']);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                    
                    // Log successful login
                    error_log("Program Head Login Success: " . $username . " - " . $head['program_code']);
                    
                    // Redirect to program-specific dashboard
                    header("Location: program_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password!";
                    error_log("Program Head Login Failed: Invalid password for " . $username);
                }
            } else {
                $error = "Username not found!";
                error_log("Program Head Login Failed: Username not found - " . $username);
            }
            $stmt->close();
        } else {
            $error = "Database error. Please try again.";
            error_log("Program Head Login: Statement preparation failed - " . $conn->error);
        }
    }
}

// Check for logout message
if (isset($_GET['msg']) && $_GET['msg'] == 'loggedout') {
    $success = "You have been successfully logged out.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Head Login | Guimba Enrollment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: rotate(45deg) translate(-10%, -10%); }
            100% { transform: rotate(45deg) translate(10%, 10%); }
        }

        .login-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 550px;
            padding: 50px 40px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
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
            color: var(--dark);
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .header h1 i {
            color: var(--primary);
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .program-badges {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 30px;
        }

        .badge {
            padding: 10px 5px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .badge.bsit { background: linear-gradient(135deg, #3498DB, #2980B9); }
        .badge.hm { background: linear-gradient(135deg, #E67E22, #D35400); }
        .badge.oad { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
        .badge.crim { background: linear-gradient(135deg, #E74C3C, #C0392B); }
        .badge.educ { background: linear-gradient(135deg, #27AE60, #229954); }
        .badge.stem { background: linear-gradient(135deg, #3498DB, #2980B9); }
        .badge.humms { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
        .badge.techvoc { background: linear-gradient(135deg, #E67E22, #D35400); }

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
            transition: all 0.3s;
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
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        input:focus + i {
            color: var(--primary);
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .error, .success {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .success {
            background: #e8f5e8;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
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
            background: var(--light);
            border-radius: 16px;
            padding: 25px;
        }

        .demo-credentials h3 {
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-credentials h3 i {
            color: var(--warning);
        }

        .credential-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .credential-item {
            background: white;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            font-size: 12px;
            transition: all 0.3s;
        }

        .credential-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }

        .credential-item .program {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .credential-item .username {
            font-family: 'Courier New', monospace;
            background: var(--light);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            display: inline-block;
            margin-top: 5px;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-link a:hover {
            color: var(--primary);
        }

        /* Loading State */
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

        /* Responsive Design */
        @media (max-width: 600px) {
            .login-container {
                padding: 30px 20px;
            }

            .program-badges {
                grid-template-columns: repeat(2, 1fr);
            }

            .credential-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 24px;
            }
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background: var(--dark);
            color: white;
            border-radius: 5px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 10;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: 120%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>
                <i class="fas fa-chalkboard-teacher"></i>
                Program Head Portal
            </h1>
            <p>Login to access your program dashboard • Guimba Enrollment System</p>
        </div>

        <div class="program-badges">
            <span class="badge bsit" data-tooltip="Information Technology">BSIT</span>
            <span class="badge hm" data-tooltip="Hospitality Management">HM</span>
            <span class="badge oad" data-tooltip="Office Administration">OAD</span>
            <span class="badge crim" data-tooltip="Criminology">CRIM</span>
            <span class="badge educ" data-tooltip="Education">EDUC</span>
            <span class="badge stem" data-tooltip="Science & Technology">STEM</span>
            <span class="badge humms" data-tooltip="Humanities">HUMMS</span>
            <span class="badge techvoc" data-tooltip="Technical-Vocational">TECHVOC</span>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                    Username
                </label>
                <div class="input-icon">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           placeholder="Enter your username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required 
                           autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password" 
                           required>
                </div>
            </div>

            <button type="submit" id="submitBtn">
                <span>Login to Dashboard</span>
                <span class="loader"></span>
            </button>
        </form>

        <div class="demo-credentials">
            <h3>
                <i class="fas fa-key"></i>
                Demo Credentials
            </h3>
            <div class="credential-grid">
                <div class="credential-item">
                    <div class="program">🎓 BSIT</div>
                    <div><span class="username">bsit_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">🍽️ HM</div>
                    <div><span class="username">hm_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">📋 OAD</div>
                    <div><span class="username">oad_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">🔍 CRIM</div>
                    <div><span class="username">crim_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">📚 EDUC</div>
                    <div><span class="username">educ_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">🔬 STEM</div>
                    <div><span class="username">stem_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">📖 HUMMS</div>
                    <div><span class="username">humms_head</span> / head123</div>
                </div>
                <div class="credential-item">
                    <div class="program">🛠️ TECHVOC</div>
                    <div><span class="username">techvoc_head</span> / head123</div>
                </div>
            </div>
        </div>

        <div class="back-link">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i>
                Back to Main Site
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

        // Auto-hide error/success messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.error, .success');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Add floating effect to badges
        const badges = document.querySelectorAll('.badge');
        badges.forEach((badge, index) => {
            badge.style.animation = `float ${2 + index * 0.2}s ease-in-out infinite`;
        });

        // Add floating animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-3px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>