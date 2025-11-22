#!/usr/bin/env php
<?php
/**
 * Cron job tự động backup
 * Chạy mỗi ngày lúc 2h sáng
 * 
 * Cấu hình cron:
 * 0 2 * * * /usr/bin/php /path/to/wcb/cron_backup.php >> /path/to/wcb/logs/backup.log 2>&1
 */

require_once __DIR__ . '/config.php';

echo "[" . date('Y-m-d H:i:s') . "] Bắt đầu backup...\n";

try {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM welcome_boards ORDER BY created_at DESC");
    
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    // Tạo thư mục backup nếu chưa có
    if (!file_exists(__DIR__ . '/backups')) {
        mkdir(__DIR__ . '/backups', 0755, true);
    }
    
    // Tạo file backup với timestamp
    $backup_file = __DIR__ . '/backups/backup_' . date('Y-m-d_H-i-s') . '.json';
    file_put_contents($backup_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Backup sang data.json
    file_put_contents(__DIR__ . '/data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "[" . date('Y-m-d H:i:s') . "] ✅ Backup thành công!\n";
    echo "📁 File: " . basename($backup_file) . "\n";
    echo "📊 Số lượng: " . count($data) . " boards\n";
    echo "💾 Dung lượng: " . number_format(filesize($backup_file) / 1024, 2) . " KB\n";
    
    // Xóa backup cũ hơn 30 ngày
    $files = glob(__DIR__ . '/backups/backup_*.json');
    $deleted = 0;
    foreach ($files as $file) {
        if (filemtime($file) < time() - (30 * 24 * 60 * 60)) {
            unlink($file);
            $deleted++;
        }
    }
    
    if ($deleted > 0) {
        echo "🗑️ Đã xóa $deleted backup cũ\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Hoàn tất!\n\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ❌ Lỗi: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>
