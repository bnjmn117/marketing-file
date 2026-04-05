<?php
/**
 * Program Head Logout Script
 * Features: Secure logout, session cleanup, redirect with message
 */

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Log logout activity for debugging
if (isset($_SESSION['program_head_id']) && isset($_SESSION['program_code'])) {
    $head_id = $_SESSION['program_head_id'];
    $program_code = $_SESSION['program_code'];
    $head_name = $_SESSION['head_name'] ?? 'Unknown';
    
    // Write to error log
    error_log("Program Head Logout: $head_name ($program_code) - ID: $head_id logged out at " . date('Y-m-d H:i:s'));
    
    // Optional: Save to database for audit trail
    /*
    require_once '../config.php';
    $sql = "INSERT INTO program_head_logs (head_id, action, timestamp) VALUES (?, 'logout', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $head_id);
    $stmt->execute();
    $stmt->close();
    */
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start a new session for flash message
session_start();

// Set logout message
$_SESSION['logout_message'] = "You have been successfully logged out. Thank you for using the Program Head Portal.";

// Redirect to login page with message
header("Location: login.php?msg=loggedout");
exit();
?>