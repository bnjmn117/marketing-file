<?php
require_once 'config.php';

if (!isset($_SESSION['temp_email']) && !isset($_GET['debug'])) {
    header("Location: register.php");
    exit();
}

$error = '';
$success = '';
$debug_mode = isset($_GET['debug']) || isset($_SESSION['debug_mode']);
$email = isset($_SESSION['temp_email']) ? $_SESSION['temp_email'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = mysqli_real_escape_string($conn, trim($_POST['otp']));
    
    if (empty($otp)) {
        $error = "Please enter the OTP code";
    } elseif (!preg_match('/^\d{6}$/', $otp)) {
        $error = "OTP must be a 6-digit number";
    } else {
        if ($debug_mode && isset($_SESSION['debug_otp']) && $otp == $_SESSION['debug_otp']) {
            // Debug mode verification
            $email = $_SESSION['temp_email'];
            $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL WHERE email = '$email'");
            unset($_SESSION['temp_email']);
            unset($_SESSION['debug_otp']);
            $_SESSION['success'] = "Email verified successfully! (Debug Mode)";
            header("Location: login.php");
            exit();
        } else {
            // Normal verification
            $email = $_SESSION['temp_email'];
            $current_time = date('Y-m-d H:i:s');
            
            $sql = "SELECT id FROM users WHERE email = '$email' AND otp_code = '$otp' AND otp_expires > '$current_time' AND is_verified = 0";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL WHERE email = '$email'");
                unset($_SESSION['temp_email']);
                $_SESSION['success'] = "Email verified successfully! You can now login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Invalid or expired OTP!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Guimba Enrollment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        
        .info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
        
        .email-display {
            background: #f0f4f8;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #667eea;
        }
        
        .otp-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 10px;
            margin-bottom: 20px;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 10px;
        }
        
        .resend-btn {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .debug-info {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }
        
        .debug-otp {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #999;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📧 Verify Your Email</h2>
        <div class="info">Enter the 6-digit code sent to:</div>
        <div class="email-display"><?php echo htmlspecialchars($email); ?></div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($debug_mode && isset($_SESSION['debug_otp'])): ?>
            <div class="debug-info">
                <strong>🔧 Debug Mode</strong>
                <div class="debug-otp"><?php echo $_SESSION['debug_otp']; ?></div>
                <p>Use this OTP for testing</p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="text" name="otp" class="otp-input" maxlength="6" pattern="\d{6}" required placeholder="000000">
            <button type="submit">Verify OTP</button>
        </form>
        
        <form method="POST" action="">
            <button type="submit" name="resend_otp" class="resend-btn">Resend OTP</button>
        </form>
        
        <div class="back-link">
            <a href="register.php">← Back to Registration</a>
        </div>
    </div>
</body>
</html>