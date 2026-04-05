<?php
session_start();
require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if high school admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_type'] != 'highschool') {
    header("Location: login.php");
    exit();
}

// Get high school strand statistics
$highschool_strands = ['STEM', 'HUMMS', 'TECHVOC'];
$strand_stats = [];
$strand_details = [];
$strand_colors = [
    'STEM' => '#3498DB',
    'HUMMS' => '#9B59B6',
    'TECHVOC' => '#E67E22'
];
$strand_icons = [
    'STEM' => '🔬',
    'HUMMS' => '📖',
    'TECHVOC' => '🛠️'
];

foreach ($highschool_strands as $strand) {
    // Get count
    $sql = "SELECT COUNT(*) as total FROM users WHERE course = ? AND course_type = 'highschool' AND is_verified = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $strand);
    $stmt->execute();
    $result = $stmt->get_result();
    $strand_stats[$strand] = $result->fetch_assoc()['total'];
    
    // Get recent enrollees for this strand
    $sql2 = "SELECT fullname, email, created_at FROM users WHERE course = ? AND course_type = 'highschool' AND is_verified = 1 ORDER BY created_at DESC LIMIT 5";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $strand);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $strand_details[$strand] = [];
    while ($row = $result2->fetch_assoc()) {
        $strand_details[$strand][] = $row;
    }
}

$total_highschool = array_sum($strand_stats);
$max_strand = array_search(max($strand_stats), $strand_stats);

// Get monthly trend for high school strands
$monthly_sql = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total,
    SUM(CASE WHEN course = 'STEM' THEN 1 ELSE 0 END) as stem,
    SUM(CASE WHEN course = 'HUMMS' THEN 1 ELSE 0 END) as humms,
    SUM(CASE WHEN course = 'TECHVOC' THEN 1 ELSE 0 END) as techvoc
    FROM users 
    WHERE course_type = 'highschool' AND is_verified = 1 
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
    <title>High School Admin Dashboard | Guimba Enrollment System</title>
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
            --primary: #E67E22;
            --primary-dark: #D35400;
            --primary-light: #F39C12;
            --secondary: #3498DB;
            --accent: #9B59B6;
            --stem: #3498DB;
            --humms: #9B59B6;
            --techvoc: #E67E22;
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
            --hover-shadow: 0 15px 40px rgba(230, 126, 34, 0.15);
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
            background: var(--primary-dark);
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
            box-shadow: 0 20px 40px rgba(230, 126, 34, 0.2);
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
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
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
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 32px;
            color: white;
        }

        .stat-icon.stem { background: linear-gradient(135deg, #3498DB, #2980B9); }
        .stat-icon.humms { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
        .stat-icon.techvoc { background: linear-gradient(135deg, #E67E22, #D35400); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 10px 0 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-code {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-top: 8px;
            font-weight: 600;
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

        /* Strand Sections */
        .strand-section {
            margin-bottom: 25px;
            background: var(--gray-100);
            border-radius: 15px;
            padding: 15px;
            border-left: 4px solid;
            transition: var(--transition);
        }

        .strand-section:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .strand-section.stem { border-left-color: #3498DB; }
        .strand-section.humms { border-left-color: #9B59B6; }
        .strand-section.techvoc { border-left-color: #E67E22; }

        .strand-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .strand-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .strand-stat {
            background: white;
            padding: 5px 12px;
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
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .student-item:hover {
            background: white;
            padding-left: 10px;
            padding-right: 10px;
            border-radius: 10px;
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
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .strand-tag {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            background: var(--gray-200);
            color: var(--gray-600);
            margin-left: 8px;
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
            padding: 25px;
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
            font-size: 4rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
        }

        .top-strand-card {
            background: var(--gray-100);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--gray-200);
            text-align: center;
        }

        .top-strand-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .top-strand-title {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 5px;
        }

        .top-strand-name {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .top-strand-count {
            font-size: 1.1rem;
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
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        <?php 
        $delay = 0;
        foreach ($highschool_strands as $strand): 
            $delay += 0.1;
            echo ".stat-card.{$strand} { animation-delay: {$delay}s; }";
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
                    <i class="fas fa-school"></i>
                    SHS Hub
                </h2>
                <p>Senior High School Administration</p>
            </div>
            
            <div class="sidebar-menu">
                <a href="#" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-flask"></i>
                    <span>STEM</span>
                    <span class="menu-badge"><?php echo $strand_stats['STEM']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-globe"></i>
                    <span>HUMMS</span>
                    <span class="menu-badge"><?php echo $strand_stats['HUMMS']; ?></span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-tools"></i>
                    <span>TECHVOC</span>
                    <span class="menu-badge"><?php echo $strand_stats['TECHVOC']; ?></span>
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
                    <input type="text" placeholder="Search students, strands...">
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
                            <div class="user-role">SHS Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-text">
                    <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['admin_name'])[0]); ?>! 👋</h2>
                    <p>Manage your senior high school strands and monitor student enrollments.</p>
                </div>
                <div class="welcome-stats">
                    <div class="welcome-stat">
                        <div class="number"><?php echo $total_highschool; ?></div>
                        <div class="label">Total Students</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="number"><?php echo count($highschool_strands); ?></div>
                        <div class="label">Active Strands</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <?php foreach ($strand_stats as $strand => $count): ?>
                <div class="stat-card <?php echo strtolower($strand); ?>" data-tooltip="Click for details">
                    <div class="stat-icon <?php echo strtolower($strand); ?>">
                        <?php 
                        switch($strand) {
                            case 'STEM': echo '🔬'; break;
                            case 'HUMMS': echo '📖'; break;
                            case 'TECHVOC': echo '🛠️'; break;
                        }
                        ?>
                    </div>
                    <div class="stat-number"><?php echo $count; ?></div>
                    <div class="stat-label">
                        <?php 
                        switch($strand) {
                            case 'STEM': echo 'Science & Technology'; break;
                            case 'HUMMS': echo 'Humanities & Social Sciences'; break;
                            case 'TECHVOC': echo 'Technical-Vocational'; break;
                        }
                        ?>
                    </div>
                    <div class="stat-code"><?php echo $strand; ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Enrollments by Strand -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-history"></i>
                            Recent SHS Enrollments
                        </h2>
                        <span class="badge">Last 5 per strand</span>
                    </div>
                    
                    <?php foreach ($strand_details as $strand => $students): ?>
                    <?php if (!empty($students)): ?>
                    <div class="strand-section <?php echo strtolower($strand); ?>">
                        <div class="strand-title">
                            <span><?php echo $strand_icons[$strand]; ?></span>
                            <span><?php echo $strand; ?></span>
                        </div>
                        <div class="strand-stats">
                            <span class="strand-stat">Total: <?php echo $strand_stats[$strand]; ?></span>
                            <span class="strand-stat">Recent: <?php echo count($students); ?></span>
                        </div>
                        <ul class="student-list">
                            <?php foreach ($students as $student): ?>
                            <li class="student-item">
                                <div class="student-info">
                                    <span class="student-name">
                                        <?php echo htmlspecialchars($student['fullname']); ?>
                                        <span class="strand-tag"><?php echo $strand; ?></span>
                                    </span>
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
                        <h3>Total SHS Enrollment</h3>
                        <div class="total-number"><?php echo $total_highschool; ?></div>
                        <p>across <?php echo count($highschool_strands); ?> strands</p>
                    </div>
                    
                    <div class="top-strand-card">
                        <div class="top-strand-icon"><?php echo $strand_icons[$max_strand]; ?></div>
                        <div class="top-strand-title">🏆 Most Popular Strand</div>
                        <div class="top-strand-name"><?php echo $max_strand; ?></div>
                        <div class="top-strand-count"><?php echo $strand_stats[$max_strand]; ?> students enrolled</div>
                    </div>
                    
                    <h3 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bolt" style="color: var(--primary);"></i>
                        Quick Actions
                    </h3>
                    <div class="quick-actions">
                        <a href="reports.php?type=highschool" class="action-btn">
                            <i class="fas fa-file-pdf"></i>
                            <span>Generate Report</span>
                        </a>
                        <a href="export.php?type=highschool" class="action-btn">
                            <i class="fas fa-download"></i>
                            <span>Export Data</span>
                        </a>
                        <a href="manage_strands.php" class="action-btn">
                            <i class="fas fa-cog"></i>
                            <span>Manage Strands</span>
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
                        <i class="fas fa-chart-pie"></i>
                        Strand Enrollment Distribution
                    </h2>
                    <span class="badge">Current Semester</span>
                </div>
                <div class="chart-container">
                    <canvas id="highschoolChart"></canvas>
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
        const ctx = document.getElementById('highschoolChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['STEM', 'HUMMS', 'TECHVOC'],
                datasets: [{
                    data: [<?php echo implode(',', array_values($strand_stats)); ?>],
                    backgroundColor: ['#3498DB', '#9B59B6', '#E67E22'],
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
                        borderColor: '#E67E22',
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
                        label: 'STEM',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo ($data['stem'] ?? 0) . ",";
                            }
                        ?>],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'HUMMS',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo ($data['humms'] ?? 0) . ",";
                            }
                        ?>],
                        borderColor: '#9B59B6',
                        backgroundColor: 'rgba(155, 89, 182, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'TECHVOC',
                        data: [<?php 
                            foreach(array_reverse($monthly_data) as $data) {
                                echo ($data['techvoc'] ?? 0) . ",";
                            }
                        ?>],
                        borderColor: '#E67E22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
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