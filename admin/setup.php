<?php
// admin/setup.php - Run this once to setup admin accounts
require_once __DIR__ . '/../config.php';

echo "<h1>Admin Setup</h1>";

// Create admin_users table
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    admin_type ENUM('main', 'college', 'highschool') NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "<p style='color:green'>✅ Admin users table created successfully</p>";
} else {
    echo "<p style='color:red'>❌ Error creating table: " . $conn->error . "</p>";
}

// Delete existing admin accounts (optional - comment out if you want to keep existing)
// $conn->query("DELETE FROM admin_users");

// Insert default admin accounts
$admins = [
    ['mainadmin', 'Main Administrator', 'mainadmin@coursereg.com', 'main'],
    ['collegeadmin', 'College Administrator', 'collegeadmin@coursereg.com', 'college'],
    ['hsadmin', 'High School Administrator', 'hsadmin@coursereg.com', 'highschool']
];

$password = password_hash('admin123', PASSWORD_DEFAULT);
$inserted = 0;

foreach ($admins as $admin) {
    $check = $conn->query("SELECT id FROM admin_users WHERE username = '$admin[0]'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO admin_users (username, password, fullname, email, admin_type) 
                VALUES ('$admin[0]', '$password', '$admin[1]', '$admin[2]', '$admin[3]')";
        if ($conn->query($sql)) {
            $inserted++;
            echo "<p style='color:green'>✅ Created admin: $admin[0]</p>";
        } else {
            echo "<p style='color:red'>❌ Error creating $admin[0]: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:orange'>⚠️ Admin already exists: $admin[0]</p>";
    }
}

echo "<h2>Setup Complete!</h2>";
echo "<p>Total admins inserted: $inserted</p>";
echo "<p><a href='login.php' style='background: #3498DB; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a></p>";
?>