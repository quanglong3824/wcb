<?php
/**
 * Database Configuration
 * Hỗ trợ kết nối song song localhost và host
 * Tự động phát hiện môi trường và kết nối phù hợp
 */

// =====================================================
// CẤU HÌNH DATABASE
// =====================================================

// Phát hiện môi trường (localhost hoặc host)
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) 
               || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;

// Cấu hình cho LOCALHOST
define('DB_LOCAL_HOST', 'localhost');
define('DB_LOCAL_USER', 'root');
define('DB_LOCAL_PASS', '');
define('DB_LOCAL_NAME', 'auroraho_wcb');
define('DB_LOCAL_PORT', 3306);
define('DB_LOCAL_CHARSET', 'utf8mb4');

// Cấu hình cho HOST/PRODUCTION
define('DB_REMOTE_HOST', 'localhost:3306'); // Thay bằng host thực tế
define('DB_REMOTE_USER', 'auroraho_longdev'); // Thay bằng username thực tế
define('DB_REMOTE_PASS', '@longdev3824'); // Thay bằng password thực tế
define('DB_REMOTE_NAME', 'auroraho_wcb');
define('DB_REMOTE_PORT', 3306);
define('DB_REMOTE_CHARSET', 'utf8mb4');

// Chọn cấu hình dựa trên môi trường
if ($isLocalhost) {
    define('DB_HOST', DB_LOCAL_HOST);
    define('DB_USER', DB_LOCAL_USER);
    define('DB_PASS', DB_LOCAL_PASS);
    define('DB_NAME', DB_LOCAL_NAME);
    define('DB_PORT', DB_LOCAL_PORT);
    define('DB_CHARSET', DB_LOCAL_CHARSET);
    define('DB_ENVIRONMENT', 'LOCAL');
} else {
    define('DB_HOST', DB_REMOTE_HOST);
    define('DB_USER', DB_REMOTE_USER);
    define('DB_PASS', DB_REMOTE_PASS);
    define('DB_NAME', DB_REMOTE_NAME);
    define('DB_PORT', DB_REMOTE_PORT);
    define('DB_CHARSET', DB_REMOTE_CHARSET);
    define('DB_ENVIRONMENT', 'REMOTE');
}

// =====================================================
// KẾT NỐI DATABASE
// =====================================================

/**
 * Kết nối database sử dụng MySQLi
 * @return mysqli|false
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            // Tạo kết nối
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            
            // Kiểm tra lỗi kết nối
            if ($conn->connect_error) {
                error_log("Database Connection Error: " . $conn->connect_error);
                return false;
            }
            
            // Set charset
            $conn->set_charset(DB_CHARSET);
            
            // Set timezone
            $conn->query("SET time_zone = '+07:00'");
            
        } catch (Exception $e) {
            error_log("Database Exception: " . $e->getMessage());
            return false;
        }
    }
    
    return $conn;
}

/**
 * Kết nối database sử dụng PDO
 * @return PDO|false
 */
function getPDOConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'"
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("PDO Connection Error: " . $e->getMessage());
            return false;
        }
    }
    
    return $pdo;
}

/**
 * Kiểm tra kết nối database
 * @return array
 */
function checkDatabaseConnection() {
    $result = [
        'success' => false,
        'environment' => DB_ENVIRONMENT,
        'host' => DB_HOST,
        'database' => DB_NAME,
        'user' => DB_USER,
        'charset' => DB_CHARSET,
        'message' => '',
        'mysqli' => false,
        'pdo' => false,
        'server_info' => '',
        'tables' => []
    ];
    
    // Test MySQLi
    $mysqli = getDBConnection();
    if ($mysqli && !$mysqli->connect_error) {
        $result['mysqli'] = true;
        $result['server_info'] = $mysqli->server_info;
        
        // Lấy danh sách bảng
        $tablesQuery = $mysqli->query("SHOW TABLES");
        if ($tablesQuery) {
            while ($row = $tablesQuery->fetch_array()) {
                $result['tables'][] = $row[0];
            }
        }
    }
    
    // Test PDO
    $pdo = getPDOConnection();
    if ($pdo) {
        $result['pdo'] = true;
    }
    
    // Kết luận
    if ($result['mysqli'] || $result['pdo']) {
        $result['success'] = true;
        $result['message'] = 'Kết nối database thành công!';
    } else {
        $result['message'] = 'Không thể kết nối database!';
    }
    
    return $result;
}

/**
 * Đóng kết nối database
 */
function closeDBConnection() {
    $conn = getDBConnection();
    if ($conn) {
        $conn->close();
    }
}

// =====================================================
// HELPER FUNCTIONS
// =====================================================

/**
 * Thực thi query và trả về kết quả
 * @param string $query
 * @param array $params
 * @return array|false
 */
function dbQuery($query, $params = []) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    if (empty($params)) {
        $result = $conn->query($query);
        if (!$result) return false;
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        $stmt = $conn->prepare($query);
        if (!$stmt) return false;
        
        // Bind parameters
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
}

/**
 * Thực thi query INSERT/UPDATE/DELETE
 * @param string $query
 * @param array $params
 * @return bool|int
 */
function dbExecute($query, $params = []) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    if (empty($params)) {
        $result = $conn->query($query);
        return $result ? $conn->insert_id : false;
    } else {
        $stmt = $conn->prepare($query);
        if (!$stmt) return false;
        
        // Bind parameters
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        
        $result = $stmt->execute();
        $insertId = $stmt->insert_id;
        
        $stmt->close();
        return $result ? ($insertId > 0 ? $insertId : true) : false;
    }
}

/**
 * Escape string để tránh SQL injection
 * @param string $string
 * @return string
 */
function dbEscape($string) {
    $conn = getDBConnection();
    if (!$conn) return $string;
    return $conn->real_escape_string($string);
}

// =====================================================
// AUTO CONNECT
// =====================================================

// Tự động kết nối khi file được include
$GLOBALS['db_connection'] = getDBConnection();

// Ghi log môi trường (chỉ trong development)
if (DB_ENVIRONMENT === 'LOCAL') {
    error_log("WCB System: Connected to " . DB_ENVIRONMENT . " database (" . DB_NAME . ")");
}
