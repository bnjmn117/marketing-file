<?php
require_once 'config.php';

echo "<h2>Database Check</h2>";

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p style='color:green'>✅ Users table exists</p>";
    
    // Count users
    $count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc();
    echo "<p>Total users: " . $count['total'] . "</p>";
    
    // Show sample users (without passwords)
    $users = $conn->query("SELECT id, fullname, email, course, is_verified FROM users LIMIT 5");
    if ($users->num_rows > 0) {
        echo "<h3>Sample Users:</h3>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Verified</th></tr>";
        while ($user = $users->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['fullname'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['course'] . "</td>";
            echo "<td>" . ($user['is_verified'] ? '✅ Yes' : '❌ No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>No users found. Please register first.</p>";
    }
} else {
    echo "<p style='color:red'>❌ Users table does not exist!</p>";
    echo "<p>Run the installation script: <a href='install.php'>install.php</a></p>";
}

echo "<p><a href='register.php'>Go to Registration</a> | <a href='login.php'>Go to Login</a></p>";
?>