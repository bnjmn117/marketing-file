<?php
// Session configuration and cookie settings

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 0, // Session cookie (expires when browser closes)
    'path' => '/',
    'domain' => '', // Leave empty for current domain
    'secure' => isset($_SERVER['HTTPS']), // Only send over HTTPS if available
    'httponly' => true, // Prevent JavaScript access to session cookie
    'samesite' => 'Lax' // CSRF protection
]);

// Start the session
session_start();

// Optional: Regenerate session ID for security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to get current user role
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

// Function to logout user
function logout() {
    // Clear all session variables
    $_SESSION = [];

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();
}
?>
