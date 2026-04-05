<?php
session_start();
require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if main admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_type'] != 'main') {
    header("Location: login.php");
    exit();
}

// Get statistics for all courses
$college_courses = ['BSIT', 'HM', 'OAD', 'CRIM', 'EDUC'];
$highschool_strands = ['STEM', 'HUMMS', 'TECHVOC'];

// Get total enrolled per course
$course_stats = [];
foreach ($college_courses as $course) {
    $sql = "SELECT COUNT(*) as total FROM users WHERE course = ? AND course_type = 'college' AND is_verified = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_stats[$course] = $result->fetch_assoc()['total'];
}

$strand_stats = [];
foreach ($highschool_strands as $strand) {
    $sql = "SELECT COUNT(*) as total FROM users WHERE course = ? AND course_type = 'highschool' AND is_verified = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $strand);
    $stmt->execute();
    $result = $stmt->get_result();
    $strand_stats[$strand] = $result->fetch_assoc()['total'];
}

// Get total enrollments
$total_college = array_sum($course_stats);
$total_highschool = array_sum($strand_stats);
$total_all = $total_college + $total_highschool;

// Get recent enrollments
$recent_sql = "SELECT fullname, email, course, course_type, created_at FROM users WHERE is_verified = 1 ORDER BY created_at DESC LIMIT 10";
$recent_result = $conn->query($recent_sql);

// Get daily enrollment trend (last 7 days)
$trend_sql = "SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE is_verified = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY date DESC";
$trend_result = $conn->query($trend_sql);
$trend_data = [];
while ($row = $trend_result->fetch_assoc()) {
    $trend_data[] = $row;
}

// Get monthly comparison
$monthly_sql = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as count,
    SUM(CASE WHEN course_type = 'college' THEN 1 ELSE 0 END) as college_count,
    SUM(CASE WHEN course_type = 'highschool' THEN 1 ELSE 0 END) as highschool_count
    FROM users WHERE is_verified = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC";
$monthly_result = $conn->query($monthly_sql);
$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_data[] = $row;
}

// Get admin activities
$activity_sql = "SELECT * FROM admin_users ORDER BY last_login DESC LIMIT 5";
$activity_result = $conn->query($activity_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Admin Dashboard | Guimba Enrollment System</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2C3E50;
            --primary-dark: #1A252F;
            --secondary: #3498DB;
            --success: #27AE60;
            --warning: #E67E22;
            --danger: #E74C3C;
            --info: #3498DB;
            --light: #ECF0F1;
            --dark: #2C3E50;
            --white: #FFFFFF;
            --gray-100: #F8F9FA;
            --gray-200: #E9ECEF;
            --gray-300: #DEE2E6;
            --gray-400: #CED4DA;
            --gray-500: #ADB5BD;
            --gray-600: #6C757D;
            --gray-700: #495057;
            --gray-800: #343A40;
            --gray-900: #212529;
            
            --sidebar-width: 280px;
            --header-height: 70px;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.05);
            --hover-shadow: 0 15px 40px rgba(52, 152, 219, 0.15);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F0F2F5;
            color: var(--gray-800);
            line-height: 1.6;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-200);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Layout */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: 5px 0 30px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-left: 45px;
        }

        .sidebar-menu {
            padding: 25px 0;
        }

        .menu-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
            margin: 4px 0;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--secondary);
        }

        .menu-item i {
            width: 24px;
            font-size: 1.2rem;
        }

        .menu-item span {
            font-size: 0.95rem;
            font-weight: 500;
        }

        .menu-badge {
            margin-left: auto;
            background: var(--secondary);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 0 30px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .nav-search {
            display: flex;
            align-items: center;
            background: var(--gray-100);
            padding: 8px 20px;
            border-radius: 30px;
            width: 300px;
        }

        .nav-search i {
            color: var(--gray-500);
            margin-right: 10px;
            font-size: 1rem;
        }

        .nav-search input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.95rem;
            width: 100%;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-icon {
            position: relative;
            cursor: pointer;
        }

        .nav-icon i {
            font-size: 1.3rem;
            color: var(--gray-600);
            transition: var(--transition);
        }

        .nav-icon:hover i {
            color: var(--secondary);
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            transition: var(--transition);
        }

        .user-profile:hover {
            background: var(--gray-100);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-info {
            line-height: 1.4;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px rgba(52, 152, 219, 0.2);
        }

        .welcome-text h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-text p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .welcome-stats {
            display: flex;
            gap: 30px;
        }

        .welcome-stat {
            text-align: center;
        }

        .welcome-stat .number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .welcome-stat .label {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            border-color: var(--secondary);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .stat-icon.college {
            background: linear-gradient(135deg, #27AE60, #229954);
        }

        .stat-icon.highschool {
            background: linear-gradient(135deg, #E67E22, #D35400);
        }

        .stat-icon.total {
            background: linear-gradient(135deg, #2C3E50, #3498DB);
        }

        .stat-info h3 {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--gray-800);
            line-height: 1.2;
        }

        .stat-change {
            font-size: 0.85rem;
            color: var(--success);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--gray-200);
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 i {
            color: var(--secondary);
        }

        .card-header .badge {
            position: static;
            background: var(--gray-200);
            color: var(--gray-700);
            padding: 5px 12px;
            font-weight: 500;
        }

        /* Course Grid */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .course-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .course-item::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            opacity: 0;
            transition: var(--transition);
        }

        .course-item:hover::before {
            opacity: 1;
        }

        .course-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .course-item.college {
            background: linear-gradient(135deg, #27AE60, #229954);
        }

        .course-item.highschool {
            background: linear-gradient(135deg, #E67E22, #D35400);
        }

        .course-code {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
        }

        .course-count {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 15px 0;
            position: relative;
        }

        .course-label {
            font-size: 0.85rem;
            opacity: 0.9;
            position: relative;
        }

        .trend-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
            background: rgba(255,255,255,0.2);
            padding: 3px 8px;
            border-radius: 15px;
        }

        /* Recent List */
        .recent-list {
            list-style: none;
        }

        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .recent-item:hover {
            background: var(--gray-100);
            padding-left: 10px;
            padding-right: 10px;
            border-radius: 10px;
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .recent-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 5px;
        }

        .recent-info p {
            font-size: 0.85rem;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recent-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-college {
            background: #27AE60;
            color: white;
        }

        .badge-highschool {
            background: #E67E22;
            color: white;
        }

        /* Chart Container */
        .chart-container {
            height: 300px;
            margin-top: 15px;
            position: relative;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .action-btn {
            background: var(--gray-100);
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--gray-700);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .action-btn:hover {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            transform: translateY(-5px);
            border-color: transparent;
        }

        .action-btn i {
            font-size: 28px;
            margin-bottom: 10px;
            display: block;
        }

        .action-btn span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Activity List */
        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: var(--gray-200);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
        }

        .activity-info p {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .activity-info small {
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-card {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .course-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .top-nav {
                flex-direction: column;
                height: auto;
                padding: 15px;
                gap: 15px;
            }
            
            .nav-search {
                width: 100%;
            }
            
            .nav-right {
                width: 100%;
                justify-content: space-around;
            }
        }

        /* Loading Animation */
        .skeleton {
            background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-300) 50%, var(--gray-200) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background: var(--gray-800);
            color: white;
            border-radius: 5px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: 120%;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>
                    <i class="fas fa-crown"></i>
                    AdminHub
                </h2>
                <p>Enrollment System v2.0</p>
            </div>
            
            <div class="sidebar-menu">
                <a href="#" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>College Courses</span>
                    <span class="menu-badge"><?php echo $total_college; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-school"></i>
                    <span>High School Strands</span>
                    <span class="menu-badge"><?php echo $total_highschool; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>All Students</span>
                    <span class="menu-badge"><?php echo $total_all; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-question-circle"></i>
                    <span>Help</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <div class="top-nav">
                <div class="nav-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search students, courses...">
                </div>
                
                <div class="nav-right">
                    <div class="nav-icon" data-tooltip="Messages">
                        <i class="far fa-envelope"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="nav-icon" data-tooltip="Notifications">
                        <i class="far fa-bell"></i>
                        <span class="badge">5</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                            <div class="user-role">Super Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-text">
                    <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['admin_name'])[0]); ?>! 👋</h2>
                    <p>Here's what's happening with your enrollment system today.</p>
                </div>
                <div class="welcome-stats">
                    <div class="welcome-stat">
                        <div class="number"><?php echo $total_all; ?></div>
                        <div class="label">Total Students</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="number"><?php echo count($trend_data) ? array_sum(array_column($trend_data, 'count')) : 0; ?></div>
                        <div class="label">This Week</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="number">8</div>
                        <div class="label">Active Courses</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Enrolled</h3>
                        <div class="stat-number"><?php echo $total_all; ?></div>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% from last month</span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon college">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-info">
                        <h3>College Students</h3>
                        <div class="stat-number"><?php echo $total_college; ?></div>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span>+8% from last month</span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon highschool">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-info">
                        <h3>High School</h3>
                        <div class="stat-number"><?php echo $total_highschool; ?></div>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span>+15% from last month</span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>This Week</h3>
                        <div class="stat-number"><?php echo count($trend_data) ? array_sum(array_column($trend_data, 'count')) : 0; ?></div>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span>+5 new today</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- College Courses -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-graduation-cap"></i>
                            College Enrollment
                        </h2>
                        <span class="badge">Total: <?php echo $total_college; ?></span>
                    </div>
                    <div class="course-grid">
                        <?php foreach ($course_stats as $course => $count): ?>
                        <div class="course-item college">
                            <div class="course-code"><?php echo $course; ?></div>
                            <div class="course-count"><?php echo $count; ?></div>
                            <div class="course-label">
                                <?php 
                                switch($course) {
                                    case 'BSIT': echo 'Information Technology'; break;
                                    case 'HM': echo 'Hospitality Management'; break;
                                    case 'OAD': echo 'Office Administration'; break;
                                    case 'CRIM': echo 'Criminology'; break;
                                    case 'EDUC': echo 'Education'; break;
                                }
                                ?>
                            </div>
                            <div class="trend-indicator">
                                <i class="fas fa-arrow-up"></i> +2
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- High School Strands -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-school"></i>
                            High School Enrollment
                        </h2>
                        <span class="badge">Total: <?php echo $total_highschool; ?></span>
                    </div>
                    <div class="course-grid">
                        <?php foreach ($strand_stats as $strand => $count): ?>
                        <div class="course-item highschool">
                            <div class="course-code"><?php echo $strand; ?></div>
                            <div class="course-count"><?php echo $count; ?></div>
                            <div class="course-label">
                                <?php 
                                switch($strand) {
                                    case 'STEM': echo 'Science & Technology'; break;
                                    case 'HUMMS': echo 'Humanities & Social Sciences'; break;
                                    case 'TECHVOC': echo 'Technical-Vocational'; break;
                                }
                                ?>
                            </div>
                            <div class="trend-indicator">
                                <i class="fas fa-arrow-up"></i> +3
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="dashboard-grid">
                <!-- Enrollment Trend Chart -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-chart-line"></i>
                            Enrollment Trend (Last 7 Days)
                        </h2>
                        <span class="badge">Weekly View</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-history"></i>
                            Recent Enrollments
                        </h2>
                        <span class="badge">Last 10</span>
                    </div>
                    <ul class="recent-list">
                        <?php while ($row = $recent_result->fetch_assoc()): ?>
                        <li class="recent-item">
                            <div class="recent-info">
                                <h4><?php echo htmlspecialchars($row['fullname']); ?></h4>
                                <p>
                                    <i class="far fa-envelope"></i>
                                    <?php echo htmlspecialchars($row['email']); ?>
                                    <i class="far fa-clock"></i>
                                    <?php echo date('M d, h:i A', strtotime($row['created_at'])); ?>
                                </p>
                            </div>
                            <span class="recent-badge <?php echo $row['course_type'] == 'college' ? 'badge-college' : 'badge-highschool'; ?>">
                                <?php echo $row['course']; ?>
                            </span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Third Row -->
            <div class="dashboard-grid">
                <!-- Monthly Comparison -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-chart-bar"></i>
                            Monthly Comparison
                        </h2>
                        <span class="badge">Last 6 Months</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Admin Activities -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-user-shield"></i>
                            Admin Activities
                        </h2>
                        <span class="badge">Recent Logins</span>
                    </div>
                    <ul class="activity-list">
                        <?php while ($activity = $activity_result->fetch_assoc()): ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="activity-info">
                                <p><?php echo htmlspecialchars($activity['fullname']); ?></p>
                                <small>
                                    <i class="far fa-clock"></i>
                                    Last login: <?php echo $activity['last_login'] ? date('M d, h:i A', strtotime($activity['last_login'])) : 'Never'; ?>
                                </small>
                            </div>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="quick-actions">
                    <a href="college_dashboard.php" class="action-btn">
                        <i class="fas fa-graduation-cap"></i>
                        <span>College Admin Panel</span>
                    </a>
                    <a href="highschool_dashboard.php" class="action-btn">
                        <i class="fas fa-school"></i>
                        <span>High School Admin Panel</span>
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-download"></i>
                        <span>Export Reports</span>
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Add New Admin</span>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Enrollment Trend Chart
        const ctx = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php 
                    foreach(array_reverse($trend_data) as $data) {
                        echo "'" . date('M d', strtotime($data['date'])) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Enrollments',
                    data: [<?php 
                        foreach(array_reverse($trend_data) as $data) {
                            echo $data['count'] . ",";
                        }
                    ?>],
                    borderColor: '#3498DB',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#3498DB',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2C3E50',
                        titleColor: 'white',
                        bodyColor: 'rgba(255,255,255,0.8)',
                        borderColor: '#3498DB',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Monthly Comparison Chart
        <?php if (!empty($monthly_data)): ?>
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    foreach(array_reverse($monthly_data) as $data) {
                        echo "'" . date('M Y', strtotime($data['month'] . '-01')) . "',";
                    }
                ?>],
                datasets: [
                    {
                        label: 'College',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo $data['college_count'] . ",";
                            }
                        ?>],
                        backgroundColor: '#27AE60',
                        borderRadius: 6
                    },
                    {
                        label: 'High School',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo $data['highschool_count'] . ",";
                            }
                        ?>],
                        backgroundColor: '#E67E22',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>