<?php
/**
 * Security Helper Functions
 * CSRF protection, XSS prevention, Rate limiting
 */

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    // Regenerate token if older than 1 hour
    if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Get CSRF token for JavaScript
 */
function csrfMeta() {
    $token = generateCSRFToken();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
}

/**
 * Validate CSRF for POST requests
 */
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (!verifyCSRFToken($token)) {
            http_response_code(403);
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            } else {
                die('Invalid CSRF token. Please refresh the page and try again.');
            }
            exit;
        }
    }
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Sanitize input string
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

/**
 * Sanitize for output (XSS prevention)
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Rate limiting check
 * Returns true if request is allowed, false if rate limited
 */
function checkRateLimit($key, $maxRequests = 60, $timeWindow = 60) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $rateLimitKey = 'rate_limit_' . $key;
    $now = time();
    
    if (!isset($_SESSION[$rateLimitKey])) {
        $_SESSION[$rateLimitKey] = [
            'count' => 1,
            'start_time' => $now
        ];
        return true;
    }
    
    $data = $_SESSION[$rateLimitKey];
    
    // Reset if time window has passed
    if ($now - $data['start_time'] > $timeWindow) {
        $_SESSION[$rateLimitKey] = [
            'count' => 1,
            'start_time' => $now
        ];
        return true;
    }
    
    // Check if limit exceeded
    if ($data['count'] >= $maxRequests) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$rateLimitKey]['count']++;
    
    return true;
}

/**
 * Apply rate limiting and return error if exceeded
 */
function applyRateLimit($key = 'api', $maxRequests = 60, $timeWindow = 60) {
    if (!checkRateLimit($key, $maxRequests, $timeWindow)) {
        http_response_code(429);
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $timeWindow
            ]);
        } else {
            die('Too many requests. Please try again later.');
        }
        exit;
    }
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedTypes = [], $maxSize = 52428800) {
    $errors = [];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds maximum allowed (' . formatBytes($maxSize) . ')';
    }
    
    // Check MIME type
    if (!empty($allowedTypes)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'File type not allowed: ' . $mimeType;
        }
    }
    
    // Check for malicious content in images
    if (strpos($file['type'], 'image/') === 0) {
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = 'Invalid image file';
        }
    }
    
    return $errors;
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Set security headers
 */
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS filter
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (basic)
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data: blob:;");
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    // Optional: Add more rules
    // if (!preg_match('/[A-Z]/', $password)) {
    //     $errors[] = 'Password must contain at least one uppercase letter';
    // }
    // if (!preg_match('/[0-9]/', $password)) {
    //     $errors[] = 'Password must contain at least one number';
    // }
    
    return $errors;
}

/**
 * Generate secure random token
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Log security event
 */
function logSecurityEvent($event, $details = '', $userId = null) {
    $logFile = dirname(__DIR__) . '/logs/security.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = sprintf(
        "[%s] %s | IP: %s | User: %s | Details: %s\n",
        date('Y-m-d H:i:s'),
        $event,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $userId ?? ($_SESSION['user_id'] ?? 'guest'),
        $details
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
