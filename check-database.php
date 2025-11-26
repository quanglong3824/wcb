<?php
/**
 * Database Connection Checker
 * Kiểm tra và xác thực kết nối database
 * Hiển thị thông tin môi trường và trạng thái kết nối
 */

// Load configuration (sẽ tự động cấu hình session)
require_once 'config/php/config.php';

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền truy cập (chỉ admin mới xem được)
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin';
$allowPublicAccess = true; // Đặt false để chỉ admin mới xem được

if (!$allowPublicAccess && !$isAdmin) {
    die('Access Denied! Only administrators can view this page.');
}

// Lấy thông tin kết nối
$dbInfo = checkDatabaseConnection();

// Lấy thông tin server
$serverInfo = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
    'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
    'http_host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
];

// Kiểm tra extensions
$extensions = [
    'mysqli' => extension_loaded('mysqli'),
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'json' => extension_loaded('json'),
    'mbstring' => extension_loaded('mbstring'),
    'gd' => extension_loaded('gd'),
    'curl' => extension_loaded('curl'),
];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Checker - WCB System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border: 3px solid #d4af37;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
            margin-bottom: 20px;
            text-align: center;
        }

        .header h1 {
            color: #d4af37;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            margin-top: 15px;
            font-size: 1.2em;
        }

        .status-success {
            background: #10b981;
            color: white;
        }

        .status-error {
            background: #ef4444;
            color: white;
        }

        .card {
            background: white;
            padding: 25px;
            border: 3px solid #d4af37;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
            margin-bottom: 20px;
        }

        .card h2 {
            color: #d4af37;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4af37;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            background: #faf6eb;
            border-left: 4px solid #d4af37;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .info-value {
            color: #666;
            font-family: 'Courier New', monospace;
            text-align: right;
        }

        .check-icon {
            color: #10b981;
            font-size: 1.2em;
        }

        .cross-icon {
            color: #ef4444;
            font-size: 1.2em;
        }

        .table-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .table-item {
            background: #faf6eb;
            padding: 10px 15px;
            border-left: 3px solid #d4af37;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .environment-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }

        .env-local {
            background: #fef3c7;
            color: #92400e;
        }

        .env-remote {
            background: #dbeafe;
            color: #1e40af;
        }

        .refresh-btn {
            background: #d4af37;
            color: white;
            border: 2px solid #d4af37;
            padding: 12px 30px;
            font-size: 1em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px auto;
            transition: all 0.3s;
            font-weight: 600;
        }

        .refresh-btn:hover {
            background: #b8941f;
            border-color: #b8941f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        .alert-info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-database"></i> Database Connection Checker</h1>
            <p>Welcome Board System - Aurora Hotel</p>
            
            <?php if ($dbInfo['success']): ?>
                <div class="status-badge status-success">
                    <i class="fas fa-check-circle"></i> Kết nối thành công
                </div>
            <?php else: ?>
                <div class="status-badge status-error">
                    <i class="fas fa-times-circle"></i> Kết nối thất bại
                </div>
            <?php endif; ?>
        </div>

        <!-- Environment Info -->
        <div class="card">
            <h2>
                <i class="fas fa-server"></i> Thông tin môi trường
            </h2>
            
            <div class="alert alert-info">
                <strong>Môi trường hiện tại:</strong> 
                <span class="environment-badge <?php echo $dbInfo['environment'] === 'LOCAL' ? 'env-local' : 'env-remote'; ?>">
                    <?php echo $dbInfo['environment']; ?>
                </span>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Database Host:</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['host']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Database Name:</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['database']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Database User:</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['user']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Charset:</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['charset']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Server Info:</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['server_info']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Tables:</span>
                    <span class="info-value"><?php echo count($dbInfo['tables']); ?> tables</span>
                </div>
            </div>
        </div>

        <!-- Connection Status -->
        <div class="card">
            <h2>
                <i class="fas fa-plug"></i> Trạng thái kết nối
            </h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">MySQLi Extension:</span>
                    <span class="info-value">
                        <?php if ($dbInfo['mysqli']): ?>
                            <i class="fas fa-check-circle check-icon"></i> Connected
                        <?php else: ?>
                            <i class="fas fa-times-circle cross-icon"></i> Not Connected
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">PDO Extension:</span>
                    <span class="info-value">
                        <?php if ($dbInfo['pdo']): ?>
                            <i class="fas fa-check-circle check-icon"></i> Connected
                        <?php else: ?>
                            <i class="fas fa-times-circle cross-icon"></i> Not Connected
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Database Tables -->
        <?php if (!empty($dbInfo['tables'])): ?>
        <div class="card">
            <h2>
                <i class="fas fa-table"></i> Danh sách bảng trong database
            </h2>
            
            <div class="table-list">
                <?php foreach ($dbInfo['tables'] as $table): ?>
                    <div class="table-item">
                        <i class="fas fa-table"></i> <?php echo htmlspecialchars($table); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- PHP Extensions -->
        <div class="card">
            <h2>
                <i class="fab fa-php"></i> PHP Extensions
            </h2>
            
            <div class="info-grid">
                <?php foreach ($extensions as $ext => $loaded): ?>
                <div class="info-item">
                    <span class="info-label"><?php echo strtoupper($ext); ?>:</span>
                    <span class="info-value">
                        <?php if ($loaded): ?>
                            <i class="fas fa-check-circle check-icon"></i> Loaded
                        <?php else: ?>
                            <i class="fas fa-times-circle cross-icon"></i> Not Loaded
                        <?php endif; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Server Info -->
        <div class="card">
            <h2>
                <i class="fas fa-info-circle"></i> Thông tin Server
            </h2>
            
            <div class="info-grid">
                <?php foreach ($serverInfo as $key => $value): ?>
                <div class="info-item">
                    <span class="info-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</span>
                    <span class="info-value"><?php echo htmlspecialchars($value); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Configuration Guide -->
        <?php if (!$dbInfo['success']): ?>
        <div class="card">
            <h2>
                <i class="fas fa-wrench"></i> Hướng dẫn cấu hình
            </h2>
            
            <div class="alert alert-warning">
                <strong>Lỗi kết nối!</strong> Vui lòng kiểm tra cấu hình database trong file 
                <code>config/php/database.php</code>
            </div>

            <p style="margin-top: 15px; line-height: 1.8;">
                <strong>Các bước khắc phục:</strong><br>
                1. Kiểm tra thông tin đăng nhập database (host, user, password)<br>
                2. Đảm bảo database <code><?php echo htmlspecialchars($dbInfo['database']); ?></code> đã được tạo<br>
                3. Import file <code>database.sql</code> vào database<br>
                4. Kiểm tra MySQL service đang chạy<br>
                5. Kiểm tra firewall và port 3306
            </p>
        </div>
        <?php endif; ?>

        <!-- Refresh Button -->
        <button class="refresh-btn" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i> Làm mới
        </button>

        <!-- Back to Home -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" style="color: white; text-decoration: none; font-size: 1.1em;">
                <i class="fas fa-home"></i> Quay lại trang chủ
            </a>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
