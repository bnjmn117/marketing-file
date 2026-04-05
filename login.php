<?php
// Enable error reporting para makita ang errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Initialize variables
$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Please fill in all fields";
    } else {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = $_POST['password'];
        
        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check if user is verified
            if ($user['is_verified'] == 1) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    // Normalize course to match dashboard mapping (e.g., IT -> BSIT)
                    $normalizedCourse = strtoupper(trim($user['course']));
                    $normalizedCourse = preg_replace('/[^A-Z0-9]/', '', $normalizedCourse);
                    if (in_array($normalizedCourse, ['IT', 'BSCS', 'BSIS'], true)) {
                        $normalizedCourse = 'BSIT';
                    }
                    $_SESSION['course'] = $normalizedCourse;
                    $_SESSION['course_type'] = $user['course_type'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email'] = $user['email'];
                    
                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password!";
                    error_log("Failed login attempt for email: $email - Wrong password");
                }
            } else {
                $error = "Account not verified. Please check your email for OTP.";
                $success = "<a href='resend_otp.php?email=" . urlencode($email) . "' style='color: #C5A028; text-decoration: none; font-weight: 600;'>Resend OTP →</a>";
            }
        } else {
            $error = "Email not found!";
            error_log("Failed login attempt - Email not found: $email");
        }
        $stmt->close();
    }
}

// Check for success message from registration
if (isset($_GET['registered'])) {
    $success = "Registration successful! Please check your email for OTP verification.";
}

if (isset($_GET['verified'])) {
    $success = "Email verified successfully! You can now login.";
}

if (isset($_GET['loggedout'])) {
    $success = "You have been successfully logged out.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guimba Marketing System • Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --maroon-deep: #4A0404;
            --maroon-rich: #6B0F1A;
            --maroon-burgundy: #800020;
            --maroon-crimson: #9B1D2C;
            --maroon-brick: #B22222;
            --maroon-rose: #C21E56;
            --maroon-blush: #D84B5E;
            --maroon-dust: #E6A4B4;
            --maroon-light: #FADADD;
            
            --gold-primary: #C5A028;
            --gold-light: #E5C87B;
            --gold-dark: #8B6F1A;
            
            --gray-dark: #1A0F0F;
            --gray-medium: #4A3A3A;
            --gray-light: #FAF3F3;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #1A0404 0%, #2A0A0A 50%, #3B0F0F 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-ornament {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .ornament-1 {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 0, 32, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .ornament-2 {
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(197, 160, 40, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: floatReverse 25s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(50px, 50px) rotate(120deg); }
            66% { transform: translate(-20px, 30px) rotate(240deg); }
        }

        @keyframes floatReverse {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-50px, -30px) rotate(-120deg); }
            66% { transform: translate(30px, -50px) rotate(-240deg); }
        }

        /* Main Container */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 48px;
            box-shadow: 
                0 50px 100px -20px rgba(0, 0, 0, 0.5),
                0 30px 60px -30px rgba(139, 0, 32, 0.5),
                inset 0 1px 1px rgba(255, 255, 255, 0.3);
            overflow: hidden;
            border: 1px solid rgba(197, 160, 40, 0.2);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Section */
        .login-header {
            background: linear-gradient(135deg, var(--maroon-deep), var(--maroon-burgundy));
            padding: 50px 40px 30px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .login-header::before {
            content: '🔐';
            position: absolute;
            top: -30px;
            right: -30px;
            font-size: 200px;
            opacity: 0.1;
            transform: rotate(15deg);
            color: var(--gold-light);
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold-light), transparent);
        }

        .login-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .login-header h1 span {
            color: var(--gold-light);
            font-weight: 800;
        }

        .login-header p {
            color: var(--maroon-light);
            font-size: 1rem;
            font-weight: 300;
            position: relative;
            line-height: 1.6;
        }

        .header-badge {
            display: inline-block;
            background: rgba(197, 160, 40, 0.2);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(197, 160, 40, 0.3);
            color: var(--gold-light);
            padding: 8px 25px;
            border-radius: 40px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Form Section */
        .form-section {
            padding: 40px;
            background: white;
        }

        .welcome-back {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-back h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }

        .welcome-back p {
            color: var(--gray-medium);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--gray-dark);
            font-weight: 600;
            font-size: 0.95rem;
        }

        label i {
            color: var(--maroon-burgundy);
            margin-right: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--maroon-burgundy);
            font-size: 1.2rem;
            transition: all 0.3s;
            z-index: 1;
        }

        input {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 2px solid var(--maroon-light);
            border-radius: 16px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            background: var(--gray-light);
            transition: all 0.3s;
            color: var(--gray-dark);
        }

        input:hover {
            border-color: var(--maroon-dust);
            background: white;
        }

        input:focus {
            outline: none;
            border-color: var(--maroon-burgundy);
            background: white;
            box-shadow: 0 10px 25px -10px var(--maroon-burgundy);
        }

        input::placeholder {
            color: var(--maroon-dust);
            font-size: 0.95rem;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--maroon-burgundy);
            font-size: 1.2rem;
            transition: all 0.3s;
            z-index: 1;
            background: white;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .password-toggle:hover {
            color: var(--gold-primary);
            transform: translateY(-50%) scale(1.1);
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--maroon-burgundy);
            cursor: pointer;
        }

        .remember-me span {
            color: var(--gray-medium);
            font-size: 0.95rem;
        }

        .forgot-link {
            color: var(--maroon-burgundy);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--gold-primary), var(--maroon-burgundy));
            transition: width 0.3s;
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        .forgot-link:hover {
            color: var(--gold-dark);
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--maroon-burgundy), var(--maroon-crimson));
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 35px -10px var(--maroon-burgundy);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .login-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 45px -10px var(--maroon-burgundy);
        }

        .login-btn i {
            margin-right: 10px;
            transition: transform 0.3s;
        }

        .login-btn:hover i {
            transform: translateX(5px);
        }

        .login-btn.loading {
            color: transparent;
            pointer-events: none;
        }

        .login-btn .loader {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .login-btn.loading .loader {
            display: block;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Messages */
        .message {
            padding: 16px 20px;
            border-radius: 16px;
            margin: 0 40px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .message.error {
            background: #FFE5E5;
            color: var(--maroon-burgundy);
            border: 1px solid #FFB3B3;
        }

        .message.success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #A5D6A7;
        }

        .message i {
            font-size: 24px;
        }

        .message a {
            color: var(--gold-primary);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Register Link Section */
        .register-section {
            text-align: center;
            padding: 30px 40px 40px;
            background: linear-gradient(to bottom, white, var(--gray-light));
            border-top: 1px solid var(--maroon-light);
        }

        .register-section p {
            color: var(--gray-medium);
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .register-link {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
            color: white;
            text-decoration: none;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px -5px rgba(197, 160, 40, 0.3);
        }

        .register-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(197, 160, 40, 0.5);
        }

        .register-link i {
            margin-right: 8px;
        }

        .back-home {
            display: inline-block;
            margin-top: 20px;
            color: var(--gray-medium);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .back-home:hover {
            color: var(--maroon-burgundy);
        }

        /* Debug Info */
        .debug-info {
            margin: 20px 40px 0;
            padding: 15px;
            background: var(--gray-light);
            border-radius: 12px;
            font-size: 0.85rem;
            color: var(--gray-medium);
            border: 1px dashed var(--maroon-dust);
        }

        .debug-info h4 {
            color: var(--maroon-burgundy);
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-header h1 {
                font-size: 2rem;
            }
            
            .login-header {
                padding: 40px 20px;
            }
            
            .form-section {
                padding: 30px 20px;
            }
            
            .register-section {
                padding: 30px 20px;
            }
            
            .form-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .login-container {
                border-radius: 32px;
            }
        }

        /* Auto-hide animation for messages */
        .message.hiding {
            opacity: 0;
            transition: opacity 0.5s;
        }
    </style>
</head>
<body>
    <div class="bg-ornament">
        <div class="ornament-1"></div>
        <div class="ornament-2"></div>
    </div>

    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <h1>Guimba <span>Marketing</span></h1>
            <p>Your gateway to educational opportunities in Guimba, Nueva Ecija</p>
            <div class="header-badge">
                <span>✨</span> Secure Access Portal
            </div>
        </div>

        <!-- Messages -->
        <?php if (!empty($error)): ?>
            <div class="message error" id="errorMessage">
                <i>⚠️</i>
                <div>
                    <strong>Error:</strong> <?php echo $error; ?>
                    <?php if (isset($success) && !empty($success)) echo $success; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success) && empty($error)): ?>
            <div class="message success" id="successMessage">
                <i>✅</i>
                <div><?php echo $success; ?></div>
            </div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="form-section">
            <div class="welcome-back">
                <h2>Welcome Back! 👋</h2>
                <p>Please enter your credentials to continue</p>
            </div>

            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label><i>📧</i> Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉️</span>
                        <input type="email" 
                               name="email" 
                               placeholder="your.email@example.com" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i>🔒</i> Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔑</span>
                        <input type="password" 
                               id="password"
                               name="password" 
                               placeholder="Enter your password" 
                               required>
                        <span class="password-toggle" onclick="togglePassword()">👁️</span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn" id="submitBtn">
                    <i>🚀</i>
                    <span>Login to Dashboard</span>
                    <span class="loader"></span>
                </button>
            </form>
        </div>

        <!-- Register Section -->
        <div class="register-section">
            <p>New to Guimba Marketing System?</p>
            <a href="register.php" class="register-link">
                <i>✨</i> Create an Account
            </a>
            <a href="index.php" class="back-home">
                ← Back to Homepage
            </a>
        </div>

        <!-- Debug info (remove in production) -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="debug-info">
            <h4>🔧 Debug Information:</h4>
            <p>Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive'; ?></p>
            <p>PHP Version: <?php echo phpversion(); ?></p>
            <p>Server Time: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggle = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggle.textContent = '🔓';
            } else {
                passwordInput.type = 'password';
                toggle.textContent = '👁️';
            }
        }

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!email || !password) {
                e.preventDefault();
                showTemporaryMessage('Please fill in all fields', 'error');
                return;
            }
            
            // Show loading state
            document.getElementById('submitBtn').classList.add('loading');
        });

        // Show temporary message
        function showTemporaryMessage(text, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.innerHTML = `
                <i>${type === 'error' ? '⚠️' : '✅'}</i>
                <div>${text}</div>
            `;
            
            const formSection = document.querySelector('.form-section');
            formSection.insertBefore(messageDiv, formSection.firstChild);
            
            setTimeout(() => {
                messageDiv.classList.add('hiding');
                setTimeout(() => messageDiv.remove(), 500);
            }, 3000);
        }

        // Auto-hide messages
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.classList.add('hiding');
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);

        // Remove loading state when page loads (in case of back button)
        window.addEventListener('pageshow', function() {
            document.getElementById('submitBtn').classList.remove('loading');
        });

        // Input animations
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Remember me functionality
        const rememberCheckbox = document.querySelector('input[name="remember"]');
        const savedEmail = localStorage.getItem('savedEmail');
        
        if (savedEmail && rememberCheckbox) {
            document.querySelector('input[name="email"]').value = savedEmail;
            rememberCheckbox.checked = true;
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            if (rememberCheckbox && rememberCheckbox.checked) {
                const email = document.querySelector('input[name="email"]').value;
                localStorage.setItem('savedEmail', email);
            } else {
                localStorage.removeItem('savedEmail');
            }
        });
    </script>
</body>
</html>