<?php
/**
 * Authentication Check Middleware
 * Kiểm tra session đăng nhập cho tất cả các trang
 * Include file này ở đầu mọi trang cần bảo vệ
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if system is installed
$lockFile = dirname(__DIR__) . '/.installed';
if (!file_exists($lockFile)) {
    // Redirect to install page
    $installUrl = getInstallUrl();
    header("Location: {$installUrl}");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Save current URL to redirect back after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    $loginUrl = getLoginUrl();
    header("Location: {$loginUrl}");
    exit;
}

// Validate session data
if (empty($_SESSION['user_id']) || empty($_SESSION['username']) || empty($_SESSION['user_role'])) {
    // Invalid session, destroy and redirect
    session_destroy();
    $loginUrl = getLoginUrl();
    header("Location: {$loginUrl}?error=invalid_session");
    exit;
}

// Optional: Check session timeout (30 minutes of inactivity)
$sessionTimeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity'])) {
    $inactiveTime = time() - $_SESSION['last_activity'];
    
    if ($inactiveTime > $sessionTimeout) {
        // Session expired
        session_destroy();
        $loginUrl = getLoginUrl();
        header("Location: {$loginUrl}?session_expired=1");
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Optional: Regenerate session ID periodically for security (every 30 minutes)
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = time();
} elseif (time() - $_SESSION['created_at'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}

/**
 * Helper function to get login URL based on current location
 */
function getLoginUrl() {
    $currentPath = $_SERVER['SCRIPT_NAME'];
    $depth = substr_count(dirname($currentPath), '/') - substr_count($_SERVER['DOCUMENT_ROOT'], '/');
    
    // Adjust for different directory levels
    if (strpos($currentPath, '/api/') !== false) {
        return '../auth/login.php';
    } elseif (strpos($currentPath, '/admin/') !== false) {
        return '../auth/login.php';
    } elseif (strpos($currentPath, '/includes/') !== false) {
        return '../auth/login.php';
    } else {
        return 'auth/login.php';
    }
}

/**
 * Helper function to get install URL based on current location
 */
function getInstallUrl() {
    $currentPath = $_SERVER['SCRIPT_NAME'];
    
    // Adjust for different directory levels
    if (strpos($currentPath, '/api/') !== false) {
        return '../install.php';
    } elseif (strpos($currentPath, '/admin/') !== false) {
        return '../install.php';
    } elseif (strpos($currentPath, '/auth/') !== false) {
        return '../install.php';
    } elseif (strpos($currentPath, '/includes/') !== false) {
        return '../install.php';
    } else {
        return 'install.php';
    }
}

/**
 * Helper function to check user role
 */
function requireRole($role) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        http_response_code(403);
        die('Access Denied: You do not have permission to access this page.');
    }
}

/**
 * Helper function to check if user has any of the specified roles
 */
function requireAnyRole($roles) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles)) {
        http_response_code(403);
        die('Access Denied: You do not have permission to access this page.');
    }
}
