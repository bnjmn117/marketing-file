<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to prevent header errors
ob_start();

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

// Get user session data
$user_id = $_SESSION['user_id'];
$course = isset($_SESSION['course']) ? $_SESSION['course'] : '';
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : '';
$course_type = isset($_SESSION['course_type']) ? $_SESSION['course_type'] : '';

// Normalize course codes to match dashboard mappings
$course = strtoupper(trim($course));
$course = preg_replace('/[^A-Z0-9]/', '', $course);

// IMPORTANT: Map HUMSS (one M) to HUMMS (two M's) for the dashboard
if ($course === 'HUMSS') {
    $course = 'HUMMS';
}

$courseAliases = [
    'IT' => 'BSIT',
    'BSCS' => 'BSIT',
    'BSIS' => 'BSIT',
    'OAD' => 'OAD',
    'HM' => 'HM',
    'CRIM' => 'CRIM',
    'EDUC' => 'EDUC',
    'STEM' => 'STEM',
    'HUMSS' => 'HUMMS', // Add this mapping
    'HUMMS' => 'HUMMS',
    'TECHVOC' => 'TECHVOC',
];

if (isset($courseAliases[$course])) {
    $course = $courseAliases[$course];
} else {
    // Also allow shorthand prefixes
    if (str_starts_with($course, 'IT')) {
        $course = 'BSIT';
    }
    if (str_starts_with($course, 'HUM')) {
        $course = 'HUMMS';
    }
}

// Persist normalized value back into session
$_SESSION['course'] = $course;

// Debug: Check session data
if (empty($course)) {
    error_log("Dashboard: No course found in session for user ID: " . $user_id);
    
    // Try to get user data from database
    $sql = "SELECT course, course_type, fullname FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user_data = $result->fetch_assoc()) {
        // Update session with database data
        $_SESSION['course'] = $user_data['course'];
        $_SESSION['course_type'] = $user_data['course_type'];
        $_SESSION['fullname'] = $user_data['fullname'];
        
        $course = $user_data['course'];
        $fullname = $user_data['fullname'];
        $course_type = $user_data['course_type'];

        // Normalize course values after fetching from database
        $course = strtoupper(trim($course));
        
        // Map HUMSS to HUMMS
        if ($course === 'HUMSS') {
            $course = 'HUMMS';
        }
        
        if (isset($courseAliases[$course])) {
            $course = $courseAliases[$course];
        }

        // Persist normalized value back into session
        $_SESSION['course'] = $course;

        error_log("Dashboard: Session updated from database for user: " . $fullname);
    } else {
        error_log("Dashboard: User not found in database: " . $user_id);
        session_destroy();
        header("Location: login.php?error=User not found");
        exit();
    }
    $stmt->close();
}

// Define dashboard paths
$dashboard_paths = [
    'BSIT' => 'dashboards/bsit_dashboard.php',
    'HM' => 'dashboards/hm_dashboard.php',
    'OAD' => 'dashboards/oad_dashboard.php',
    'CRIM' => 'dashboards/crim_dashboard.php',
    'EDUC' => 'dashboards/educ_dashboard.php',
    'STEM' => 'dashboards/stem_dashboard.php',
    'HUMMS' => 'dashboards/humms_dashboard.php',
    'TECHVOC' => 'dashboards/techvoc_dashboard.php'
];

// If the course still isn't registered, attempt fallbacks
if (!array_key_exists($course, $dashboard_paths)) {
    if (stripos($course, 'IT') !== false) {
        $course = 'BSIT';
    } elseif (stripos($course, 'HUM') !== false) {
        $course = 'HUMMS';
    } elseif (stripos($course, 'SCI') !== false || stripos($course, 'STEM') !== false) {
        $course = 'STEM';
    } elseif (stripos($course, 'TECH') !== false) {
        $course = 'TECHVOC';
    }
    $_SESSION['course'] = $course;
}

// Check if course exists in our paths
if (array_key_exists($course, $dashboard_paths)) {
    $dashboard_file = $dashboard_paths[$course];
    
    // Check if dashboard file exists
    if (file_exists($dashboard_file)) {
        error_log("Dashboard: Redirecting to $dashboard_file for course $course");
        header("Location: $dashboard_file");
        exit();
    } else {
        error_log("Dashboard: File not found - $dashboard_file");
        $error = "Dashboard file not found for course: $course";
    }
} else {
    error_log("Dashboard: Invalid course - $course");
    $error = "Invalid course: $course";
}

// If we get here, there's an error - show error page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Error</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            text-align: center;
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h2 {
            color: #c62828;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .debug-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            text-align: left;
            margin-bottom: 20px;
        }
        .debug-info h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .debug-info ul {
            list-style: none;
            padding: 0;
        }
        .debug-info li {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            margin: 5px;
        }
        .btn-logout {
            background: #c62828;
        }
        .btn-fix {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h2>Dashboard Error</h2>
        <p><?php echo isset($error) ? $error : "An unknown error occurred."; ?></p>
        
        <div class="debug-info">
            <h3>Debug Information:</h3>
            <ul>
                <li><strong>User ID:</strong> <?php echo $user_id; ?></li>
                <li><strong>Full Name:</strong> <?php echo htmlspecialchars($fullname); ?></li>
                <li><strong>Course:</strong> <?php echo htmlspecialchars($course); ?></li>
                <li><strong>Course Type:</strong> <?php echo htmlspecialchars($course_type); ?></li>
                <li><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive'; ?></li>
                <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
            </ul>
        </div>
        
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="dashboard.php" class="btn">↻ Retry</a>
            <a href="logout.php" class="btn btn-logout">🚪 Logout</a>
            <a href="index.php" class="btn">🏠 Home</a>
            <a href="fix_humms.php" class="btn btn-fix">🔧 Fix HUMSS Issue</a>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>