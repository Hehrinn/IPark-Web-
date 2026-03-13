<?php
/**
 * iPark Database Configuration
 * Secure database connection using MySQLi
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database Credentials (Hostinger)
define('DB_HOST', 'localhost');
define('DB_USER', 'u847001018_spencer');
define('DB_PASSWORD', 'SpencerMil@no123');
define('DB_NAME', 'u847001018_citialerts');  // Use existing database
define('DB_PORT', 3306);

// Site Configuration
define('SITE_URL', 'https://jsmkj.space/Ipark');
define('SITE_NAME', 'iPark');
define('ADMIN_EMAIL', 'admin@ipark.local');

// Security Configuration
define('SESSION_TIMEOUT', 1800);  // 30 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 12]);

// Email Configuration (for future use)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// Create Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Check Connection
if ($conn->connect_error) {
    // Log error securely, don't expose to user
    error_log("Database Connection Error: " . $conn->connect_error);
    die("Connection to database failed. Please try again later.");
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Set timezone
date_default_timezone_set('UTC');
$conn->query("SET time_zone = '+00:00'");

?>
