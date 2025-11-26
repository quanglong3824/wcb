<?php
/**
 * Configuration File
 * Welcome Board System - Quang Long Hotel
 */

// Session Configuration (phải đặt TRƯỚC session_start())
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Base URL
define('BASE_URL', 'http://localhost/quanglong3824/wcb/');

// Paths
define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// Database Configuration (from database.php)
require_once __DIR__ . '/database.php';

// Error Reporting (Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Application Settings
define('APP_NAME', 'Welcome Board System');
define('APP_VERSION', '1.0.0');
define('HOTEL_NAME', 'Quang Long Hotel');

// Upload Settings
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_MANAGER', 'manager');
define('ROLE_USER', 'user');

/**
 * Helper function to get base path
 */
function getBasePath($fromPath = '') {
    $depth = substr_count($fromPath, '/');
    return str_repeat('../', $depth);
}

/**
 * Helper function to sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Helper function to check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Helper function to check user role
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Helper function to redirect
 */
function redirect($url) {
    header("Location: $url");
    exit;
}
