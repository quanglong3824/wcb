<?php
/**
 * Health check endpoint
 * Kiểm tra trạng thái hệ thống
 */

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => time(),
    'datetime' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Kiểm tra config file
$health['checks']['config_file'] = file_exists('config.php') ? 'ok' : 'error';

// Kiểm tra database
try {
    require_once 'config.php';
    $conn = getDBConnection();
    
    // Test query
    $result = $conn->query("SELECT COUNT(*) as count FROM welcome_boards");
    if ($result) {
        $row = $result->fetch_assoc();
        $health['checks']['database'] = 'ok';
        $health['stats']['total_boards'] = $row['count'];
        
        // Đếm active boards
        $result = $conn->query("SELECT COUNT(*) as count FROM welcome_boards WHERE status = 'active'");
        $row = $result->fetch_assoc();
        $health['stats']['active_boards'] = $row['count'];
    } else {
        $health['checks']['database'] = 'error';
        $health['status'] = 'degraded';
    }
} catch (Exception $e) {
    $health['checks']['database'] = 'error';
    $health['checks']['database_error'] = $e->getMessage();
    $health['status'] = 'error';
}

// Kiểm tra thư mục uploads
if (is_dir('uploads') && is_writable('uploads')) {
    $health['checks']['uploads_dir'] = 'ok';
    $health['stats']['uploads_count'] = count(glob('uploads/*'));
} else {
    $health['checks']['uploads_dir'] = 'error';
    $health['status'] = 'degraded';
}

// Kiểm tra thư mục backups
if (is_dir('backups') && is_writable('backups')) {
    $health['checks']['backups_dir'] = 'ok';
    $health['stats']['backups_count'] = count(glob('backups/*.json'));
    
    // Backup mới nhất
    $backups = glob('backups/backup_*.json');
    if (!empty($backups)) {
        rsort($backups);
        $latest = $backups[0];
        $health['stats']['latest_backup'] = basename($latest);
        $health['stats']['latest_backup_time'] = date('Y-m-d H:i:s', filemtime($latest));
        $health['stats']['latest_backup_age_hours'] = round((time() - filemtime($latest)) / 3600, 1);
    }
} else {
    $health['checks']['backups_dir'] = 'error';
    $health['status'] = 'degraded';
}

// Kiểm tra PHP version
$health['system']['php_version'] = PHP_VERSION;
$health['system']['server_software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'unknown';

// Kiểm tra memory
$health['system']['memory_usage'] = round(memory_get_usage() / 1024 / 1024, 2) . ' MB';
$health['system']['memory_limit'] = ini_get('memory_limit');

// Kiểm tra upload limits
$health['system']['upload_max_filesize'] = ini_get('upload_max_filesize');
$health['system']['post_max_size'] = ini_get('post_max_size');

// HTTP status code
http_response_code($health['status'] === 'ok' ? 200 : 503);

echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
