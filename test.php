<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test WCB System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .test-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .test-section h2 {
            color: #1a1a1a;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        .test-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-item.pass {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        .test-item.fail {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .test-item.warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
        .badge.pass { background: #28a745; color: white; }
        .badge.fail { background: #dc3545; color: white; }
        .badge.warning { background: #ffc107; color: #212529; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 13px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4a90e2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-right: 10px;
        }
        .btn:hover { background: #357abd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test WCB System</h1>

        <?php
        $tests = [];
        $pass_count = 0;
        $fail_count = 0;
        $warning_count = 0;

        // Test 1: PHP Version
        $php_version = PHP_VERSION;
        if (version_compare($php_version, '7.0.0', '>=')) {
            $tests[] = ['name' => 'PHP Version', 'status' => 'pass', 'message' => "PHP $php_version"];
            $pass_count++;
        } else {
            $tests[] = ['name' => 'PHP Version', 'status' => 'fail', 'message' => "PHP $php_version (cần >= 7.0)"];
            $fail_count++;
        }

        // Test 2: MySQLi Extension
        if (extension_loaded('mysqli')) {
            $tests[] = ['name' => 'MySQLi Extension', 'status' => 'pass', 'message' => 'Đã cài đặt'];
            $pass_count++;
        } else {
            $tests[] = ['name' => 'MySQLi Extension', 'status' => 'fail', 'message' => 'Chưa cài đặt'];
            $fail_count++;
        }

        // Test 3: Config file
        if (file_exists('config.php')) {
            $tests[] = ['name' => 'Config File', 'status' => 'pass', 'message' => 'config.php tồn tại'];
            $pass_count++;
            
            // Test 4: Database connection
            try {
                require_once 'config.php';
                $conn = getDBConnection();
                $tests[] = ['name' => 'Database Connection', 'status' => 'pass', 'message' => 'Kết nối thành công'];
                $pass_count++;
                
                // Test 5: Table exists
                $result = $conn->query("SHOW TABLES LIKE 'welcome_boards'");
                if ($result && $result->num_rows > 0) {
                    $tests[] = ['name' => 'Database Table', 'status' => 'pass', 'message' => 'Bảng welcome_boards tồn tại'];
                    $pass_count++;
                    
                    // Test 6: Count records
                    $result = $conn->query("SELECT COUNT(*) as count FROM welcome_boards");
                    $row = $result->fetch_assoc();
                    $tests[] = ['name' => 'Database Records', 'status' => 'pass', 'message' => $row['count'] . ' boards'];
                    $pass_count++;
                } else {
                    $tests[] = ['name' => 'Database Table', 'status' => 'fail', 'message' => 'Bảng welcome_boards không tồn tại'];
                    $fail_count++;
                }
            } catch (Exception $e) {
                $tests[] = ['name' => 'Database Connection', 'status' => 'fail', 'message' => $e->getMessage()];
                $fail_count++;
            }
        } else {
            $tests[] = ['name' => 'Config File', 'status' => 'warning', 'message' => 'Chưa cài đặt - chạy install.php'];
            $warning_count++;
        }

        // Test 7: Uploads directory
        if (is_dir('uploads')) {
            if (is_writable('uploads')) {
                $tests[] = ['name' => 'Uploads Directory', 'status' => 'pass', 'message' => 'Có quyền ghi'];
                $pass_count++;
            } else {
                $tests[] = ['name' => 'Uploads Directory', 'status' => 'fail', 'message' => 'Không có quyền ghi'];
                $fail_count++;
            }
        } else {
            $tests[] = ['name' => 'Uploads Directory', 'status' => 'warning', 'message' => 'Chưa tạo thư mục'];
            $warning_count++;
        }

        // Test 8: Backups directory
        if (is_dir('backups')) {
            if (is_writable('backups')) {
                $tests[] = ['name' => 'Backups Directory', 'status' => 'pass', 'message' => 'Có quyền ghi'];
                $pass_count++;
            } else {
                $tests[] = ['name' => 'Backups Directory', 'status' => 'fail', 'message' => 'Không có quyền ghi'];
                $fail_count++;
            }
        } else {
            $tests[] = ['name' => 'Backups Directory', 'status' => 'warning', 'message' => 'Chưa tạo thư mục'];
            $warning_count++;
        }

        // Test 9: Upload limits
        $upload_max = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');
        if (intval($upload_max) >= 10 && intval($post_max) >= 10) {
            $tests[] = ['name' => 'Upload Limits', 'status' => 'pass', 'message' => "upload: $upload_max, post: $post_max"];
            $pass_count++;
        } else {
            $tests[] = ['name' => 'Upload Limits', 'status' => 'warning', 'message' => "upload: $upload_max, post: $post_max (khuyến nghị >= 10M)"];
            $warning_count++;
        }

        // Test 10: GD Library (for image processing)
        if (extension_loaded('gd')) {
            $tests[] = ['name' => 'GD Library', 'status' => 'pass', 'message' => 'Đã cài đặt'];
            $pass_count++;
        } else {
            $tests[] = ['name' => 'GD Library', 'status' => 'warning', 'message' => 'Chưa cài đặt (tùy chọn)'];
            $warning_count++;
        }

        // Test 11: .htaccess
        if (file_exists('.htaccess')) {
            $tests[] = ['name' => '.htaccess File', 'status' => 'pass', 'message' => 'Đã cấu hình'];
            $pass_count++;
        } else {
            $tests[] = ['name' => '.htaccess File', 'status' => 'warning', 'message' => 'Chưa có (khuyến nghị)'];
            $warning_count++;
        }

        // Test 12: API endpoint
        if (file_exists('api.php')) {
            $tests[] = ['name' => 'API Endpoint', 'status' => 'pass', 'message' => 'api.php tồn tại'];
            $pass_count++;
        } else {
            $tests[] = ['name' => 'API Endpoint', 'status' => 'fail', 'message' => 'api.php không tồn tại'];
            $fail_count++;
        }
        ?>

        <!-- Summary -->
        <div class="test-section">
            <h2>📊 Tổng quan</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 20px; background: #d4edda; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #28a745;"><?php echo $pass_count; ?></div>
                    <div style="color: #155724;">Passed</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #f8d7da; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #dc3545;"><?php echo $fail_count; ?></div>
                    <div style="color: #721c24;">Failed</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #ffc107;"><?php echo $warning_count; ?></div>
                    <div style="color: #856404;">Warnings</div>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="test-section">
            <h2>🧪 Kết quả kiểm tra</h2>
            <?php foreach ($tests as $test): ?>
                <div class="test-item <?php echo $test['status']; ?>">
                    <div>
                        <strong><?php echo $test['name']; ?></strong><br>
                        <small><?php echo $test['message']; ?></small>
                    </div>
                    <span class="badge <?php echo $test['status']; ?>">
                        <?php 
                        echo $test['status'] === 'pass' ? '✓ PASS' : 
                             ($test['status'] === 'fail' ? '✗ FAIL' : '⚠ WARNING'); 
                        ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- System Info -->
        <div class="test-section">
            <h2>💻 Thông tin hệ thống</h2>
            <pre><?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post Max Size: " . ini_get('post_max_size') . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
            ?></pre>
        </div>

        <!-- Actions -->
        <div class="test-section">
            <h2>🔧 Hành động</h2>
            <?php if ($fail_count > 0 || $warning_count > 0): ?>
                <p style="margin-bottom: 15px;">Hệ thống cần được cấu hình thêm:</p>
                <?php if (!file_exists('config.php')): ?>
                    <a href="install.php" class="btn">🚀 Chạy cài đặt</a>
                <?php endif; ?>
                <?php if (!is_dir('uploads')): ?>
                    <a href="?action=create_uploads" class="btn">📁 Tạo thư mục uploads</a>
                <?php endif; ?>
                <?php if (!is_dir('backups')): ?>
                    <a href="?action=create_backups" class="btn">📁 Tạo thư mục backups</a>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: #28a745; font-weight: 600;">✅ Hệ thống đã sẵn sàng!</p>
                <a href="index.php" class="btn">→ Vào hệ thống</a>
                <a href="display.php" class="btn">🖥️ Màn hình chiếu</a>
                <a href="health_check.php" class="btn">🏥 Health Check</a>
            <?php endif; ?>
        </div>

        <?php
        // Handle actions
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'create_uploads':
                    if (!is_dir('uploads')) {
                        mkdir('uploads', 0755, true);
                        echo '<script>alert("Đã tạo thư mục uploads!"); location.href="test.php";</script>';
                    }
                    break;
                case 'create_backups':
                    if (!is_dir('backups')) {
                        mkdir('backups', 0755, true);
                        echo '<script>alert("Đã tạo thư mục backups!"); location.href="test.php";</script>';
                    }
                    break;
            }
        }
        ?>
    </div>
</body>
</html>
