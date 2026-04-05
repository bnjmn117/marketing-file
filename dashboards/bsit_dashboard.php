<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if loading animation should be shown
if (!isset($_SESSION['loading_shown'])) {
    $_SESSION['loading_shown'] = true;
    $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['loading_course'] = 'BSIT';
    header("Location: loading.php");
    exit();
}

require_once '../config.php';

// Check if user is logged in and is BSIT student
if (!isset($_SESSION['user_id']) || $_SESSION['course'] != 'BSIT') {
    unset($_SESSION['loading_shown']);
    unset($_SESSION['loading_course']);
    header("Location: ../login.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.*, s.school_name as school_name 
        FROM users u 
        LEFT JOIN schools s ON u.previous_school_id = s.id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Determine school display
$school_display = '';
if (!empty($user['previous_school_id'])) {
    $school_display = $user['school_name'];
} elseif (!empty($user['previous_school_other'])) {
    $school_display = $user['previous_school_other'] . ' (Other)';
} else {
    $school_display = 'Not specified';
}

// Get current time for greeting
$hour = date('H');
$greeting = '';
if ($hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour < 18) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Server • College of Information Technology</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bsit-green: #27AE60;
            --bsit-green-dark: #229954;
            --bsit-green-light: #7DCEA0;
            --bsit-green-pale: #E8F5E9;
            
            --gray-dark: #1A1F2C;
            --gray-medium: #2A3142;
            --gray-light: #F8FAFC;
            --gray-border: rgba(0, 0, 0, 0.05);
            
            --success: #2E7D4D;
            --warning: #F4A261;
            --danger: #D45D5D;
            
            --card-shadow: 0 20px 40px -10px rgba(39, 174, 96, 0.15);
            --hover-shadow: 0 30px 50px -12px rgba(39, 174, 96, 0.25);
            --glass-effect: rgba(255, 255, 255, 0.7);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #F0F2F5 0%, #E8EAED 100%);
            min-height: 100vh;
            color: var(--gray-dark);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--bsit-green);
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--bsit-green-dark);
        }

        /* Main Container */
        .enterprise-dashboard {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 16px 24px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            box-shadow: 0 8px 16px rgba(39, 174, 96, 0.2);
        }

        .brand-text h1 {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .brand-text p {
            font-size: 0.85rem;
            color: var(--gray-medium);
            font-weight: 500;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 8px 16px 8px 8px;
            background: var(--gray-light);
            border-radius: 100px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
        }

        .user-info {
            line-height: 1.4;
        }

        .user-name {
            font-weight: 600;
            color: var(--gray-dark);
        }

        .user-greeting {
            font-size: 0.85rem;
            color: var(--bsit-green);
            font-weight: 500;
        }

        .logout-btn {
            background: white;
            border: 1px solid #e0e0e0;
            color: var(--gray-medium);
            padding: 10px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: var(--bsit-green);
            border-color: var(--bsit-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(39, 174, 96, 0.2);
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(105deg, white 0%, white 50%, #E8F5E9 100%);
            border-radius: 30px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(39, 174, 96, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .banner-content {
            position: relative;
            z-index: 1;
        }

        .banner-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--gray-dark);
        }

        .banner-content h2 span {
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .banner-content p {
            color: var(--gray-medium);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .banner-stats {
            display: flex;
            gap: 40px;
        }

        .banner-stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-circle {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--bsit-green);
            box-shadow: 0 8px 16px rgba(39, 174, 96, 0.1);
        }

        .stat-text h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gray-dark);
        }

        .stat-text p {
            color: var(--gray-medium);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--bsit-green), var(--bsit-green-light));
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            background: #E8F5E9;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--bsit-green);
        }

        .kpi-trend {
            padding: 4px 12px;
            background: #E8F5E9;
            border-radius: 40px;
            color: var(--bsit-green);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }

        .kpi-label {
            color: var(--gray-medium);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Server Grid Section */
        .server-grid-section {
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            font-size: 28px;
            color: var(--bsit-green);
        }

        .section-title h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-dark);
        }

        .section-badge {
            background: #E8F5E9;
            color: var(--bsit-green);
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Server Grid */
        .server-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
        }

        .server-card {
            background: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .server-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--hover-shadow);
        }

        .server-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(39, 174, 96, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.4s;
        }

        .server-card:hover::after {
            opacity: 1;
        }

        .server-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .server-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);
        }

        .server-status {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #E8F5E9;
            padding: 6px 16px;
            border-radius: 40px;
            color: var(--bsit-green);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background: var(--bsit-green);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--bsit-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.2); }
        }

        .server-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }

        .server-subtitle {
            color: var(--bsit-green);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .server-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            background: var(--gray-light);
            border-radius: 20px;
            padding: 20px;
            margin: 20px 0;
        }

        .server-stat-item {
            text-align: center;
        }

        .server-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-dark);
        }

        .server-stat-label {
            font-size: 0.9rem;
            color: var(--gray-medium);
            font-weight: 500;
        }

        .server-progress {
            margin: 20px 0;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            color: var(--gray-medium);
            margin-bottom: 8px;
        }

        .progress-bar {
            height: 10px;
            background: #E8F5E9;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--bsit-green), var(--bsit-green-dark));
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .server-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .access-server {
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 16px rgba(39, 174, 96, 0.2);
            text-decoration: none;
            display: inline-block;
        }

        .access-server:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(39, 174, 96, 0.3);
            background: linear-gradient(135deg, var(--bsit-green-dark), var(--bsit-green));
        }

        .server-meta {
            color: var(--gray-medium);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Role Selection Modal */
        .role-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 40px;
            padding: 50px;
            max-width: 650px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(39, 174, 96, 0.3);
            transform: scale(0.9);
            animation: modalPop 0.3s forwards;
        }

        @keyframes modalPop {
            to { transform: scale(1); }
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            margin: 0 auto 20px;
            box-shadow: 0 15px 30px rgba(39, 174, 96, 0.3);
        }

        .modal-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--bsit-green), var(--bsit-green-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-content p {
            color: var(--gray-medium);
            margin-bottom: 40px;
            font-size: 1.1rem;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .role-card {
            background: var(--gray-light);
            border: 2px solid transparent;
            border-radius: 28px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .role-card:hover {
            border-color: var(--bsit-green);
            background: white;
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .role-card i {
            font-size: 3rem;
            display: block;
            margin-bottom: 15px;
        }

        .role-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }

        .role-card p {
            font-size: 0.9rem;
            color: var(--gray-medium);
            margin: 0;
        }

        .modal-close {
            background: var(--gray-light);
            border: none;
            color: var(--gray-medium);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .modal-close:hover {
            background: var(--danger);
            color: white;
        }

        /* Facilities Section */
        .facilities-section {
            background: white;
            border-radius: 28px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: var(--card-shadow);
        }

        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .facility-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 6px solid var(--bsit-green);
            position: relative;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .facility-card:hover {
            transform: translateX(10px);
            box-shadow: var(--hover-shadow);
        }

        .facility-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .facility-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gray-dark);
        }

        .facility-badge {
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-active { background: #E8F5E9; color: var(--bsit-green); }

        .facility-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 20px 0;
        }

        .metric-item {
            background: var(--gray-light);
            padding: 12px;
            border-radius: 14px;
            text-align: center;
        }

        .metric-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gray-dark);
        }

        .metric-label {
            font-size: 0.8rem;
            color: var(--gray-medium);
        }

        .facility-features {
            list-style: none;
        }

        .facility-features li {
            padding: 8px 0;
            color: var(--gray-medium);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .facility-features li i {
            color: var(--bsit-green);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
            
            .banner-stats {
                flex-direction: column;
                gap: 20px;
            }
            
            .top-nav {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .server-grid {
                grid-template-columns: 1fr;
            }
            
            .facilities-grid {
                grid-template-columns: 1fr;
            }
            
            .role-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f1f1f1;
            border-top: 3px solid var(--bsit-green);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="enterprise-dashboard">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="nav-brand">
                <div class="brand-icon">
                    <span>💻</span>
                </div>
                <div class="brand-text">
                    <h1>OLSCHO · SERVER</h1>
                    <p>Enterprise Management System v3.0</p>
                </div>
            </div>
            
            <div class="nav-right">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($user['fullname']); ?></div>
                        <div class="user-greeting"><?php echo $greeting; ?>!</div>
                    </div>
                </div>
                <a href="../logout.php" class="logout-btn">
                    <span>🔒</span>
                    <span>Secure Logout</span>
                </a>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="banner-content">
                <h2>Welcome back, <span><?php echo htmlspecialchars(explode(' ', $user['fullname'])[0]); ?></span></h2>
                <p>Monitor all department servers and facilities from one central hub.</p>
                
                <div class="banner-stats">
                    <div class="banner-stat-item">
                        <div class="stat-circle">⚡</div>
                        <div class="stat-text">
                            <h4>99.9%</h4>
                            <p>System Uptime</p>
                        </div>
                    </div>
                    <div class="banner-stat-item">
                        <div class="stat-circle">🌐</div>
                        <div class="stat-text">
                            <h4>5</h4>
                            <p>Active Servers</p>
                        </div>
                    </div>
                    <div class="banner-stat-item">
                        <div class="stat-circle">👥</div>
                        <div class="stat-text">
                            <h4>1,552</h4>
                            <p>Total Enrollees</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon">📊</div>
                    <span class="kpi-trend">+12.5%</span>
                </div>
                <div class="kpi-value">1,247</div>
                <div class="kpi-label">Total Students</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon">👨‍🏫</div>
                    <span class="kpi-trend">+5.2%</span>
                </div>
                <div class="kpi-value">89</div>
                <div class="kpi-label">Faculty Members</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon">💻</div>
                    <span class="kpi-trend">Active</span>
                </div>
                <div class="kpi-value">12</div>
                <div class="kpi-label">Server Clusters</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon">🏛️</div>
                    <span class="kpi-trend">8</span>
                </div>
                <div class="kpi-value">24</div>
                <div class="kpi-label">Facilities</div>
            </div>
        </div>

        <!-- Department Servers Section -->
        <div class="server-grid-section">
            <div class="section-header">
                <div class="section-title">
                    <i>🖥️</i>
                    <h2>Department Servers</h2>
                </div>
                <div class="section-badge">All Systems Operational</div>
            </div>

            <div class="server-grid">
                <!-- BSCRIM Server -->
                <div class="server-card" onclick="showRoleModal('crim')">
                    <div class="server-card-header">
                        <div class="server-icon">⚖️</div>
                        <div class="server-status">
                            <span class="status-dot"></span>
                            <span>Online</span>
                        </div>
                    </div>
                    <h3 class="server-title">BSCRIM Server</h3>
                    <div class="server-subtitle">College of Criminology</div>
                    
                    <div class="server-stats">
                        <div class="server-stat-item">
                            <div class="server-stat-value">342</div>
                            <div class="server-stat-label">Students</div>
                        </div>
                        <div class="server-stat-item">
                            <div class="server-stat-value">98%</div>
                            <div class="server-stat-label">Capacity</div>
                        </div>
                    </div>

                    <div class="server-progress">
                        <div class="progress-header">
                            <span>Server Load</span>
                            <span>67%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 67%"></div>
                        </div>
                    </div>

                    <div class="server-footer">
                        <button class="access-server" onclick="event.stopPropagation(); showRoleModal('crim')">
                            Access Server →
                        </button>
                        <span class="server-meta">
                            <span>⚡</span> 99.9% uptime
                        </span>
                    </div>
                </div>

                <!-- BSIT Server -->
                <div class="server-card" onclick="showRoleModal('it')">
                    <div class="server-card-header">
                        <div class="server-icon">💻</div>
                        <div class="server-status">
                            <span class="status-dot"></span>
                            <span>Online</span>
                        </div>
                    </div>
                    <h3 class="server-title">BSIT Server</h3>
                    <div class="server-subtitle">College of Information Technology</div>
                    
                    <div class="server-stats">
                        <div class="server-stat-item">
                            <div class="server-stat-value">456</div>
                            <div class="server-stat-label">Students</div>
                        </div>
                        <div class="server-stat-item">
                            <div class="server-stat-value">94%</div>
                            <div class="server-stat-label">Capacity</div>
                        </div>
                    </div>

                    <div class="server-progress">
                        <div class="progress-header">
                            <span>Server Load</span>
                            <span>82%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 82%"></div>
                        </div>
                    </div>

                    <div class="server-footer">
                        <button class="access-server" onclick="event.stopPropagation(); showRoleModal('it')">
                            Access Server →
                        </button>
                        <span class="server-meta">
                            <span>⚡</span> 99.9% uptime
                        </span>
                    </div>
                </div>

                <!-- BSED Server -->
                <div class="server-card" onclick="showRoleModal('educ')">
                    <div class="server-card-header">
                        <div class="server-icon">📚</div>
                        <div class="server-status">
                            <span class="status-dot"></span>
                            <span>Online</span>
                        </div>
                    </div>
                    <h3 class="server-title">BSED Server</h3>
                    <div class="server-subtitle">College of Education</div>
                    
                    <div class="server-stats">
                        <div class="server-stat-item">
                            <div class="server-stat-value">289</div>
                            <div class="server-stat-label">Students</div>
                        </div>
                        <div class="server-stat-item">
                            <div class="server-stat-value">76%</div>
                            <div class="server-stat-label">Capacity</div>
                        </div>
                    </div>

                    <div class="server-progress">
                        <div class="progress-header">
                            <span>Server Load</span>
                            <span>45%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 45%"></div>
                        </div>
                    </div>

                    <div class="server-footer">
                        <button class="access-server" onclick="event.stopPropagation(); showRoleModal('educ')">
                            Access Server →
                        </button>
                        <span class="server-meta">
                            <span>⚡</span> 99.9% uptime
                        </span>
                    </div>
                </div>

                <!-- BSOA Server -->
                <div class="server-card" onclick="showRoleModal('oad')">
                    <div class="server-card-header">
                        <div class="server-icon">📋</div>
                        <div class="server-status">
                            <span class="status-dot"></span>
                            <span>Online</span>
                        </div>
                    </div>
                    <h3 class="server-title">BSOA Server</h3>
                    <div class="server-subtitle">Office Administration</div>
                    
                    <div class="server-stats">
                        <div class="server-stat-item">
                            <div class="server-stat-value">198</div>
                            <div class="server-stat-label">Students</div>
                        </div>
                        <div class="server-stat-item">
                            <div class="server-stat-value">52%</div>
                            <div class="server-stat-label">Capacity</div>
                        </div>
                    </div>

                    <div class="server-progress">
                        <div class="progress-header">
                            <span>Server Load</span>
                            <span>38%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 38%"></div>
                        </div>
                    </div>

                    <div class="server-footer">
                        <button class="access-server" onclick="event.stopPropagation(); showRoleModal('oad')">
                            Access Server →
                        </button>
                        <span class="server-meta">
                            <span>⚡</span> 99.9% uptime
                        </span>
                    </div>
                </div>

                <!-- HM Server -->
                <div class="server-card" onclick="showRoleModal('hm')">
                    <div class="server-card-header">
                        <div class="server-icon">🍽️</div>
                        <div class="server-status">
                            <span class="status-dot"></span>
                            <span>Online</span>
                        </div>
                    </div>
                    <h3 class="server-title">HM Server</h3>
                    <div class="server-subtitle">Hospitality Management</div>
                    
                    <div class="server-stats">
                        <div class="server-stat-item">
                            <div class="server-stat-value">267</div>
                            <div class="server-stat-label">Students</div>
                        </div>
                        <div class="server-stat-item">
                            <div class="server-stat-value">71%</div>
                            <div class="server-stat-label">Capacity</div>
                        </div>
                    </div>

                    <div class="server-progress">
                        <div class="progress-header">
                            <span>Server Load</span>
                            <span>54%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 54%"></div>
                        </div>
                    </div>

                    <div class="server-footer">
                        <button class="access-server" onclick="event.stopPropagation(); showRoleModal('hm')">
                            Access Server →
                        </button>
                        <span class="server-meta">
                            <span>⚡</span> 99.9% uptime
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- OLSHCO Facilities Section -->
        <div class="facilities-section">
            <div class="section-header">
                <div class="section-title">
                    <i>🏛️</i>
                    <h2>OLSCHO Facilities & Resources</h2>
                </div>
            </div>
            
            <div class="facilities-grid">
                <!-- CIT Facilities -->
                <div class="facility-card">
                    <div class="facility-header">
                        <span class="facility-name">CIT Facilities</span>
                        <span class="facility-badge badge-active">Active</span>
                    </div>
                    <div class="facility-metrics">
                        <div class="metric-item">
                            <div class="metric-number">5</div>
                            <div class="metric-label">Computer Labs</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-number">200+</div>
                            <div class="metric-label">Workstations</div>
                        </div>
                    </div>
                    <ul class="facility-features">
                        <li><i class="fas fa-network-wired"></i> Cisco Networking Lab</li>
                        <li><i class="fas fa-robot"></i> Robotics & AI Lab</li>
                    </ul>
                </div>

                <!-- CCJ Facilities -->
                <div class="facility-card">
                    <div class="facility-header">
                        <span class="facility-name">CCJ Facilities</span>
                        <span class="facility-badge badge-active">Active</span>
                    </div>
                    <div class="facility-metrics">
                        <div class="metric-item">
                            <div class="metric-number">3</div>
                            <div class="metric-label">Mock Courtrooms</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-number">2</div>
                            <div class="metric-label">Forensic Labs</div>
                        </div>
                    </div>
                    <ul class="facility-features">
                        <li><i class="fas fa-gavel"></i> Crime Scene Simulation Room</li>
                        <li><i class="fas fa-dna"></i> DNA Analysis Lab</li>
                    </ul>
                </div>

                <!-- COE Facilities -->
                <div class="facility-card">
                    <div class="facility-header">
                        <span class="facility-name">COE Facilities</span>
                        <span class="facility-badge badge-active">Active</span>
                    </div>
                    <div class="facility-metrics">
                        <div class="metric-item">
                            <div class="metric-number">4</div>
                            <div class="metric-label">Demo Classrooms</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-number">1</div>
                            <div class="metric-label">Learning Resource Center</div>
                        </div>
                    </div>
                    <ul class="facility-features">
                        <li><i class="fas fa-chalkboard"></i> Microteaching Labs</li>
                        <li><i class="fas fa-heart"></i> Special Education Center</li>
                    </ul>
                </div>

                <!-- OA Facilities -->
                <div class="facility-card">
                    <div class="facility-header">
                        <span class="facility-name">OA Facilities</span>
                        <span class="facility-badge badge-active">Active</span>
                    </div>
                    <div class="facility-metrics">
                        <div class="metric-item">
                            <div class="metric-number">3</div>
                            <div class="metric-label">Office Simulation Labs</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-number">50+</div>
                            <div class="metric-label">Workstations</div>
                        </div>
                    </div>
                    <ul class="facility-features">
                        <li><i class="fas fa-file-alt"></i> Document Processing Center</li>
                        <li><i class="fas fa-phone-alt"></i> Virtual Office Setup</li>
                    </ul>
                </div>

                <!-- HM Facilities -->
                <div class="facility-card">
                    <div class="facility-header">
                        <span class="facility-name">HM Facilities</span>
                        <span class="facility-badge badge-active">Active</span>
                    </div>
                    <div class="facility-metrics">
                        <div class="metric-item">
                            <div class="metric-number">2</div>
                            <div class="metric-label">Kitchen Labs</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-number">1</div>
                            <div class="metric-label">Hotel Simulation</div>
                        </div>
                    </div>
                    <ul class="facility-features">
                        <li><i class="fas fa-utensils"></i> Culinary Arts Center</li>
                        <li><i class="fas fa-wine-glass-alt"></i> Fine Dining Restaurant</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Selection Modal -->
    <div id="roleModal" class="role-modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-user-tag"></i>
            </div>
            <h2>Select Your Role</h2>
            <p>Please identify yourself to access the department server</p>
            
            <div class="role-grid">
                <button class="role-card" onclick="selectRole('student')">
                    <i>👨‍🎓</i>
                    <h3>Student</h3>
                    <p>Access your courses and grades</p>
                </button>
                <button class="role-card" onclick="selectRole('instructor')">
                    <i>👨‍🏫</i>
                    <h3>Instructor</h3>
                    <p>Manage classes and materials</p>
                </button>
                <button class="role-card" onclick="selectRole('staff')">
                    <i>👔</i>
                    <h3>Staff</h3>
                    <p>Administrative access</p>
                </button>
                <button class="role-card" onclick="selectRole('admin')">
                    <i>👨‍💼</i>
                    <h3>Admin</h3>
                    <p>Full system control</p>
                </button>
            </div>
            
            <button class="modal-close" onclick="closeRoleModal()">Cancel</button>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loading" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="loading-spinner"></div>
    </div>

    <script>
        let selectedDepartment = '';

        function showRoleModal(department) {
            selectedDepartment = department;
            document.getElementById('roleModal').style.display = 'flex';
        }

        function closeRoleModal() {
            document.getElementById('roleModal').style.display = 'none';
        }

        function selectRole(role) {
            // Redirect to specific department dashboard based on role and department
            window.location.href = `department_dashboard.php?dept=${selectedDepartment}&role=${role}`;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('roleModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Loading effect for navigation
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.href.includes('logout')) {
                    document.getElementById('loading').style.display = 'block';
                }
            });
        });

        // Simulated server status updates
        setInterval(function() {
            console.log('Server health check: All systems operational');
        }, 30000);
    </script>
</body>
</html>