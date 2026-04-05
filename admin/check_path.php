<?php
// admin/check_path.php
echo "<h1>Path Checker</h1>";

echo "<h2>Current Directory:</h2>";
echo "<p>" . __DIR__ . "</p>";

echo "<h2>Looking for config.php:</h2>";
$config_path = __DIR__ . '/../config.php';
if (file_exists($config_path)) {
    echo "<p style='color:green'>✅ config.php found at: $config_path</p>";
    
    // Try to include it
    require_once $config_path;
    echo "<p style='color:green'>✅ config.php loaded successfully</p>";
    
    // Check database connection
    if (isset($conn) && $conn->ping()) {
        echo "<p style='color:green'>✅ Database connected: " . DB_NAME . "</p>";
    } else {
        echo "<p style='color:red'>❌ Database connection failed</p>";
    }
} else {
    echo "<p style='color:red'>❌ config.php NOT found at: $config_path</p>";
    
    // List files in parent directory
    $parent = __DIR__ . '/..';
    echo "<h3>Files in parent directory ($parent):</h3>";
    $files = scandir($parent);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

echo "<h2>Server Info:</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
?>