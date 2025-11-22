<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt WCB System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .note {
            background: #fff3cd;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        .note strong {
            display: block;
            margin-bottom: 8px;
        }
        .note code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Cài đặt WCB System</h1>
        <p class="subtitle">Cấu hình database cho hệ thống Welcome Board</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db_host = $_POST['db_host'];
            $db_user = $_POST['db_user'];
            $db_pass = $_POST['db_pass'];
            $db_name = $_POST['db_name'];
            
            // Test kết nối
            $conn = new mysqli($db_host, $db_user, $db_pass);
            
            if ($conn->connect_error) {
                echo '<div class="alert error">❌ Kết nối thất bại: ' . $conn->connect_error . '</div>';
            } else {
                // Tạo database nếu chưa có
                $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                if ($conn->query($sql)) {
                    // Ghi file config
                    $config_content = "<?php
// Cấu hình database
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');

// Kết nối database
function getDBConnection() {
    static \$conn = null;
    
    if (\$conn === null) {
        \$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if (\$conn->connect_error) {
            die(\"Kết nối database thất bại: \" . \$conn->connect_error);
        }
        
        \$conn->set_charset(\"utf8mb4\");
    }
    
    return \$conn;
}

// Tạo bảng nếu chưa có
function initDatabase() {
    \$conn = getDBConnection();
    
    \$sql = \"CREATE TABLE IF NOT EXISTS welcome_boards (
        id VARCHAR(50) PRIMARY KEY,
        event_date DATE NOT NULL,
        event_title VARCHAR(255) NOT NULL,
        filename VARCHAR(255) NOT NULL,
        filepath VARCHAR(255) NOT NULL,
        upload_time DATETIME NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'inactive',
        width INT NOT NULL,
        height INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_event_date (event_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\";
    
    if (!\$conn->query(\$sql)) {
        die(\"Lỗi tạo bảng: \" . \$conn->error);
    }
}

// Khởi tạo database khi load config
initDatabase();
?>";
                    
                    if (file_put_contents('config.php', $config_content)) {
                        // Migrate dữ liệu từ JSON nếu có
                        $migrated = 0;
                        if (file_exists('data.json')) {
                            $conn->select_db($db_name);
                            require_once 'config.php';
                            
                            $json_data = json_decode(file_get_contents('data.json'), true) ?: [];
                            $db_conn = getDBConnection();
                            
                            foreach ($json_data as $board) {
                                $stmt = $db_conn->prepare("INSERT INTO welcome_boards (id, event_date, event_title, filename, filepath, upload_time, status, width, height) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmt->bind_param("sssssssii", 
                                    $board['id'],
                                    $board['event_date'],
                                    $board['event_title'],
                                    $board['filename'],
                                    $board['filepath'],
                                    $board['upload_time'],
                                    $board['status'],
                                    $board['width'],
                                    $board['height']
                                );
                                if ($stmt->execute()) {
                                    $migrated++;
                                }
                            }
                        }
                        
                        echo '<div class="alert success">
                            ✅ Cài đặt thành công!<br>
                            📦 Database: ' . $db_name . '<br>
                            📊 Đã migrate: ' . $migrated . ' board<br><br>
                            <a href="index.php" style="color: #155724; font-weight: 600;">→ Vào hệ thống</a>
                        </div>';
                    } else {
                        echo '<div class="alert error">❌ Không thể ghi file config.php. Kiểm tra quyền ghi.</div>';
                    }
                } else {
                    echo '<div class="alert error">❌ Không thể tạo database: ' . $conn->error . '</div>';
                }
            }
            $conn->close();
        }
        ?>

        <div class="note">
            <strong>📝 Lưu ý:</strong>
            Bạn cần tạo database MySQL trước hoặc đảm bảo user có quyền tạo database.<br>
            Ví dụ: <code>CREATE DATABASE wcb;</code>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Database Host</label>
                <input type="text" name="db_host" value="localhost" required>
            </div>

            <div class="form-group">
                <label>Database Username</label>
                <input type="text" name="db_user" required>
            </div>

            <div class="form-group">
                <label>Database Password</label>
                <input type="password" name="db_pass">
            </div>

            <div class="form-group">
                <label>Database Name</label>
                <input type="text" name="db_name" value="wcb" required>
            </div>

            <button type="submit" class="btn">🚀 Cài đặt ngay</button>
        </form>
    </div>
</body>
</html>
