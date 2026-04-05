<?php
require_once '../config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get parameters
$department = isset($_GET['dept']) ? $_GET['dept'] : 'it';
$role = isset($_GET['role']) ? $_GET['role'] : 'student';

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Department configurations
$departments = [
    'crim' => [
        'name' => 'College of Criminal Justice',
        'icon' => '⚖️',
        'color' => '#dc2626',
        'programs' => ['BS Criminology'],
        'interest_count' => 342
    ],
    'it' => [
        'name' => 'College of Information Technology',
        'icon' => '💻',
        'color' => '#2563eb',
        'programs' => ['BSIT', 'BSCS', 'BSIS'],
        'interest_count' => 456
    ],
    'educ' => [
        'name' => 'College of Education',
        'icon' => '📚',
        'color' => '#16a34a',
        'programs' => ['BEEd', 'BSEd', 'BPEd'],
        'interest_count' => 289
    ],
    'oad' => [
        'name' => 'Office Administration',
        'icon' => '📋',
        'color' => '#9333ea',
        'programs' => ['BSOA'],
        'interest_count' => 198
    ],
    'hm' => [
        'name' => 'Hospitality Management',
        'icon' => '🍽️',
        'color' => '#ea580c',
        'programs' => ['BSHM', 'BSTM'],
        'interest_count' => 267
    ]
];

$current_dept = $departments[$department] ?? $departments['it'];

// Interest data (simulated database)
$interest_data = [
    'crim' => ['Jan' => 45, 'Feb' => 52, 'Mar' => 48, 'Apr' => 55, 'May' => 60, 'Jun' => 58],
    'it' => ['Jan' => 78, 'Feb' => 85, 'Mar' => 82, 'Apr' => 90, 'May' => 95, 'Jun' => 92],
    'educ' => ['Jan' => 34, 'Feb' => 38, 'Mar' => 35, 'Apr' => 42, 'May' => 45, 'Jun' => 40],
    'oad' => ['Jan' => 23, 'Feb' => 25, 'Mar' => 28, 'Apr' => 30, 'May' => 32, 'Jun' => 29],
    'hm' => ['Jan' => 41, 'Feb' => 44, 'Mar' => 47, 'Apr' => 50, 'May' => 53, 'Jun' => 48]
];

$inquiries = [
    'crim' => ['online' => 120, 'walkin' => 85, 'phone' => 45, 'email' => 92],
    'it' => ['online' => 245, 'walkin' => 156, 'phone' => 89, 'email' => 178],
    'educ' => ['online' => 98, 'walkin' => 67, 'phone' => 34, 'email' => 56],
    'oad' => ['online' => 67, 'walkin' => 45, 'phone' => 23, 'email' => 41],
    'hm' => ['online' => 134, 'walkin' => 89, 'phone' => 56, 'email' => 78]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_dept['name']; ?> | Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --dept-color: <?php echo $current_dept['color']; ?>;
            --dept-light: <?php echo $current_dept['color']; ?>22;
            --dept-dark: <?php echo $current_dept['color']; ?>dd;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: var(--dept-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid var(--dept-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--dept-color);
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .welcome-message {
            background: linear-gradient(135deg, var(--dept-color), var(--dept-dark));
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .welcome-message h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .btn {
            background: var(--dept-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: var(--dept-dark);
        }

        .logout-btn {
            background: #e74c3c;
        }

        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <span><?php echo $current_dept['icon']; ?></span>
                <?php echo $current_dept['name']; ?>
            </h1>
            <div>
                <span style="margin-right: 15px;">Role: <?php echo ucfirst($role); ?></span>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>

        <div class="welcome-message">
            <h2>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h2>
            <p>You are logged in as a <?php echo ucfirst($role); ?> in the <?php echo $current_dept['name']; ?>.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $current_dept['interest_count']; ?></div>
                <div class="stat-label">Total Students Enrolled</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($current_dept['programs']); ?></div>
                <div class="stat-label">Programs Offered</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $inquiries[$department]['online'] ?? 0; ?></div>
                <div class="stat-label">Online Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $inquiries[$department]['walkin'] ?? 0; ?></div>
                <div class="stat-label">Walk-in Inquiries</div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: var(--dept-color); margin-bottom: 20px;">Department Overview</h2>
            <p>Welcome to the <?php echo $current_dept['name']; ?> dashboard. Here you can manage and monitor all department activities.</p>
            
            <?php if ($role == 'admin'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <h3>Admin Controls</h3>
                    <p>You have full administrative access to this department.</p>
                </div>
            <?php elseif ($role == 'instructor'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <h3>Instructor Tools</h3>
                    <p>You can manage classes and view student progress.</p>
                </div>
            <?php elseif ($role == 'staff'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <h3>Staff Portal</h3>
                    <p>You have access to administrative functions.</p>
                </div>
            <?php else: ?>
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <h3>Student Portal</h3>
                    <p>View your courses, grades, and department announcements.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>