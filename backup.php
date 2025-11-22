<?php
require_once 'config.php';

// Script backup database sang JSON
$conn = getDBConnection();
$result = $conn->query("SELECT * FROM welcome_boards ORDER BY created_at DESC");

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Tạo thư mục backup nếu chưa có
if (!file_exists('backups')) {
    mkdir('backups', 0755, true);
}

// Tạo file backup với timestamp
$backup_file = 'backups/backup_' . date('Y-m-d_H-i-s') . '.json';
file_put_contents($backup_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Cũng backup sang data.json
file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Backup thành công!\n";
echo "📁 File: $backup_file\n";
echo "📊 Số lượng: " . count($data) . " boards\n";
echo "💾 Dung lượng: " . number_format(filesize($backup_file) / 1024, 2) . " KB\n";

// Xóa backup cũ hơn 30 ngày
$files = glob('backups/backup_*.json');
foreach ($files as $file) {
    if (filemtime($file) < time() - (30 * 24 * 60 * 60)) {
        unlink($file);
        echo "🗑️ Đã xóa backup cũ: " . basename($file) . "\n";
    }
}
?>
