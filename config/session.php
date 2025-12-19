<?php
// ============================================
// SESSION CONFIGURATION FILE
// ============================================
// This file handles user sessions (keeping users logged in)
// Include this file on pages that require authentication

// ============================================
// START SESSION
// ============================================
// Session allows us to store user information across different pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// SESSION SECURITY SETTINGS
// ============================================
// Make sessions more secure

// Regenerate session ID to prevent session fixation attacks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Set session cookie parameters for security
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to session cookie
ini_set('session.use_only_cookies', 1);  // Only use cookies for session
ini_set('session.cookie_secure', 0);     // Set to 1 if using HTTPS

// ============================================
// SESSION TIMEOUT SETTINGS
// ============================================
// Auto-logout after 30 minutes of inactivity
define('SESSION_TIMEOUT', 1800); // 1800 seconds = 30 minutes

// Check if session has timed out
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    if ($inactive_time > SESSION_TIMEOUT) {
        // Session has expired
        session_unset();
        session_destroy();
        header("Location: ../index.php?timeout=1");
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 * Returns true if doctor is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['doctor_id']) && !empty($_SESSION['doctor_id']);
}

/**
 * Check if user is logged in, redirect to login if not
 * Use this function at the top of protected pages
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php?login_required=1");
        exit();
    }
}

/**
 * Get logged in doctor's ID
 * Returns doctor_id or null if not logged in
 */
function getDoctorId() {
    return $_SESSION['doctor_id'] ?? null;
}

/**
 * Get logged in doctor's name
 * Returns full name or 'Guest' if not logged in
 */
function getDoctorName() {
    if (isset($_SESSION['doctor_name'])) {
        return $_SESSION['doctor_name'];
    }
    return 'Guest';
}

/**
 * Get logged in doctor's email
 * Returns email or null if not logged in
 */
function getDoctorEmail() {
    return $_SESSION['doctor_email'] ?? null;
}

/**
 * Set user session after successful login
 * Call this function after verifying login credentials
 */
function setUserSession($doctor_id, $doctor_name, $doctor_email, $username) {
    $_SESSION['doctor_id'] = $doctor_id;
    $_SESSION['doctor_name'] = $doctor_name;
    $_SESSION['doctor_email'] = $doctor_email;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}

/**
 * Logout user - destroy all session data
 * Call this function when user clicks logout
 */
function logout() {
    session_unset();
    session_destroy();
    header("Location: ../index.php?logout=1");
    exit();
}

/**
 * Check if email is verified
 * Returns true if email is verified, false otherwise
 */
function isEmailVerified() {
    return isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 1;
}

// ============================================
// DISPLAY SESSION STATUS (for debugging)
// ============================================
// Uncomment below lines to see session info (ONLY for testing!)
/*
echo "<pre>";
echo "Session Status: " . (isLoggedIn() ? "Logged In" : "Not Logged In") . "<br>";
echo "Doctor ID: " . getDoctorId() . "<br>";
echo "Doctor Name: " . getDoctorName() . "<br>";
echo "Time remaining: " . (SESSION_TIMEOUT - (time() - $_SESSION['last_activity'])) . " seconds<br>";
echo "</pre>";
*/

?>
