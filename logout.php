<?php
// Start session
session_start();

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

// Optional: Add logout message
session_start(); // Start new session for message
$_SESSION['logout_message'] = "You have been successfully logged out.";

// Redirect to index.php (homepage)
header("Location: index.php");
exit();
?>