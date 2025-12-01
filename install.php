<?php
/**
 * Installation Script
 * Welcome Board System - Aurora Hotel Plaza
 * 
 * Chạy file này lần đầu để tạo tài khoản admin
 * Database: auroraho_wcb
 */

session_start();

require_once 'config/php/config.php';

// Prevent running if already installed
$lockFile = __DIR__ . '/.installed';

// If lock file exists, redirect immediately
if (file_exists($lockFile)) {
    header('Location: auth/login.php');
    exit;
}

// Check database and admin status
$dbCheckResult = checkDatabaseConnection();
$dbStatus = [
    'connected' => $dbCheckResult['success'] ?? false,
    'environment' => $dbCheckResult['environment'] ?? 'unknown',
    'host' => $dbCheckResult['host'] ?? 'unknown',
    'database' => $dbCheckResult['database'] ?? 'unknown',
    'message' => $dbCheckResult['message'] ?? '',
    'tables_count' => count($dbCheckResult['tables'] ?? []),
    'has_users_table' => in_array('users', $dbCheckResult['tables'] ?? [])
];

// Check if admin exists
$adminExists = false;
if ($dbStatus['connected'] && $dbStatus['has_users_table']) {
    $conn = getDBConnection();
    if ($conn) {
        $result = $conn->query("SELECT id FROM users WHERE username = 'admin' OR role = 'super_admin' LIMIT 1");
        $adminExists = ($result && $result->num_rows > 0);
        $conn->close();
    }
}

// Auto-redirect if everything is OK (DB connected, users table exists, admin exists)
if ($dbStatus['connected'] && $dbStatus['has_users_table'] && $adminExists) {
    // Try to create lock file
    @file_put_contents($lockFile, date('Y-m-d H:i:s'));
    
    // Redirect to login (don't check lock file again to avoid loop)
    header('Location: auth/login.php');
    exit;
}

$errors = [];
$success = [];

// Process installation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    // Clear session for fresh start
    $_SESSION = [];
    session_regenerate_id(true);
    
    $conn = getDBConnection();
    
    if (!$conn) {
        $errors[] = 'Không thể kết nối database. Vui lòng kiểm tra cấu hình trong config/php/database.php';
    } else {
        // Check if users table exists
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        
        if ($result->num_rows === 0) {
            $errors[] = 'Bảng users chưa tồn tại. Vui lòng import file database.sql trước khi chạy cài đặt.';
        } else {
            // Check if admin user exists
            $result = $conn->query("SELECT id FROM users WHERE username = 'admin'");
            
            if ($result->num_rows === 0) {
                // Create admin user
                $username = 'admin';
                $password = password_hash('admin123', PASSWORD_DEFAULT);
                $fullName = 'Administrator';
                $email = 'admin@quanglonghotel.com';
                
                $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role, status, created_at) VALUES (?, ?, ?, ?, 'super_admin', 'active', NOW())");
                $stmt->bind_param("ssss", $username, $password, $fullName, $email);
                
                if ($stmt->execute()) {
                    $success[] = 'Tạo tài khoản admin thành công!';
                    
                    // Mark as installed
                    file_put_contents($lockFile, date('Y-m-d H:i:s'));
                    
                    // Redirect to login after 2 seconds
                    header('refresh:2;url=auth/login.php');
                } else {
                    $errors[] = "Lỗi tạo admin: {$stmt->error}";
                }
                
                $stmt->close();
            } else {
                // Admin exists, mark as installed and redirect
                file_put_contents($lockFile, date('Y-m-d H:i:s'));
                header('Location: auth/login.php');
                exit;
            }
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - Welcome Board System</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .install-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        
        .install-header {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .install-header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .install-header p {
            opacity: 0.9;
        }
        
        .install-body {
            padding: 40px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #d4af37;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #d4af37;
            margin-bottom: 15px;
        }
        
        .info-box ul {
            list-style: none;
            padding: 0;
        }
        
        .info-box li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-box li i {
            color: #10b981;
            width: 20px;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .credentials {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .credentials h4 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .credentials p {
            margin: 8px 0;
            font-family: 'Courier New', monospace;
        }
        
        .credentials strong {
            color: #d4af37;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .loading i {
            font-size: 3em;
            color: #d4af37;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .db-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .db-info p {
            margin: 5px 0;
            font-size: 0.95em;
        }
        
        .db-info code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        
        .db-status-detail {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .db-status-detail.error {
            background: #fff5f5;
            border-color: #feb2b2;
        }
        
        .db-status-detail p {
            margin: 8px 0;
            font-size: 0.95em;
        }
        
        .db-status-detail ul {
            margin: 10px 0 0 20px;
            font-size: 0.9em;
        }
        
        .db-status-detail ul li {
            margin: 5px 0;
        }
        
        .db-status-detail code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #d4af37;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="fas fa-cog"></i> Cài đặt hệ thống</h1>
            <p>Welcome Board System - Aurora Hotel Plaza</p>
        </div>
        
        <div class="install-body">
            <!-- Database Connection Status -->
            <?php if ($dbStatus): ?>
                <?php if ($dbStatus['connected']): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>Kết nối database thành công!</span>
                    </div>
                    <div class="db-status-detail">
                        <p><i class="fas fa-server"></i> <strong>Môi trường:</strong> <?php echo htmlspecialchars($dbStatus['environment']); ?></p>
                        <p><i class="fas fa-network-wired"></i> <strong>Host:</strong> <?php echo htmlspecialchars($dbStatus['host']); ?></p>
                        <p><i class="fas fa-database"></i> <strong>Database:</strong> <?php echo htmlspecialchars($dbStatus['database']); ?></p>
                        <p><i class="fas fa-table"></i> <strong>Số bảng:</strong> <?php echo $dbStatus['tables_count']; ?></p>
                        <p><i class="fas fa-users"></i> <strong>Bảng users:</strong> 
                            <?php if ($dbStatus['has_users_table']): ?>
                                <span style="color: #10b981;">✓ Đã tồn tại</span>
                            <?php else: ?>
                                <span style="color: #dc3545;">✗ Chưa tồn tại</span>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        <i class="fas fa-times-circle"></i>
                        <span>Không thể kết nối database!</span>
                    </div>
                    <div class="db-status-detail error">
                        <p><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($dbStatus['message']); ?></p>
                        <p><strong>Kiểm tra:</strong></p>
                        <ul>
                            <li>Database <code><?php echo htmlspecialchars($dbStatus['database']); ?></code> đã được tạo chưa?</li>
                            <li>Thông tin kết nối trong <code>config/php/database.php</code> có đúng không?</li>
                            <li>MySQL/MariaDB service có đang chạy không?</li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <?php foreach ($success as $msg): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($msg); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p>Đang chuyển hướng đến trang chủ...</p>
                </div>
            <?php else: ?>
                <div class="db-info">
                    <p><i class="fas fa-database"></i> <strong>Database:</strong> <code>auroraho_wcb</code></p>
                    <p><i class="fas fa-info-circle"></i> Đảm bảo bạn đã import file <code>database.sql</code> vào database trước khi cài đặt</p>
                </div>
                
                <div class="info-box">
                    <h3><i class="fas fa-info-circle"></i> Cài đặt sẽ thực hiện:</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Kiểm tra kết nối database</li>
                        <li><i class="fas fa-check"></i> Kiểm tra bảng users đã tồn tại</li>
                        <li><i class="fas fa-check"></i> Tạo tài khoản admin mặc định</li>
                        <li><i class="fas fa-check"></i> Tự động đăng nhập vào hệ thống</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Lưu ý: Cài đặt này chỉ tạo tài khoản admin. Dữ liệu TV, media, schedules phải được import từ file database.sql</span>
                </div>
                
                <form method="POST">
                    <button type="submit" name="install" class="btn" <?php echo (!$dbStatus['connected'] || !$dbStatus['has_users_table']) ? 'disabled' : ''; ?>>
                        <i class="fas fa-user-plus"></i>
                        Tạo tài khoản Admin
                    </button>
                </form>
                
                <?php if (!$dbStatus['connected']): ?>
                    <div class="alert alert-error" style="margin-top: 15px;">
                        <i class="fas fa-ban"></i>
                        <span>Không thể cài đặt: Chưa kết nối được database</span>
                    </div>
                <?php elseif (!$dbStatus['has_users_table']): ?>
                    <div class="alert alert-error" style="margin-top: 15px;">
                        <i class="fas fa-ban"></i>
                        <span>Không thể cài đặt: Vui lòng import file database.sql trước</span>
                    </div>
                <?php endif; ?>
                
                <div class="credentials">
                    <h4><i class="fas fa-key"></i> Thông tin đăng nhập mặc định:</h4>
                    <p><strong>Username:</strong> admin</p>
                    <p><strong>Password:</strong> admin123</p>
                    <p style="margin-top: 10px; font-size: 0.9em; color: #856404;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Vui lòng đổi mật khẩu sau khi đăng nhập!
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
