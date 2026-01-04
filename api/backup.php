<?php
/**
 * Backup API
 * Database and files backup management
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';
require_once '../includes/logger.php';
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
 */
function createBackup($type = 'database')
{
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
function createDatabaseBackup($backupDir, $timestamp)
{
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

        // Get all tables
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

        // Compress
        $gzFilepath = $filepath . '.gz';
        $fp = gzopen($gzFilepath, 'w9');
        gzwrite($fp, $output);
        gzclose($fp);

        unlink($filepath);

        $filesize = filesize($gzFilepath);

        logActivity($conn, 'create_backup', 'backup', 0, "Tạo database backup: {$filename}.gz");

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
 * Create media backup
 */
function createMediaBackup($backupDir, $timestamp)
{
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

        $info = "Aurora Hotel WCB Media Backup\nGenerated: " . date('Y-m-d H:i:s') . "\nType: Media Files (uploads)\n";
        $zip->addFromString('_backup_info.txt', $info);

        $fileCount = addFolderToZip($zip, $uploadsDir, 'uploads');
        $zip->close();

        $filesize = filesize($filepath);
        $conn = getDBConnection();
        logActivity($conn, 'create_backup', 'backup', 0, "Tạo media backup: {$filename} ({$fileCount} files)");

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
 * Create WCB backup
 */
function createWcbBackup($backupDir, $timestamp)
{
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

        $info = "Aurora Hotel WCB Content Backup\nGenerated: " . date('Y-m-d H:i:s') . "\nType: WCB Content Files\n";
        $zip->addFromString('_backup_info.txt', $info);

        $fileCount = addFolderToZip($zip, $wcbDir, 'wcb-content');
        $zip->close();

        $filesize = filesize($filepath);
        $conn = getDBConnection();
        logActivity($conn, 'create_backup', 'backup', 0, "Tạo WCB backup: {$filename} ({$fileCount} files)");

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
 * Create full backup
 */
function createFullBackup($backupDir, $timestamp)
{
    $filename = "backup_full_{$timestamp}.zip";
    $filepath = $backupDir . '/' . $filename;

    try {
        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception('Cannot create zip file');
        }

        $info = "Aurora Hotel WCB Full System Backup\nGenerated: " . date('Y-m-d H:i:s') . "\nType: Full Backup\n";
        $zip->addFromString('_backup_info.txt', $info);

        $totalFiles = 0;
        $conn = getDBConnection();
        if ($conn) {
            $dbDump = createDatabaseDump($conn);
            $zip->addFromString('database.sql', $dbDump);
            $totalFiles++;
        }

        $uploadsDir = dirname(__DIR__) . '/uploads';
        if (is_dir($uploadsDir)) {
            $totalFiles += addFolderToZip($zip, $uploadsDir, 'uploads');
        }

        $wcbDir = dirname(__DIR__) . '/wcb-content';
        if (is_dir($wcbDir)) {
            $totalFiles += addFolderToZip($zip, $wcbDir, 'wcb-content');
        }

        $zip->close();
        $filesize = filesize($filepath);
        logActivity($conn, 'create_backup', 'backup', 0, "Tạo full backup: {$filename} ({$totalFiles} files)");

        echo json_encode([
            'success' => true,
            'message' => "Full backup created successfully",
            'filename' => $filename,
            'size' => formatBytes($filesize),
            'type' => 'full'
        ]);

    } catch (Exception $e) {
        if (isset($filepath) && file_exists($filepath)) {
            @unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to create full backup: ' . $e->getMessage()]);
    }
}

/**
 * Helper: Database dump
 */
function createDatabaseDump($conn)
{
    $output = "-- Aurora Hotel WCB Database Backup\nGenerated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\nSET AUTOCOMMIT = 0;\nSTART TRANSACTION;\n\n";

    $tables = [];
    $result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    while ($row = $result->fetch_row())
        $tables[] = $row[0];

    foreach ($tables as $table) {
        $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $res = $conn->query("SHOW CREATE TABLE `{$table}`");
        if ($res) {
            $row = $res->fetch_row();
            $output .= $row[1] . ";\n\n";
        }

        $dataRes = $conn->query("SELECT * FROM `{$table}`");
        while ($row = $dataRes->fetch_row()) {
            $values = array_map(function ($v) use ($conn) {
                return $v === null ? 'NULL' : '"' . $conn->real_escape_string($v) . '"';
            }, $row);
            $output .= "INSERT INTO `{$table}` VALUES(" . implode(',', $values) . ");\n";
        }
        $output .= "\n";
    }
    $output .= "SET FOREIGN_KEY_CHECKS=1;\nCOMMIT;\n";
    return $output;
}

/**
 * Add folder to zip recursively
 */
function addFolderToZip($zip, $folder, $zipPath)
{
    $fileCount = 0;
    if (!is_dir($folder))
        return 0;

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
function getBackupStats()
{
    $rootDir = dirname(__DIR__);
    $dbSize = getDatabaseSize();
    $uploadsSize = getFolderSize($rootDir . '/uploads');
    $wcbSize = getFolderSize($rootDir . '/wcb-content');

    echo json_encode([
        'success' => true,
        'stats' => [
            'database' => ['size' => $dbSize, 'size_formatted' => formatBytes($dbSize)],
            'uploads' => ['size' => $uploadsSize, 'size_formatted' => formatBytes($uploadsSize)],
            'wcb_content' => ['size' => $wcbSize, 'size_formatted' => formatBytes($wcbSize)],
            'total' => ['size' => $dbSize + $uploadsSize + $wcbSize, 'size_formatted' => formatBytes($dbSize + $uploadsSize + $wcbSize)]
        ]
    ]);
}

function getDatabaseSize()
{
    $conn = getDBConnection();
    if (!$conn)
        return 0;
    $res = $conn->query("SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
    $row = $res->fetch_assoc();
    return (int) $row['size'];
}

function getFolderSize($folder)
{
    if (!is_dir($folder))
        return 0;
    $size = 0;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($files as $file)
        if ($file->isFile())
            $size += $file->getSize();
    return $size;
}

/**
 * List backups
 */
function listBackups()
{
    $backupDir = dirname(__DIR__) . '/backups';
    if (!is_dir($backupDir)) {
        echo json_encode(['success' => true, 'backups' => []]);
        return;
    }

    $files = glob($backupDir . '/*.{gz,zip}', GLOB_BRACE);
    $backups = [];
    foreach ($files as $file) {
        $name = basename($file);
        $type = 'database';
        if (strpos($name, 'full') !== false)
            $type = 'full';
        elseif (strpos($name, 'media') !== false)
            $type = 'media';
        elseif (strpos($name, 'wcb') !== false)
            $type = 'wcb';

        $backups[] = [
            'filename' => $name,
            'size' => filesize($file),
            'size_formatted' => formatBytes(filesize($file)),
            'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            'type' => $type
        ];
    }

    usort($backups, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']); });
    echo json_encode(['success' => true, 'backups' => $backups]);
}

/**
 * Download backup
 */
function downloadBackup()
{
    $filename = basename($_GET['filename'] ?? '');
    $filepath = dirname(__DIR__) . '/backups/' . $filename;

    if (empty($filename) || !file_exists($filepath))
        exit('File not found');

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
}

/**
 * Delete backup
 */
function deleteBackup()
{
    $filename = basename($_GET['filename'] ?? '');
    $filepath = dirname(__DIR__) . '/backups/' . $filename;

    if (file_exists($filepath) && unlink($filepath)) {
        $conn = getDBConnection();
        logActivity($conn, 'delete_backup', 'backup', 0, "Xóa backup: {$filename}");
        echo json_encode(['success' => true, 'message' => 'Xóa thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa']);
    }
}

/**
 * Restore backup
 */
function restoreBackup()
{
    $filename = basename($_GET['filename'] ?? '');
    $filepath = dirname(__DIR__) . '/backups/' . $filename;

    if (!file_exists($filepath)) {
        echo json_encode(['success' => false, 'message' => 'File không tồn tại']);
        return;
    }

    try {
        $conn = getDBConnection();
        $sql = '';
        $gz = gzopen($filepath, 'rb');
        while (!gzeof($gz))
            $sql .= gzread($gz, 4096);
        gzclose($gz);

        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        $statements = array_filter(array_map('trim', explode(";\n", $sql)));
        foreach ($statements as $st) {
            if (!empty($st) && strpos($st, '--') !== 0 && !preg_match('/^(SET|START|COMMIT)/i', $st)) {
                $conn->query($st);
            }
        }
        $conn->query("SET FOREIGN_KEY_CHECKS=1");

        logActivity($conn, 'restore_backup', 'backup', 0, "Khôi phục từ backup: {$filename}");
        echo json_encode(['success' => true, 'message' => 'Khôi phục thành công']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}

function formatBytes($bytes, $precision = 2)
{
    if ($bytes <= 0)
        return '0 B';
    $units = ['B', 'KB', 'MB', 'GB'];
    $pow = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}
