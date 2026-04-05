<?php
require_once 'config.php';

echo "<h1>Dashboard System Check</h1>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red'>❌ You are not logged in. <a href='login.php'>Login here</a></p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$course = $_SESSION['course'];
$fullname = $_SESSION['fullname'];

echo "<h2>Session Information:</h2>";
echo "<ul>";
echo "<li><strong>User ID:</strong> " . $user_id . "</li>";
echo "<li><strong>Full Name:</strong> " . htmlspecialchars($fullname) . "</li>";
echo "<li><strong>Course:</strong> " . htmlspecialchars($course) . "</li>";
echo "<li><strong>Session ID:</strong> " . session_id() . "</li>";
echo "</ul>";

// Check database
echo "<h2>Database Information:</h2>";
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo "<p style='color:green'>✅ User found in database</p>";
    echo "<ul>";
    echo "<li><strong>Full Name:</strong> " . htmlspecialchars($user['fullname']) . "</li>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
    echo "<li><strong>Course:</strong> " . htmlspecialchars($user['course']) . "</li>";
    echo "<li><strong>Course Type:</strong> " . htmlspecialchars($user['course_type']) . "</li>";
    echo "<li><strong>Verified:</strong> " . ($user['is_verified'] ? '✅ Yes' : '❌ No') . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color:red'>❌ User not found in database</p>";
}

// Check dashboards folder
echo "<h2>Dashboard Files Check:</h2>";
$dashboards = [
    'BSIT' => 'dashboards/bsit_dashboard.php',
    'HM' => 'dashboards/hm_dashboard.php',
    'OAD' => 'dashboards/oad_dashboard.php',
    'CRIM' => 'dashboards/crim_dashboard.php',
    'EDUC' => 'dashboards/educ_dashboard.php',
    'STEM' => 'dashboards/stem_dashboard.php',
    'HUMMS' => 'dashboards/humms_dashboard.php',
    'TECHVOC' => 'dashboards/techvoc_dashboard.php'
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Course</th><th>File Path</th><th>Status</th></tr>";

foreach ($dashboards as $course_code => $file) {
    $status = file_exists($file) ? '✅ Found' : '❌ Not Found';
    $color = file_exists($file) ? 'green' : 'red';
    echo "<tr>";
    echo "<td><strong>$course_code</strong></td>";
    echo "<td>$file</td>";
    echo "<td style='color: $color'>$status</td>";
    echo "</tr>";
}
echo "</table>";

// Check current course dashboard
echo "<h2>Your Dashboard:</h2>";
$your_dashboard = "dashboards/" . strtolower($course) . "_dashboard.php";
if (file_exists($your_dashboard)) {
    echo "<p style='color:green'>✅ Your dashboard exists: $your_dashboard</p>";
    echo "<p><a href='$your_dashboard' style='background: green; color: white; padding: 10px 20px; text-decoration: none;'>Go to your dashboard</a></p>";
} else {
    echo "<p style='color:red'>❌ Your dashboard not found: $your_dashboard</p>";
}

// Create missing dashboards if needed
echo "<h2>Create Missing Dashboards:</h2>";
foreach ($dashboards as $course_code => $file) {
    if (!file_exists($file)) {
        echo "<p>Creating $file... ";
        $content = get_dashboard_template($course_code);
        if (file_put_contents($file, $content)) {
            echo "<span style='color:green'>✅ Created</span></p>";
        } else {
            echo "<span style='color:red'>❌ Failed</span></p>";
        }
    }
}

function get_dashboard_template($course) {
    return '<?php
require_once \'config.php\';

if (!isset($_SESSION[\'user_id\']) || $_SESSION[\'course\'] != \'' . $course . '\') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION[\'user_id\'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $course . ' Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>' . $course . ' Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($user[\'fullname\']); ?></span>
            <a href="../logout.php" style="color: white; margin-left: 20px;">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>Welcome to ' . $course . ' Program!</h1>
            <p>This is your personalized dashboard.</p>
            <p><strong>Previous School:</strong> <?php 
                if ($user[\'previous_school_id\']) {
                    echo getSchoolName($conn, $user[\'previous_school_id\']);
                } else {
                    echo htmlspecialchars($user[\'previous_school_other\']);
                }
            ?></p>
        </div>
    </div>
</body>
</html>';
}

// Add school name function if not exists
if (!function_exists('getSchoolName')) {
    function getSchoolName($conn, $school_id) {
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
}
?>
<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f5f5f5;
    }
    h1, h2 {
        color: #333;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin: 10px 0;
        background: white;
    }
    th {
        background: #667eea;
        color: white;
        padding: 10px;
    }
    td {
        padding: 10px;
    }
    ul {
        background: white;
        padding: 20px;
        border-radius: 5px;
    }
</style>