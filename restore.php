<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục dữ liệu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 30px; }
        .backup-list {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }
        .backup-item {
            background: #f8f9fb;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e8eef5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .backup-info h3 {
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        .backup-info p {
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            padding: 10px 24px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover { background: #357abd; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Khôi phục dữ liệu</h1>

        <?php
        require_once 'config.php';

        if (isset($_POST['restore'])) {
            $backup_file = $_POST['backup_file'];
            
            if (file_exists($backup_file)) {
                $data = json_decode(file_get_contents($backup_file), true);
                
                if ($data) {
                    $conn = getDBConnection();
                    
                    // Xóa dữ liệu cũ
                    $conn->query("TRUNCATE TABLE welcome_boards");
                    
                    // Import dữ liệu
                    $imported = 0;
                    foreach ($data as $board) {
                        $stmt = $conn->prepare("INSERT INTO welcome_boards (id, event_date, event_title, filename, filepath, upload_time, status, width, height, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        
                        $created = $board['created_at'] ?? date('Y-m-d H:i:s');
                        $updated = $board['updated_at'] ?? date('Y-m-d H:i:s');
                        
                        $stmt->bind_param("sssssssiiis", 
                            $board['id'],
                            $board['event_date'],
                            $board['event_title'],
                            $board['filename'],
                            $board['filepath'],
                            $board['upload_time'],
                            $board['status'],
                            $board['width'],
                            $board['height'],
                            $created,
                            $updated
                        );
                        
                        if ($stmt->execute()) {
                            $imported++;
                        }
                    }
                    
                    echo '<div class="alert success">✅ Khôi phục thành công ' . $imported . ' boards!</div>';
                } else {
                    echo '<div class="alert error">❌ File backup không hợp lệ!</div>';
                }
            } else {
                echo '<div class="alert error">❌ File backup không tồn tại!</div>';
            }
        }

        // Liệt kê các file backup
        $backups = glob('backups/backup_*.json');
        rsort($backups); // Mới nhất trước
        ?>

        <div class="backup-list">
            <?php if (empty($backups)): ?>
                <p style="text-align: center; color: #6c757d; padding: 40px;">
                    Chưa có file backup nào. Chạy <code>backup.php</code> để tạo backup.
                </p>
            <?php else: ?>
                <?php foreach ($backups as $backup): ?>
                    <?php
                    $filename = basename($backup);
                    $size = filesize($backup);
                    $date = filemtime($backup);
                    $data = json_decode(file_get_contents($backup), true);
                    $count = count($data);
                    ?>
                    <div class="backup-item">
                        <div class="backup-info">
                            <h3>📦 <?php echo $filename; ?></h3>
                            <p>
                                📅 <?php echo date('d/m/Y H:i:s', $date); ?> | 
                                📊 <?php echo $count; ?> boards | 
                                💾 <?php echo number_format($size / 1024, 2); ?> KB
                            </p>
                        </div>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ Khôi phục sẽ XÓA toàn bộ dữ liệu hiện tại. Bạn có chắc?');">
                            <input type="hidden" name="backup_file" value="<?php echo $backup; ?>">
                            <button type="submit" name="restore" class="btn">Khôi phục</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="index.php" class="btn">← Quay lại</a>
    </div>
</body>
</html>
