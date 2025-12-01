<?php
/**
 * Backup API
 * Database and files backup management
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Only super_admin can access
if ($_SESSION['user_role'] !== 'super_admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'create':
        createBackup();
        break;
    case 'list':
        listBackups();
        break;
    case 'download':
        downloadBackup();
        break;
    case 'delete':
        deleteBackup();
        break;
    case 'restore':
        restoreBackup();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Create database backup
 */
function createBackup() {
    $backupDir = dirname(__DIR__) . '/backups';
    
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d_His');
    $filename = "backup_{$timestamp}.sql";
    $filepath = $backupDir . '/' . $filename;
    
    // Get database config
    $config = require dirname(__DIR__) . '/config/php/database.php';
    $dbConfig = $config['localhost'] ?? $config['host'] ?? null;
    
    if (!$dbConfig) {
        echo json_encode(['success' => false, 'message' => 'Database config not found']);
        return;
    }
    
    // Build mysqldump command
    $command = sprintf(
        'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
        escapeshellarg($dbConfig['host']),
        escapeshellarg($dbConfig['username']),
        escapeshellarg($dbConfig['password']),
        escapeshellarg($dbConfig['database']),
        escapeshellarg($filepath)
    );
    
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($filepath)) {
        // Compress the backup
        $gzFilepath = $filepath . '.gz';
        $fp = gzopen($gzFilepath, 'w9');
        gzwrite($fp, file_get_contents($filepath));
        gzclose($fp);
        
        // Remove uncompressed file
        unlink($filepath);
        
        $filesize = filesize($gzFilepath);
        
        // Log activity
        $conn = getDBConnection();
        if ($conn) {
            logActivity($conn, 'create_backup', 'backup', 0, "Created backup: {$filename}.gz");
            $conn->close();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Backup created successfully',
            'filename' => $filename . '.gz',
            'size' => formatBytes($filesize)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create backup',
            'error' => implode("\n", $output)
        ]);
    }
}

/**
 * List available backups
 */
function listBackups() {
    $backupDir = dirname(__DIR__) . '/backups';
    
    if (!is_dir($backupDir)) {
        echo json_encode(['success' => true, 'backups' => []]);
        return;
    }
    
    $files = glob($backupDir . '/backup_*.sql.gz');
    $backups = [];
    
    foreach ($files as $file) {
        $filename = basename($file);
        $backups[] = [
            'filename' => $filename,
            'size' => filesize($file),
            'size_formatted' => formatBytes(filesize($file)),
            'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            'created_at_formatted' => date('d/m/Y H:i', filemtime($file))
        ];
    }
    
    // Sort by date descending
    usort($backups, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    echo json_encode([
        'success' => true,
        'backups' => $backups,
        'total' => count($backups)
    ]);
}

/**
 * Download backup file
 */
function downloadBackup() {
    $filename = isset($_GET['filename']) ? basename($_GET['filename']) : '';
    $backupDir = dirname(__DIR__) . '/backups';
    $filepath = $backupDir . '/' . $filename;
    
    if (empty($filename) || !file_exists($filepath)) {
        echo json_encode(['success' => false, 'message' => 'Backup file not found']);
        return;
    }
    
    // Set headers for download
    header('Content-Type: application/gzip');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache');
    
    readfile($filepath);
    exit;
}

/**
 * Delete backup file
 */
function deleteBackup() {
    $filename = isset($_GET['filename']) ? basename($_GET['filename']) : '';
    $backupDir = dirname(__DIR__) . '/backups';
    $filepath = $backupDir . '/' . $filename;
    
    if (empty($filename) || !file_exists($filepath)) {
        echo json_encode(['success' => false, 'message' => 'Backup file not found']);
        return;
    }
    
    if (unlink($filepath)) {
        // Log activity
        $conn = getDBConnection();
        if ($conn) {
            logActivity($conn, 'delete_backup', 'backup', 0, "Deleted backup: {$filename}");
            $conn->close();
        }
        
        echo json_encode(['success' => true, 'message' => 'Backup deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete backup']);
    }
}

/**
 * Restore from backup
 */
function restoreBackup() {
    $filename = isset($_GET['filename']) ? basename($_GET['filename']) : '';
    $backupDir = dirname(__DIR__) . '/backups';
    $filepath = $backupDir . '/' . $filename;
    
    if (empty($filename) || !file_exists($filepath)) {
        echo json_encode(['success' => false, 'message' => 'Backup file not found']);
        return;
    }
    
    // Get database config
    $config = require dirname(__DIR__) . '/config/php/database.php';
    $dbConfig = $config['localhost'] ?? $config['host'] ?? null;
    
    if (!$dbConfig) {
        echo json_encode(['success' => false, 'message' => 'Database config not found']);
        return;
    }
    
    // Decompress and restore
    $command = sprintf(
        'gunzip -c %s | mysql --host=%s --user=%s --password=%s %s 2>&1',
        escapeshellarg($filepath),
        escapeshellarg($dbConfig['host']),
        escapeshellarg($dbConfig['username']),
        escapeshellarg($dbConfig['password']),
        escapeshellarg($dbConfig['database'])
    );
    
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0) {
        // Log activity
        $conn = getDBConnection();
        if ($conn) {
            logActivity($conn, 'restore_backup', 'backup', 0, "Restored from backup: {$filename}");
            $conn->close();
        }
        
        echo json_encode(['success' => true, 'message' => 'Backup restored successfully']);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to restore backup',
            'error' => implode("\n", $output)
        ]);
    }
}

/**
 * Format bytes
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

/**
 * Log activity
 */
function logActivity($conn, $action, $entityType, $entityId, $description) {
    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("ississ", $_SESSION['user_id'], $action, $entityType, $entityId, $description, $ip);
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}
