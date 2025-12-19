<?php
// ============================================
// DATABASE CONFIGURATION FILE
// ============================================
// This file handles the connection to MySQL database
// All other PHP files will include this file to access the database

// ============================================
// DATABASE CREDENTIALS
// ============================================
// Change these values according to your local setup

define('DB_HOST', '127.0.0.1:3307');      // Database server
define('DB_USER', 'root');           // Database username (default 'root')
define('DB_PASS', '');               // Database password (default empty)
define('DB_NAME', 'ehr_system');     // Database name (must match the one in SQL file)

// ============================================
// CHARACTER SET (for proper encoding)
// ============================================
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CREATE DATABASE CONNECTION
// ============================================
// Using mysqli (MySQL Improved) - more secure than old mysql_connect

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ============================================
// CHECK IF CONNECTION WAS SUCCESSFUL
// ============================================
if ($conn->connect_error) {
    // If connection fails, stop execution and show error
    die("Database Connection Failed: " . $conn->connect_error);
}

// ============================================
// SET CHARACTER SET
// ============================================
// This ensures proper handling of special characters and emojis
$conn->set_charset(DB_CHARSET);

// ============================================
// OPTIONAL: Display success message (for testing only)
// ============================================
// Uncomment the line below to test if connection works
// echo "Database connected successfully!";

// ============================================
// TIMEZONE SETTING (optional but recommended)
// ============================================
// Set timezone for date/time operations
date_default_timezone_set('Europe/Berlin'); // Change to your timezone

?>
