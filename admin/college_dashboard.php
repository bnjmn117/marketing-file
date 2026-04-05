<?php
session_start();
require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if college admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_type'] != 'college') {
    header("Location: login.php");
    exit();
}

// Get college course statistics
$college_courses = ['BSIT', 'HM', 'OAD', 'CRIM', 'EDUC'];
$course_stats = [];
$course_details = [];
$course_colors = [
    'BSIT' => '#3498DB',
    'HM' => '#E67E22',
    'OAD' => '#9B59B6',
    'CRIM' => '#E74C3C',
    'EDUC' => '#27AE60'
];

foreach ($college_courses as $course) {
    // Get count
    $sql = "SELECT COUNT(*) as total FROM users WHERE course = ? AND course_type = 'college' AND is_verified = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_stats[$course] = $result->fetch_assoc()['total'];
    
    // Get recent enrollees for this course
    $sql2 = "SELECT fullname, email, created_at FROM users WHERE course = ? AND course_type = 'college' AND is_verified = 1 ORDER BY created_at DESC LIMIT 5";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $course);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $course_details[$course] = [];
    while ($row = $result2->fetch_assoc()) {
        $course_details[$course][] = $row;
    }
}

$total_college = array_sum($course_stats);

// Get college with highest enrollment
$max_course = array_search(max($course_stats), $course_stats);

// Get monthly trend for college courses
$monthly_sql = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total,
    SUM(CASE WHEN course = 'BSIT' THEN 1 ELSE 0 END) as bsit,
    SUM(CASE WHEN course = 'HM' THEN 1 ELSE 0 END) as hm,
    SUM(CASE WHEN course = 'OAD' THEN 1 ELSE 0 END) as oad,
    SUM(CASE WHEN course = 'CRIM' THEN 1 ELSE 0 END) as crim,
    SUM(CASE WHEN course = 'EDUC' THEN 1 ELSE 0 END) as educ
    FROM users 
    WHERE course_type = 'college' AND is_verified = 1 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC";
$monthly_result = $conn->query($monthly_sql);
$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Admin Dashboard | Guimba Enrollment System</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
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
            --primary: #27AE60;
            --primary-dark: #229954;
            --primary-light: #2ECC71;
            --secondary: #3498DB;
            --accent: #9B59B6;
            --danger: #E74C3C;
            --warning: #E67E22;
            --info: #3498DB;
            --dark: #2C3E50;
            --light: #ECF0F1;
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
            --hover-shadow: 0 15px 40px rgba(39, 174, 96, 0.15);
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
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Layout */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark) 0%, #1A252F 100%);
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

        .sidebar-header h2 i {
            color: var(--primary);
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
            border-left-color: var(--primary);
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
            background: var(--primary);
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
            color: var(--primary);
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px rgba(39, 174, 96, 0.2);
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
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }

        .stat-icon.bsit { background: linear-gradient(135deg, #3498DB, #2980B9); }
        .stat-icon.hm { background: linear-gradient(135deg, #E67E22, #D35400); }
        .stat-icon.oad { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
        .stat-icon.crim { background: linear-gradient(135deg, #E74C3C, #C0392B); }
        .stat-icon.educ { background: linear-gradient(135deg, #27AE60, #229954); }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 10px 0 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-code {
            font-size: 0.8rem;
            color: var(--gray-500);
            margin-top: 5px;
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
            margin-bottom: 20px;
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
            color: var(--primary);
        }

        .card-header .badge {
            position: static;
            background: var(--gray-200);
            color: var(--gray-700);
            padding: 5px 12px;
            font-weight: 500;
        }

        /* Course Sections */
        .course-section {
            margin-bottom: 25px;
            background: var(--gray-100);
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid;
            transition: var(--transition);
        }

        .course-section:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .course-section.bsit { border-left-color: #3498DB; }
        .course-section.hm { border-left-color: #E67E22; }
        .course-section.oad { border-left-color: #9B59B6; }
        .course-section.crim { border-left-color: #E74C3C; }
        .course-section.educ { border-left-color: #27AE60; }

        .course-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .course-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .course-stat {
            background: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .student-list {
            list-style: none;
        }

        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .student-item:hover {
            background: white;
            padding-left: 10px;
            padding-right: 10px;
            border-radius: 8px;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-info {
            display: flex;
            flex-direction: column;
        }

        .student-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--gray-800);
        }

        .student-email {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .student-date {
            font-size: 0.8rem;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Quick Stats */
        .quick-stats {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }

        .quick-stats h3 {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .quick-stats .total-number {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
        }

        .top-course-card {
            background: var(--gray-100);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--gray-200);
        }

        .top-course-title {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 5px;
        }

        .top-course-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .top-course-count {
            font-size: 1rem;
            color: var(--gray-600);
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
            border-radius: 12px;
            text-decoration: none;
            color: var(--gray-700);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .action-btn:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Chart Container */
        .chart-container {
            height: 350px;
            margin-top: 20px;
            position: relative;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
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

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .stat-card {
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        <?php 
        $delay = 0;
        foreach ($college_courses as $course): 
            $delay += 0.1;
            echo ".stat-card.{$course} { animation-delay: {$delay}s; }";
        endforeach; 
        ?>

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
                    <i class="fas fa-graduation-cap"></i>
                    CollegeHub
                </h2>
                <p>College Administration</p>
            </div>
            
            <div class="sidebar-menu">
                <a href="#" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-laptop-code"></i>
                    <span>BSIT</span>
                    <span class="menu-badge"><?php echo $course_stats['BSIT']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-utensils"></i>
                    <span>HM</span>
                    <span class="menu-badge"><?php echo $course_stats['HM']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-folder-open"></i>
                    <span>OAD</span>
                    <span class="menu-badge"><?php echo $course_stats['OAD']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-gavel"></i>
                    <span>CRIM</span>
                    <span class="menu-badge"><?php echo $course_stats['CRIM']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>EDUC</span>
                    <span class="menu-badge"><?php echo $course_stats['EDUC']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
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
                            <div class="user-role">College Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-text">
                    <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['admin_name'])[0]); ?>! 👋</h2>
                    <p>Manage your college courses and monitor student enrollments.</p>
                </div>
                <div class="welcome-stats">
                    <div class="welcome-stat">
                        <div class="number"><?php echo $total_college; ?></div>
                        <div class="label">Total Students</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="number"><?php echo count($college_courses); ?></div>
                        <div class="label">Active Courses</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <?php foreach ($course_stats as $course => $count): ?>
                <div class="stat-card <?php echo strtolower($course); ?>" data-tooltip="Click for details">
                    <div class="stat-icon <?php echo strtolower($course); ?>">
                        <?php 
                        switch($course) {
                            case 'BSIT': echo '<i class="fas fa-laptop-code"></i>'; break;
                            case 'HM': echo '<i class="fas fa-utensils"></i>'; break;
                            case 'OAD': echo '<i class="fas fa-folder-open"></i>'; break;
                            case 'CRIM': echo '<i class="fas fa-gavel"></i>'; break;
                            case 'EDUC': echo '<i class="fas fa-chalkboard-teacher"></i>'; break;
                        }
                        ?>
                    </div>
                    <div class="stat-number"><?php echo $count; ?></div>
                    <div class="stat-label">
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
                    <div class="stat-code"><?php echo $course; ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Enrollments by Course -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-history"></i>
                            Recent College Enrollments
                        </h2>
                        <span class="badge">Last 5 per course</span>
                    </div>
                    
                    <?php foreach ($course_details as $course => $students): ?>
                    <?php if (!empty($students)): ?>
                    <div class="course-section <?php echo strtolower($course); ?>">
                        <div class="course-title">
                            <i class="fas fa-graduation-cap"></i>
                            <span><?php echo $course; ?></span>
                        </div>
                        <div class="course-stats">
                            <span class="course-stat">Total: <?php echo $course_stats[$course]; ?></span>
                            <span class="course-stat">Recent: <?php echo count($students); ?></span>
                        </div>
                        <ul class="student-list">
                            <?php foreach ($students as $student): ?>
                            <li class="student-item">
                                <div class="student-info">
                                    <span class="student-name"><?php echo htmlspecialchars($student['fullname']); ?></span>
                                    <span class="student-email"><?php echo htmlspecialchars($student['email']); ?></span>
                                </div>
                                <span class="student-date">
                                    <i class="far fa-clock"></i>
                                    <?php echo date('M d', strtotime($student['created_at'])); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Quick Stats & Actions -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-chart-pie"></i>
                            Quick Overview
                        </h2>
                    </div>
                    
                    <div class="quick-stats">
                        <h3>Total College Enrollment</h3>
                        <div class="total-number"><?php echo $total_college; ?></div>
                        <p>across <?php echo count($college_courses); ?> courses</p>
                    </div>
                    
                    <div class="top-course-card">
                        <div class="top-course-title">🏆 Top Performing Course</div>
                        <div class="top-course-name"><?php echo $max_course; ?></div>
                        <div class="top-course-count"><?php echo $course_stats[$max_course]; ?> students enrolled</div>
                    </div>
                    
                    <h3 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bolt" style="color: var(--primary);"></i>
                        Quick Actions
                    </h3>
                    <div class="quick-actions">
                        <a href="reports.php?type=college" class="action-btn">
                            <i class="fas fa-file-pdf"></i>
                            <span>Generate Report</span>
                        </a>
                        <a href="export.php?type=college" class="action-btn">
                            <i class="fas fa-download"></i>
                            <span>Export Data</span>
                        </a>
                        <a href="manage_college.php" class="action-btn">
                            <i class="fas fa-cog"></i>
                            <span>Manage Courses</span>
                        </a>
                        <a href="main_dashboard.php" class="action-btn">
                            <i class="fas fa-crown"></i>
                            <span>Main Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-chart-bar"></i>
                        Course Enrollment Distribution
                    </h2>
                    <span class="badge">Current Semester</span>
                </div>
                <div class="chart-container">
                    <canvas id="collegeChart"></canvas>
                </div>
            </div>

            <!-- Monthly Trend Chart (if data exists) -->
            <?php if (!empty($monthly_data)): ?>
            <div class="card" style="margin-top: 25px;">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-chart-line"></i>
                        Monthly Enrollment Trend
                    </h2>
                    <span class="badge">Last 6 Months</span>
                </div>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Main Chart
        const ctx = document.getElementById('collegeChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['BSIT', 'HM', 'OAD', 'CRIM', 'EDUC'],
                datasets: [{
                    data: [<?php echo implode(',', array_values($course_stats)); ?>],
                    backgroundColor: [
                        '#3498DB',
                        '#E67E22',
                        '#9B59B6',
                        '#E74C3C',
                        '#27AE60'
                    ],
                    borderWidth: 0,
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#2C3E50',
                        titleColor: 'white',
                        bodyColor: 'rgba(255,255,255,0.8)',
                        borderColor: '#27AE60',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} students (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        <?php if (!empty($monthly_data)): ?>
        // Monthly Trend Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    foreach(array_reverse($monthly_data) as $data) {
                        echo "'" . date('M Y', strtotime($data['month'] . '-01')) . "',";
                    }
                ?>],
                datasets: [
                    {
                        label: 'BSIT',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo ($data['bsit'] ?? 0) . ",";
                            }
                        ?>],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'HM',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo ($data['hm'] ?? 0) . ",";
                            }
                        ?>],
                        borderColor: '#E67E22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'CRIM',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo ($data['crim'] ?? 0) . ",";
                            }
                        ?>],
                        borderColor: '#E74C3C',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.4,
                        fill: false
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
                    }
                }
            }
        });
        <?php endif; ?>

        // Animation for stat cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            observer.observe(card);
        });
    </script>
</body>
</html>