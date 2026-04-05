<?php
session_start();
require_once '../config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if program head is logged in
if (!isset($_SESSION['program_head_id']) || !isset($_SESSION['program_code'])) {
    header("Location: login.php");
    exit();
}

$program_code = $_SESSION['program_code'];
$program_type = $_SESSION['program_type'];
$head_name = $_SESSION['head_name'] ?? 'Program Head';

// Get program-specific statistics
$sql = "SELECT COUNT(*) as total FROM users WHERE course = ? AND course_type = ? AND is_verified = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $program_code, $program_type);
$stmt->execute();
$result = $stmt->get_result();
$total_students = $result->fetch_assoc()['total'];

// Get recent enrollments for this program
$recent_sql = "SELECT fullname, email, previous_school_id, previous_school_other, created_at 
               FROM users 
               WHERE course = ? AND course_type = ? AND is_verified = 1 
               ORDER BY created_at DESC LIMIT 10";
$recent_stmt = $conn->prepare($recent_sql);
$recent_stmt->bind_param("ss", $program_code, $program_type);
$recent_stmt->execute();
$recent_result = $recent_stmt->get_result();

// Get monthly trend for this program
$monthly_sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                FROM users 
                WHERE course = ? AND course_type = ? AND is_verified = 1 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";
$monthly_stmt = $conn->prepare($monthly_sql);
$monthly_stmt->bind_param("ss", $program_code, $program_type);
$monthly_stmt->execute();
$monthly_result = $monthly_stmt->get_result();
$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_data[] = $row;
}

// Get school distribution
$school_sql = "SELECT 
    COALESCE(s.school_name, 'Other Schools') as school_name,
    COUNT(*) as count 
    FROM users u 
    LEFT JOIN schools s ON u.previous_school_id = s.id 
    WHERE u.course = ? AND u.course_type = ? AND u.is_verified = 1 
    GROUP BY COALESCE(s.school_name, 'Other Schools')
    ORDER BY count DESC 
    LIMIT 5";
$school_stmt = $conn->prepare($school_sql);
$school_stmt->bind_param("ss", $program_code, $program_type);
$school_stmt->execute();
$school_result = $school_stmt->get_result();

// Get gender distribution (if you have gender field in users table)
// For now, we'll use placeholder data
$male_count = 0;
$female_count = 0;

// Program-specific details
$program_details = [
    'BSIT' => [
        'name' => 'Information Technology',
        'icon' => '💻',
        'color' => '#3498DB',
        'description' => 'Bachelor of Science in Information Technology',
        'badge' => 'College of ICT'
    ],
    'HM' => [
        'name' => 'Hospitality Management',
        'icon' => '🍽️',
        'color' => '#E67E22',
        'description' => 'Bachelor of Science in Hospitality Management',
        'badge' => 'College of Tourism'
    ],
    'OAD' => [
        'name' => 'Office Administration',
        'icon' => '📋',
        'color' => '#9B59B6',
        'description' => 'Bachelor of Science in Office Administration',
        'badge' => 'College of Business'
    ],
    'CRIM' => [
        'name' => 'Criminology',
        'icon' => '🔍',
        'color' => '#E74C3C',
        'description' => 'Bachelor of Science in Criminology',
        'badge' => 'College of Criminal Justice'
    ],
    'EDUC' => [
        'name' => 'Education',
        'icon' => '📚',
        'color' => '#27AE60',
        'description' => 'Bachelor of Secondary Education',
        'badge' => 'College of Education'
    ],
    'STEM' => [
        'name' => 'Science, Technology, Engineering, Mathematics',
        'icon' => '🔬',
        'color' => '#3498DB',
        'description' => 'STEM Strand',
        'badge' => 'Senior High School'
    ],
    'HUMMS' => [
        'name' => 'Humanities and Social Sciences',
        'icon' => '📖',
        'color' => '#9B59B6',
        'description' => 'HUMMS Strand',
        'badge' => 'Senior High School'
    ],
    'TECHVOC' => [
        'name' => 'Technical-Vocational Livelihood',
        'icon' => '🛠️',
        'color' => '#E67E22',
        'description' => 'TECHVOC Strand',
        'badge' => 'Senior High School'
    ]
];

$current_program = $program_details[$program_code] ?? [
    'name' => $program_code,
    'icon' => '📚',
    'color' => '#667eea',
    'description' => 'Program Dashboard',
    'badge' => $program_type
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $program_code; ?> Program Head Dashboard | Guimba Enrollment System</title>
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
            --program-color: <?php echo $current_program['color']; ?>;
            --program-dark: <?php echo $current_program['color']; ?>dd;
            --program-light: <?php echo $current_program['color']; ?>22;
            --program-gradient: linear-gradient(135deg, <?php echo $current_program['color']; ?>, <?php echo $current_program['color']; ?>dd);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F0F2F5;
            color: #333;
            line-height: 1.6;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--program-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--program-dark);
        }

        /* Navbar */
        .navbar {
            background: var(--program-gradient);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-brand h1 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand span {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.9rem;
            backdrop-filter: blur(5px);
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Program Header */
        .program-header {
            background: var(--program-gradient);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }

        .program-header::before {
            content: '<?php echo $current_program['icon']; ?>';
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 120px;
            opacity: 0.1;
            transform: rotate(15deg);
        }

        .program-icon {
            font-size: 4rem;
            background: rgba(255,255,255,0.2);
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .program-info {
            flex: 1;
            margin-left: 30px;
        }

        .program-info h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .program-info p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .program-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .program-stats {
            display: flex;
            gap: 40px;
            background: rgba(255,255,255,0.1);
            padding: 20px 30px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            border-left: 4px solid var(--program-color);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            background: var(--program-light);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--program-color);
        }

        .stat-info h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info .number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            line-height: 1.2;
        }

        .stat-info .trend {
            font-size: 0.8rem;
            color: #27ae60;
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
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 i {
            color: var(--program-color);
            font-size: 1.4rem;
        }

        .card-header .badge {
            background: var(--program-light);
            color: var(--program-color);
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Student List */
        .student-list {
            list-style: none;
        }

        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        .student-item:hover {
            background: var(--program-light);
            padding-left: 15px;
            padding-right: 15px;
            border-radius: 12px;
            margin: 0 -5px;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-info h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .student-info p {
            font-size: 0.85rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .student-info p i {
            font-size: 0.8rem;
            color: #999;
        }

        .student-date {
            font-size: 0.85rem;
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* School Distribution */
        .school-list {
            list-style: none;
        }

        .school-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .school-item:last-child {
            border-bottom: none;
        }

        .school-name {
            font-weight: 500;
            color: #333;
        }

        .school-count {
            background: var(--program-light);
            color: var(--program-color);
            padding: 4px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Chart Container */
        .chart-container {
            height: 350px;
            margin-top: 20px;
            position: relative;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .action-btn {
            background: #f8f9fa;
            padding: 25px 20px;
            border-radius: 16px;
            text-decoration: none;
            color: #333;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .action-btn:hover {
            background: var(--program-gradient);
            color: white;
            transform: translateY(-5px);
            border-color: transparent;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .action-btn i {
            font-size: 32px;
        }

        .action-btn span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid #f0f0f0;
            border-top: 3px solid var(--program-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .program-header {
                flex-direction: column;
                text-align: center;
                gap: 30px;
            }
            
            .program-info {
                margin-left: 0;
                text-align: center;
            }
            
            .program-stats {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .program-stats {
                flex-direction: column;
                gap: 15px;
            }
            
            .program-icon {
                width: 80px;
                height: 80px;
                font-size: 2.5rem;
            }
            
            .program-info h2 {
                font-size: 1.8rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card, .card {
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        <?php 
        $delay = 0.1;
        foreach (range(1, 6) as $i): 
            echo ".stat-card:nth-child({$i}) { animation-delay: {$delay}s; }";
            $delay += 0.1;
        endforeach; 
        ?>

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
            background: #333;
            color: white;
            border-radius: 5px;
            font-size: 0.8rem;
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
    <nav class="navbar">
        <div class="nav-brand">
            <h1>
                <span><?php echo $current_program['icon']; ?></span>
                <?php echo $program_code; ?> Program Head
            </h1>
            <span><?php echo $current_program['badge']; ?></span>
        </div>
        <div class="nav-user">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($head_name, 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($head_name); ?></span>
            </div>
            <a href="logout.php" class="logout-btn" data-tooltip="Sign out">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Program Header -->
        <div class="program-header">
            <div class="program-icon">
                <?php echo $current_program['icon']; ?>
            </div>
            <div class="program-info">
                <h2><?php echo $current_program['name']; ?></h2>
                <p><?php echo $current_program['description']; ?></p>
                <span class="program-badge">
                    <i class="fas fa-calendar-alt"></i>
                    Academic Year 2024-2025
                </span>
            </div>
            <div class="program-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-label">Total Enrolled</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?php echo count($monthly_data) ? array_sum(array_column($monthly_data, 'count')) : 0; ?>
                    </div>
                    <div class="stat-label">This Semester</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $recent_result->num_rows; ?></div>
                    <div class="stat-label">Recent</div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Students</h3>
                    <div class="number"><?php echo $total_students; ?></div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12% from last sem</span>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-male"></i>
                </div>
                <div class="stat-info">
                    <h3>Male Students</h3>
                    <div class="number"><?php echo $male_count ?: '--'; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-female"></i>
                </div>
                <div class="stat-info">
                    <h3>Female Students</h3>
                    <div class="number"><?php echo $female_count ?: '--'; ?></div>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Recent Enrollments -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-history"></i>
                        Recent Enrollments
                    </h2>
                    <span class="badge">Last 10</span>
                </div>
                <?php if ($recent_result->num_rows > 0): ?>
                <ul class="student-list">
                    <?php while ($row = $recent_result->fetch_assoc()): ?>
                    <li class="student-item">
                        <div class="student-info">
                            <h4><?php echo htmlspecialchars($row['fullname']); ?></h4>
                            <p>
                                <i class="fas fa-envelope"></i>
                                <?php echo htmlspecialchars($row['email']); ?>
                            </p>
                        </div>
                        <div class="student-date">
                            <i class="far fa-calendar"></i>
                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-graduate"></i>
                    <p>No enrollments yet</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- School Distribution -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-school"></i>
                        Top Feeder Schools
                    </h2>
                    <span class="badge">Top 5</span>
                </div>
                <?php if ($school_result->num_rows > 0): ?>
                <ul class="school-list">
                    <?php while ($row = $school_result->fetch_assoc()): ?>
                    <li class="school-item">
                        <span class="school-name"><?php echo htmlspecialchars($row['school_name']); ?></span>
                        <span class="school-count"><?php echo $row['count']; ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-school"></i>
                    <p>No school data available</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Enrollment Trend Chart -->
        <?php if (!empty($monthly_data)): ?>
        <div class="card">
            <div class="card-header">
                <h2>
                    <i class="fas fa-chart-line"></i>
                    Enrollment Trend
                </h2>
                <span class="badge">Last 6 Months</span>
            </div>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <p>No enrollment data available for chart</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h2>
            </div>
            <div class="quick-actions">
                <a href="view_students.php" class="action-btn">
                    <i class="fas fa-users"></i>
                    <span>View All Students</span>
                </a>
                <a href="export_data.php" class="action-btn">
                    <i class="fas fa-download"></i>
                    <span>Export Data</span>
                </a>
                <a href="reports.php" class="action-btn">
                    <i class="fas fa-file-alt"></i>
                    <span>Generate Reports</span>
                </a>
                <a href="settings.php" class="action-btn">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        <?php if (!empty($monthly_data)): ?>
        // Enrollment Trend Chart
        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php 
                    foreach(array_reverse($monthly_data) as $data) {
                        echo "'" . date('M Y', strtotime($data['month'] . '-01')) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Enrollments',
                    data: [<?php 
                        foreach(array_reverse($monthly_data) as $data) {
                            echo $data['count'] . ",";
                        }
                    ?>],
                    borderColor: '<?php echo $current_program['color']; ?>',
                    backgroundColor: '<?php echo $current_program['color']; ?>20',
                    borderWidth: 3,
                    pointBackgroundColor: '<?php echo $current_program['color']; ?>',
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
                        backgroundColor: '#333',
                        titleColor: 'white',
                        bodyColor: 'rgba(255,255,255,0.8)',
                        borderColor: '<?php echo $current_program['color']; ?>',
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
                        },
                        ticks: {
                            stepSize: 1
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

        // Add animation to cards on scroll
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

        document.querySelectorAll('.stat-card, .card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            observer.observe(card);
        });
    </script>
</body>
</html>