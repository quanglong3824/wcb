<?php
/**
 * Backup API
 * Database and files backup management
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';
require_once '../includes/permissions.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Kiểm tra quyền dựa trên action
if ($action === 'list' && !hasPermission('backup', PERM_VIEW)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền xem danh sách backup']);
    exit;
}

if ($action === 'create' && !hasPermission('backup', PERM_CREATE)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền tạo backup']);
    exit;
}

if ($action === 'delete' && !hasPermission('backup', PERM_DELETE)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền xóa backup']);
    exit;
}

if ($action === 'restore' && !hasPermission('backup', PERM_CREATE)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền khôi phục backup']);
    exit;
}

if ($action === 'download' && !hasPermission('backup', PERM_VIEW)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền tải backup']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'create':
        $type = isset($_GET['type']) ? $_GET['type'] : 'database';
        createBackup($type);
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
    case 'get_stats':
        getBackupStats();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Create backup
 * @param string $type - 'database', 'media', 'wcb', 'full'
 */
function createBackup($type = 'database') {
    $backupDir = dirname(__DIR__) . '/backups';
    
    // Create backup directory if not exists
    if (!is_dir($backupDir)) {
        if (!@mkdir($backupDir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Cannot create backup directory. Please create it manually.']);
            return;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($backupDir)) {
        echo json_encode(['success' => false, 'message' => 'Backup directory is not writable']);
        return;
    }
    
    $timestamp = date('Y-m-d_His');
    
    switch ($type) {
        case 'database':
            createDatabaseBackup($backupDir, $timestamp);
            break;
        case 'media':
            createMediaBackup($backupDir, $timestamp);
            break;
        case 'wcb':
            createWcbBackup($backupDir, $timestamp);
            break;
        case 'full':
            createFullBackup($backupDir, $timestamp);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid backup type']);
    }
}

/**
 * Create database backup
 */
function createDatabaseBackup($backupDir, $timestamp) {
    $filename = "backup_db_{$timestamp}.sql";
    $filepath = $backupDir . '/' . $filename;
    
    try {
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        $output = "";
        
        // Add header
        $output .= "-- Aurora Hotel WCB Database Backup\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Database: " . DB_NAME . "\n";
        $output .= "-- --------------------------------------------------------\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $output .= "SET AUTOCOMMIT = 0;\n";
        $output .= "START TRANSACTION;\n\n";
        
        // Get all tables (only BASE TABLEs, not views)
        $tables = [];
        $result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        
        // Export each table
        foreach ($tables as $table) {
            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Table structure for `{$table}`\n";
            $output .= "-- --------------------------------------------------------\n\n";
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            $createResult = $conn->query("SHOW CREATE TABLE `{$table}`");
            if ($createResult) {
                $createRow = $createResult->fetch_row();
                $output .= $createRow[1] . ";\n\n";
            }
            
            // Table data
            $dataResult = $conn->query("SELECT * FROM `{$table}`");
            if ($dataResult && $dataResult->num_rows > 0) {
                $numFields = $dataResult->field_count;
                $output .= "-- Dumping data for `{$table}`\n\n";
                
                while ($row = $dataResult->fetch_row()) {
                    $output .= "INSERT INTO `{$table}` VALUES(";
                    for ($i = 0; $i < $numFields; $i++) {
                        if (isset($row[$i])) {
                            $row[$i] = addslashes($row[$i]);
                            $row[$i] = str_replace("\n", "\\n", $row[$i]);
                            $output .= '"' . $row[$i] . '"';
                        } else {
                            $output .= 'NULL';
                        }
                        if ($i < ($numFields - 1)) {
                            $output .= ',';
                        }
                    }
                    $output .= ");\n";
                }
                $output .= "\n";
            }
        }
        
        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $output .= "COMMIT;\n";
        
        // Write to file
        file_put_contents($filepath, $output);
        
        // Compress the backup
        $gzFilepath = $filepath . '.gz';
        $fp = gzopen($gzFilepath, 'w9');
        gzwrite($fp, $output);
        gzclose($fp);
        
        // Remove uncompressed file
        unlink($filepath);
        
        $filesize = filesize($gzFilepath);
        
        @logActivity($conn, 'create_backup', 'backup', 0, "Created database backup: {$filename}.gz");
        
        echo json_encode([
            'success' => true,
            'message' => 'Database backup created successfully',
            'filename' => $filename . '.gz',
            'size' => formatBytes($filesize),
            'type' => 'database'
        ]);
        
    } catch (Exception $e) {
        if (isset($filepath) && file_exists($filepath)) {
            @unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to create backup: ' . $e->getMessage()]);
    }
}

/**
 * Create media backup (uploads folder)
 */
function createMediaBackup($backupDir, $timestamp) {
    $uploadsDir = dirname(__DIR__) . '/uploads';
    $filename = "backup_media_{$timestamp}.zip";
    $filepath = $backupDir . '/' . $filename;
    
    try {
        if (!is_dir($uploadsDir)) {
            echo json_encode(['success' => false, 'message' => 'Uploads directory not found']);
            return;
        }
        
        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception('Cannot create zip file');
        }
        
        // Add info file
        $info = "Aurora Hotel WCB Media Backup\n";
        $info .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $info .= "Type: Media Files (uploads)\n";
        $zip->addFromString('_backup_info.txt', $info);
        
        // Add all files from uploads directory
        $fileCount = addFolderToZip($zip, $uploadsDir, 'uploads');
        
        $zip->close();
        
        $filesize = filesize($filepath);
        
        $conn = getDBConnection();
        @logActivity($conn, 'create_backup', 'backup', 0, "Created media backup: {$filename} ({$fileCount} files)");
        
        echo json_encode([
            'success' => true,
            'message' => "Media backup created successfully ({$fileCount} files)",
            'filename' => $filename,
            'size' => formatBytes($filesize),
            'type' => 'media',
            'file_count' => $fileCount
        ]);
        
    } catch (Exception $e) {
        if (isset($filepath) && file_exists($filepath)) {
            @unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to create media backup: ' . $e->getMessage()]);
    }
}

/**
 * Create WCB backup (wcb-content folder)
 */
function createWcbBackup($backupDir, $timestamp) {
    $wcbDir = dirname(__DIR__) . '/wcb-content';
    $filename = "backup_wcb_{$timestamp}.zip";
    $filepath = $backupDir . '/' . $filename;
    
    try {
        if (!is_dir($wcbDir)) {
            echo json_encode(['success' => false, 'message' => 'WCB content directory not found']);
            return;
        }
        
        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception('Cannot create zip file');
        }
        
        // Add info file
        $info = "Aurora Hotel WCB Content Backup\n";
        $info .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $info .= "Type: WCB Content Files\n";
        $zip->addFromString('_backup_info.txt', $info);
        
        // Add all files from wcb-content directory
        $fileCount = addFolderToZip($zip, $wcbDir, 'wcb-content');
        
        $zip->close();
        
        $filesize = filesize($filepath);
        
        $conn = getDBConnection();
        @logActivity($conn, 'create_backup', 'backup', 0, "Created WCB backup: {$filename} ({$fileCount} files)");
        
        echo json_encode([
            'success' => true,
            'message' => "WCB backup created successfully ({$fileCount} files)",
            'filename' => $filename,
            'size' => formatBytes($filesize),
            'type' => 'wcb',
            'file_count' => $fileCount
        ]);
        
    } catch (Exception $e) {
        if (isset($filepath) && file_exists($filepath)) {
            @unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to create WCB backup: ' . $e->getMessage()]);
    }
}

/**
 * Create full backup (database + media + wcb)
 */
function createFullBackup($backupDir, $timestamp) {
    $filename = "backup_full_{$timestamp}.zip";
    $filepath = $backupDir . '/' . $filename;
    
    try {
        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception('Cannot create zip file');
        }
        
        // Add info file
        $info = "Aurora Hotel WCB Full System Backup\n";
        $info .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $info .= "Type: Full Backup (Database + Media + WCB)\n";
        $info .= "Contents:\n";
        $info .= "  - database.sql: Database dump\n";
        $info .= "  - uploads/: Media files\n";
        $info .= "  - wcb-content/: WCB content files\n";
        $zip->addFromString('_backup_info.txt', $info);
        
        $totalFiles = 0;
        
        // 1. Add database dump
        $conn = getDBConnection();
        if ($conn) {
            $dbDump = createDatabaseDump($conn);
            $zip->addFromString('database.sql', $dbDump);
            $totalFiles++;
        }
        
        // 2. Add uploads folder
        $uploadsDir = dirname(__DIR__) . '/uploads';
        if (is_dir($uploadsDir)) {
            $totalFiles += addFolderToZip($zip, $uploadsDir, 'uploads');
        }
        
        // 3. Add wcb-content folder
        $wcbDir = dirname(__DIR__) . '/wcb-content';
        if (is_dir($wcbDir)) {
            $totalFiles += addFolderToZip($zip, $wcbDir, 'wcb-content');
        }
        
        $zip->close();
        
        $filesize = filesize($filepath);
        
        @logActivity($conn, 'create_backup', 'backup', 0, "Created full backup: {$filename} ({$totalFiles} files)");
        
        echo json_encode([
            'success' => true,
            'message' => "Full backup created successfully ({$totalFiles} files)",
            'filename' => $filename,
            'size' => formatBytes($filesize),
            'type' => 'full',
            'file_count' => $totalFiles
        ]);
        
    } catch (Exception $e) {
        if (isset($filepath) && file_exists($filepath)) {
            @unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to create full backup: ' . $e->getMessage()]);
    }
}

/**
 * Create database dump string
 */
function createDatabaseDump($conn) {
    $output = "";
    
    $output .= "-- Aurora Hotel WCB Database Backup\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Database: " . DB_NAME . "\n";
    $output .= "-- --------------------------------------------------------\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $output .= "SET AUTOCOMMIT = 0;\n";
    $output .= "START TRANSACTION;\n\n";
    
    $tables = [];
    $result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        $output .= "-- Table structure for `{$table}`\n";
        $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        $createResult = $conn->query("SHOW CREATE TABLE `{$table}`");
        if ($createResult) {
            $createRow = $createResult->fetch_row();
            $output .= $createRow[1] . ";\n\n";
        }
        
        $dataResult = $conn->query("SELECT * FROM `{$table}`");
        if ($dataResult && $dataResult->num_rows > 0) {
            $numFields = $dataResult->field_count;
            
            while ($row = $dataResult->fetch_row()) {
                $output .= "INSERT INTO `{$table}` VALUES(";
                for ($i = 0; $i < $numFields; $i++) {
                    if (isset($row[$i])) {
                        $row[$i] = addslashes($row[$i]);
                        $row[$i] = str_replace("\n", "\\n", $row[$i]);
                        $output .= '"' . $row[$i] . '"';
                    } else {
                        $output .= 'NULL';
                    }
                    if ($i < ($numFields - 1)) {
                        $output .= ',';
                    }
                }
                $output .= ");\n";
            }
            $output .= "\n";
        }
    }
    
    $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $output .= "COMMIT;\n";
    
    return $output;
}

/**
 * Add folder to zip recursively
 */
function addFolderToZip($zip, $folder, $zipPath) {
    $fileCount = 0;
    
    if (!is_dir($folder)) {
        return 0;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = $zipPath . '/' . substr($filePath, strlen($folder) + 1);
            $zip->addFile($filePath, $relativePath);
            $fileCount++;
        }
    }
    
    return $fileCount;
}

/**
 * Get backup statistics
 */
function getBackupStats() {
    $rootDir = dirname(__DIR__);
    
    $stats = [
        'database' => getDatabaseSize(),
        'uploads' => getFolderSize($rootDir . '/uploads'),
        'wcb_content' => getFolderSize($rootDir . '/wcb-content'),
    ];
    
    $stats['total'] = $stats['database'] + $stats['uploads'] + $stats['wcb_content'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'database' => [
                'size' => $stats['database'],
                'size_formatted' => formatBytes($stats['database'])
            ],
            'uploads' => [
                'size' => $stats['uploads'],
                'size_formatted' => formatBytes($stats['uploads']),
                'file_count' => countFiles($rootDir . '/uploads')
            ],
            'wcb_content' => [
                'size' => $stats['wcb_content'],
                'size_formatted' => formatBytes($stats['wcb_content']),
                'file_count' => countFiles($rootDir . '/wcb-content')
            ],
            'total' => [
                'size' => $stats['total'],
                'size_formatted' => formatBytes($stats['total'])
            ]
        ]
    ]);
}

/**
 * Get database size
 */
function getDatabaseSize() {
    $conn = getDBConnection();
    if (!$conn) return 0;
    
    $result = $conn->query("SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
    if ($result) {
        $row = $result->fetch_assoc();
        return (int)$row['size'];
    }
    return 0;
}

/**
 * Get folder size
 */
function getFolderSize($folder) {
    if (!is_dir($folder)) return 0;
    
    $size = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($files as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    
    return $size;
}

/**
 * Count files in folder
 */
function countFiles($folder) {
    if (!is_dir($folder)) return 0;
    
    $count = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $file) {
        if ($file->isFile()) {
            $count++;
        }
    }
    
    return $count;
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
    
    // Get all backup files
    $patterns = [
        $backupDir . '/backup_db_*.sql.gz',
        $backupDir . '/backup_media_*.zip',
        $backupDir . '/backup_wcb_*.zip',
        $backupDir . '/backup_full_*.zip',
        $backupDir . '/backup_*.sql.gz' // Legacy format
    ];
    
    $allFiles = [];
    foreach ($patterns as $pattern) {
        $files = glob($pattern);
        foreach ($files as $file) {
            $allFiles[$file] = true; // Use as key to avoid duplicates
        }
    }
    
    $backups = [];
    foreach (array_keys($allFiles) as $file) {
        $filename = basename($file);
        
        // Determine backup type
        $type = 'database';
        $typeLabel = 'Database';
        $icon = 'fas fa-database';
        
        if (strpos($filename, 'backup_full_') === 0) {
            $type = 'full';
            $typeLabel = 'Full System';
            $icon = 'fas fa-archive';
        } elseif (strpos($filename, 'backup_media_') === 0) {
            $type = 'media';
            $typeLabel = 'Media Files';
            $icon = 'fas fa-images';
        } elseif (strpos($filename, 'backup_wcb_') === 0) {
            $type = 'wcb';
            $typeLabel = 'WCB Content';
            $icon = 'fas fa-tv';
        }
        
        $backups[] = [
            'filename' => $filename,
            'size' => filesize($file),
            'size_formatted' => formatBytes(filesize($file)),
            'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            'created_at_formatted' => date('d/m/Y H:i', filemtime($file)),
            'type' => $type,
            'type_label' => $typeLabel,
            'icon' => $icon
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
 * Restore from backup using PHP (no mysql command required)
 */
function restoreBackup() {
    $filename = isset($_GET['filename']) ? basename($_GET['filename']) : '';
    $backupDir = dirname(__DIR__) . '/backups';
    $filepath = $backupDir . '/' . $filename;
    
    if (empty($filename) || !file_exists($filepath)) {
        echo json_encode(['success' => false, 'message' => 'Backup file not found']);
        return;
    }
    
    try {
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        // Read and decompress the backup file
        $sql = '';
        $gz = gzopen($filepath, 'rb');
        if (!$gz) {
            throw new Exception('Cannot open backup file');
        }
        
        while (!gzeof($gz)) {
            $sql .= gzread($gz, 4096);
        }
        gzclose($gz);
        
        // Disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(";\n", $sql)));
        
        $errors = [];
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            // Skip certain statements
            if (preg_match('/^(SET|START|COMMIT)/i', $statement)) {
                continue;
            }
            
            if (!$conn->query($statement)) {
                $errors[] = $conn->error;
            }
        }
        
        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        
        if (empty($errors)) {
            // Log activity
            logActivity($conn, 'restore_backup', 'backup', 0, "Restored from backup: {$filename}");
            
            echo json_encode(['success' => true, 'message' => 'Backup restored successfully']);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Restore completed with errors',
                'errors' => array_slice($errors, 0, 5) // Show first 5 errors
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to restore backup: ' . $e->getMessage()
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
