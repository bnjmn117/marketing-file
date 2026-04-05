<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'marketing');

// Email configuration (Gmail)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'zaramalongembanacido@gmail.com'); // Replace with your Gmail
define('SMTP_PASS', 'qqdm thhg phel tfrn'); // Replace with Gmail App Password
define('SMTP_FROM', 'zaramalongembanacido@gmail.com');
define('SMTP_FROM_NAME', 'OUR LADY SCARED HEART COLLEGE OF GUIMBA');

// Site configuration
define('SITE_NAME', 'Guimba Enrollment System');
define('SITE_URL', 'http://localhost/marketing');
define('ADMIN_EMAIL', 'admin@guimba-enrollment.edu.ph');

// Create database connection with error handling
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error);
    die("
        <div style='font-family: Arial; padding: 20px; background: #ffebee; border-left: 4px solid #c62828; margin: 20px;'>
            <h2 style='color: #c62828;'>Database Connection Error</h2>
            <p>Unable to connect to the database. Please check:</p>
            <ul>
                <li>If MySQL is running in XAMPP</li>
                <li>If database 'marketing' exists</li>
                <li>Database credentials in config.php</li>
            </ul>
            <p><strong>Error:</strong> " . $conn->connect_error . "</p>
            <p><a href='install.php' style='background: #2e7d32; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Installation</a></p>
        </div>
    ");
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

/**
 * Function to send OTP via email using PHP's mail() function
 * Enhanced version with better error handling
 */
function sendOTP($email, $otp) {
    $to = $email;
    $subject = "Your OTP Verification Code - " . SITE_NAME;
    
    // Email template
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #f0f2f5;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 40px;
                text-align: center;
            }
            .header h1 {
                font-size: 28px;
                margin-bottom: 10px;
            }
            .header p {
                font-size: 16px;
                opacity: 0.9;
            }
            .content {
                padding: 40px;
            }
            .greeting {
                font-size: 18px;
                color: #333;
                margin-bottom: 20px;
            }
            .otp-container {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                margin: 30px 0;
            }
            .otp-code {
                font-size: 48px;
                font-weight: bold;
                letter-spacing: 10px;
                color: white;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            }
            .expiry {
                color: #ffd700;
                margin-top: 10px;
                font-size: 14px;
            }
            .info-box {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
                border-left: 4px solid #667eea;
            }
            .info-box h3 {
                color: #333;
                margin-bottom: 10px;
            }
            .info-box ul {
                padding-left: 20px;
                color: #666;
            }
            .info-box li {
                margin: 5px 0;
            }
            .warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                border-radius: 5px;
                margin-top: 20px;
            }
            .warning p {
                color: #856404;
                margin: 0;
            }
            .footer {
                background: #f8f9fa;
                padding: 20px;
                text-align: center;
                border-top: 1px solid #e0e0e0;
            }
            .footer p {
                color: #666;
                font-size: 14px;
            }
            .school-name {
                font-weight: bold;
                color: #667eea;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎓 " . SITE_NAME . "</h1>
                <p>Email Verification</p>
            </div>
            
            <div class='content'>
                <div class='greeting'>
                    Hello,
                </div>
                
                <p>Thank you for registering with " . SITE_NAME . ". Please use the following One-Time Password (OTP) to verify your email address:</p>
                
                <div class='otp-container'>
                    <div class='otp-code'>" . $otp . "</div>
                    <div class='expiry'>⏰ Valid for 10 minutes only</div>
                </div>
                
                <div class='info-box'>
                    <h3>📋 Next Steps:</h3>
                    <ul>
                        <li>Enter this 6-digit code on the verification page</li>
                        <li>Complete your student registration</li>
                        <li>Access your personalized course dashboard</li>
                        <li>Start your academic journey with us</li>
                    </ul>
                </div>
                
                <div class='warning'>
                    <p>⚠️ <strong>Security Notice:</strong> Never share this OTP with anyone. Our staff will never ask for your verification code.</p>
                </div>
                
                <p style='color: #666; font-size: 14px; margin-top: 20px;'>
                    If you didn't request this verification, please ignore this email or contact our support team.
                </p>
            </div>
            
            <div class='footer'>
                <p>© " . date('Y') . " <span class='school-name'>" . SITE_NAME . "</span></p>
                <p>Guimba, Nueva Ecija, Philippines</p>
                <p style='font-size: 12px; margin-top: 10px;'>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">" . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("sendOTP: Invalid email address - $email");
        return false;
    }
    
    // Try to send email
    try {
        if (mail($to, $subject, $message, $headers)) {
            error_log("sendOTP: OTP sent successfully to $email");
            return true;
        } else {
            error_log("sendOTP: Failed to send email to $email");
            
            // For development - store OTP in session
            $_SESSION['debug_otp'] = $otp;
            $_SESSION['debug_email'] = $email;
            
            return false;
        }
    } catch (Exception $e) {
        error_log("sendOTP: Exception - " . $e->getMessage());
        $_SESSION['debug_otp'] = $otp;
        return false;
    }
}

/**
 * Alternative: PHPMailer function (commented out - requires PHPMailer library)
 * Uncomment if you have PHPMailer installed via Composer
 */
/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function sendOTPWithPHPMailer($email, $otp) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($email);
        $mail->addReplyTo(ADMIN_EMAIL, 'Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code - " . SITE_NAME;
        
        // HTML email template here
        $mail->Body = $message; // Use the same HTML template
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
*/

/**
 * Function to generate OTP
 */
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

/**
 * Function to get schools by type
 * Enhanced with error handling
 */
function getSchools($conn, $type = null) {
    $schools = [];
    
    try {
        $sql = "SELECT id, school_name, school_type, address FROM schools WHERE is_active = 1";
        if ($type) {
            $sql .= " AND (school_type = ? OR school_type = 'both')";
        }
        $sql .= " ORDER BY school_name";
        
        if ($type) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $schools[] = $row;
            }
        }
    } catch (Exception $e) {
        error_log("getSchools Error: " . $e->getMessage());
    }
    
    return $schools;
}

/**
 * Function to get school name by ID
 */
function getSchoolName($conn, $school_id) {
    if (!$school_id) return 'Not specified';
    
    $sql = "SELECT school_name FROM schools WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $school_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['school_name'];
    }
    return 'Unknown School';
}

/**
 * Function to sanitize input
 */
function sanitize($conn, $input) {
    if (is_array($input)) {
        return array_map(function($item) use ($conn) {
            return mysqli_real_escape_string($conn, htmlspecialchars(trim($item), ENT_QUOTES, 'UTF-8'));
        }, $input);
    }
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8'));
}

/**
 * Function to display messages
 */
function showMessage($message, $type = 'success') {
    $colors = [
        'success' => '#d4edda',
        'error' => '#f8d7da',
        'info' => '#d1ecf1',
        'warning' => '#fff3cd'
    ];
    
    $textColors = [
        'success' => '#155724',
        'error' => '#721c24',
        'info' => '#0c5460',
        'warning' => '#856404'
    ];
    
    $borderColors = [
        'success' => '#c3e6cb',
        'error' => '#f5c6cb',
        'info' => '#bee5eb',
        'warning' => '#ffeeba'
    ];
    
    echo "<div style='
        padding: 15px 20px;
        margin: 15px 0;
        background-color: {$colors[$type]};
        color: {$textColors[$type]};
        border: 1px solid {$borderColors[$type]};
        border-radius: 8px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    '>";
    
    // Add icon based on type
    $icons = [
        'success' => '✅',
        'error' => '❌',
        'info' => 'ℹ️',
        'warning' => '⚠️'
    ];
    
    echo "<span style='font-size: 18px;'>{$icons[$type]}</span>";
    echo "<span>{$message}</span>";
    echo "</div>";
}

/**
 * Function to check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Function to redirect with message
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'text' => $message,
        'type' => $type
    ];
    header("Location: $url");
    exit();
}

/**
 * Function to display flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        showMessage($msg['text'], $msg['type']);
        unset($_SESSION['flash_message']);
    }
}

/**
 * Function to get user by ID
 */
function getUserById($conn, $user_id) {
    $sql = "SELECT u.*, s.school_name 
            FROM users u 
            LEFT JOIN schools s ON u.previous_school_id = s.id 
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Function to check if email exists
 */
function emailExists($conn, $email) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

/**
 * Function to get enrollment statistics
 */
function getEnrollmentStats($conn) {
    $stats = [
        'college' => [],
        'highschool' => [],
        'total' => 0
    ];
    
    // College courses
    $college_courses = ['BSIT', 'HM', 'OAD', 'CRIM', 'EDUC'];
    foreach ($college_courses as $course) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE course = ? AND course_type = 'college' AND is_verified = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $course);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['college'][$course] = $result->fetch_assoc()['count'];
    }
    
    // High school strands
    $hs_strands = ['STEM', 'HUMMS', 'TECHVOC'];
    foreach ($hs_strands as $strand) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE course = ? AND course_type = 'highschool' AND is_verified = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $strand);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['highschool'][$strand] = $result->fetch_assoc()['count'];
    }
    
    // Total
    $sql = "SELECT COUNT(*) as total FROM users WHERE is_verified = 1";
    $result = $conn->query($sql);
    $stats['total'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

// Test database connection (optional - comment out in production)
/*
if ($conn->ping()) {
    error_log("Database connected successfully to " . DB_NAME);
}
*/
?>